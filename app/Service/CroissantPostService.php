<?php

namespace CroissantApi\Service;

use WpTapestryPlugin\Service\PostInterface;
use CroissantApi\Transformer\TransformerFactory;
use CroissantApi\PostTypeDecorator\PostTypeDecoratorFactory;
use CroissantApi\Service\SeoSchemaGenerator;
use CroissantApi\Util\Image;
use CroissantApi\Util\Post;

class CroissantPostService implements PostInterface {

	public  $wp_query;
	private $image_helper;
	private $post_helper;
	private $seo_schema_generator;

	public function __construct(
		Image $image_helper,
		Post $post_helper,
		SeoSchemaGenerator $seo_schema_generator
	) {
		$this->image_helper         = $image_helper;
		$this->post_helper          = $post_helper;
		$this->seo_schema_generator = $seo_schema_generator;
	}

	public function set_wp_query( $wp_query ) {
		$this->wp_query = $wp_query;
	}

	public function get_single_post( array $params ) {
		if ( ! isset( $params['p'] ) ) {
			throw new \Exception( '[' . __METHOD__ . '] param `p` with post_id is required' );
		}

		$posts = $this->wp_query->query( $params );
		if ( empty( $posts ) ) {
			return [];
		}

		$parent    = null;
		$post_type = $posts[0]->post_type;
		$author_id = $posts[0]->post_author;
		$post_id   = $posts[0]->ID;

		if ( 'revision' === $post_type ) {
			$parent    = get_post( $posts[0]->post_parent );
			$post_type = $parent->post_type;
			$author_id = $parent->post_author;
			$post_id   = $parent->ID;
		}

		$date         = new \DateTime( $posts[0]->post_date );
		$date_gmt     = new \DateTime( $posts[0]->post_date_gmt );
		$modified     = new \DateTime( $posts[0]->post_modified );
		$modified_gmt = new \DateTime( $posts[0]->post_modified_gmt );

		$acf = $this->post_helper->get_acf_data( $posts[0]->ID, 'complete', $parent );

		if ( $this->post_helper->show_placeholder( $post_id ) ) {
			$this->image_helper->placeholder = true;
		}

		$acf['hero_images'] = $this->post_helper->expand_hero_image( $acf['hero_images'] ?? [] );

		$acf = $this->post_helper->set_video_header_metadata( $acf, $post_id );

		unset( $acf['hero_looping_video'] );
		unset( $acf['hero_looping_video_placeholder'] );
		unset( $acf['hero_mobile_looping_video'] );
		unset( $acf['hero_mobile_looping_video_placeholder'] );
		unset( $acf['hero_looping_video_width'] );

		if ( ! empty( $acf['related_manual_posts'] ) ) {
			$acf['related_manual_posts'] = $this->get_post_list_simple_details( $acf['related_manual_posts'] );
		}

		if ( ! empty($acf['email_vertical'] ) ) {
			$acf['email_vertical'] = $this->post_helper->get_vertical_details( $acf['email_vertical'] );
		}

		if ( isset( $acf['widgets'] ) ) {
			$acf['widgets'] = $this->apply_widget_transformation( $acf['widgets'] ) ?? [];
		}

		$acf['canonical_url'] = $acf['canonical_url'] ? ( get_permalink(  $post_id ) ) : null;

		$acf['tagbot_data'] = $this->post_helper->get_tagbot_data( $post_id );

		if ( ! isset( $acf['disable_ads'] ) ) {
			$acf['disable_ads'] = false;
		}

		$next_article = $this->post_helper->set_next_article( $post_id, $acf );

		if ( get_the_terms( $post_id, 'category' ) && $next_article ) {
			$next_article = $this->post_helper->get_post_object_simple_details( get_post( $next_article ) );
			unset( $acf['next_article_manual'] );
		}

		$authors = [
			$this->post_helper->get_author_info( $author_id ),
		];

		if ( isset( $acf['co_author']['ID'] ) ) {
			$authors[] = $this->post_helper->get_author_info( $acf['co_author']['ID'] );
		}

		$return = [
			'id'           => $post_id,
			'date'         => $date->format( 'Y-m-d\TH:i:s' ),
			'date_gmt'     => $date_gmt->format( 'Y-m-d\TH:i:s' ),
			'modified'     => $modified->format( 'Y-m-d\TH:i:s' ),
			'modified_gmt' => $modified_gmt->format( 'Y-m-d\TH:i:s' ),
			'canonical_url' => get_permalink( $post_id ),
			'link'         => get_permalink( $post_id ),
			'type'         => $post_type,
			'title'        => [
				'rendered' => $posts[0]->post_title,
			],
			'slug'         => sanitize_title( $posts[0]->post_title ),
			'acf'          => $this->apply_single_post_decoration( $acf, $post_type ),
			'next_article' => $next_article,
			'time_to_read' => (int) $this->post_helper->get_time_to_read( $post_id ),
			'word_count' => (int) $this->post_helper->word_count( $post_id ),
			'_embedded'    => [
				'author' => $authors,
			],
		];

		if ( ! empty( $return['acf']['series'] ) ) {
			$fields                         = get_fields( $return['acf']['series'] );
			$fields['series_nav_label']     = $this->image_helper->get_attachment_metadata( $fields['series_nav_label'] );
			$fields['series_badge']         = $this->image_helper->get_attachment_metadata( $fields['series_badge'] );
			$fields['series_banner']        = $this->image_helper->get_attachment_metadata( $fields['series_banner'] );
			$fields['series_mobile_banner'] = $this->image_helper->get_attachment_metadata( $fields['series_mobile_banner'] );
			$return['acf']['series']        = array_merge( (array) $return['acf']['series'], $fields );
		}

		if ( ! empty( $return['acf']['premium'] ) ) {

			$cms_subscription_ids = [];
			foreach ( $return['acf']['premium'] as $package ) {

				$cms_subscription_ids[] = $package->term_id;
				if ( $package->parent != 0 ) {
					$cms_subscription_ids[] = $package->parent;
				}
				$cms_subscription_ids = array_merge( $cms_subscription_ids, get_term_children( $package->term_id, 'premium' ) );
			}
			$cms_subscription_ids = array_unique( $cms_subscription_ids );

			$alexandria_subscription_ids = [];
			foreach ( $cms_subscription_ids as $sub_id ) {
				$alexandria_subscription_ids[] = get_term_meta( $sub_id, 'telemetry_subscription_id', true );
			}

			$return['acf']['package_ids'] = $alexandria_subscription_ids;
			unset( $return['acf']['premium'] );
		}

		$return['acf']['package_ids'] = $return['acf']['package_ids'] ?? '';
		$return['acf']['seo_schema']  = json_encode($this->seo_schema_generator->generate( $return ), true, JSON_UNESCAPED_SLASHES);
		$return['acf']['seo_schema'] = stripslashes($return['acf']['seo_schema']);

		if($acf['sponsored']) {
			unset( $return['time_to_read'] );
		}

		return $return;
	}

	public function get_post_list( array $params ) {
		if ( empty( $params ) ) {
			throw new \Exception( '[' . __METHOD__ . '] $params is empty' );
		}
		return $this->get_post_list_simple_details( $this->wp_query->query( $params ) );
	}

	public function get_post_count() {
		return (int) $this->wp_query->found_posts ?? 0;
	}

	private function get_post_list_simple_details( $posts ) {
		$post_list = [];

		$factory = PostTypeDecoratorFactory::get_instance();
		foreach ( $posts as $post ) {
			$class       = $factory->create( $post->post_type );
			$fields      = $this->post_helper->get_post_object_simple_details( $post );
			$post_list[] = $class ? $class->decorate_list( $fields ) : $fields;
		}

		return $post_list;
	}

	private function apply_single_post_decoration( $acf, $post_type ) {

		$factory = PostTypeDecoratorFactory::get_instance();
		$class   = $factory->create( $post_type );
		return $class ? $class->decorate_single( $acf ) : $acf;
	}

	private function apply_widget_transformation( $widgets ) {
		if ( empty( $widgets ) ) {
			return [];
		}

		$return_widgets = [];
		$factory        = TransformerFactory::get_instance();

		foreach ( $widgets as $widget ) {
			$class            = $factory->create( $widget['acf_fc_layout'], $this );
			$return_widgets[] = $class ? $class->apply_transformation( $widget ) : $widget;
		}

		return $return_widgets;
	}
}
