<?php
declare(strict_types=1);

namespace CroissantApi\Util;

class Post implements Util {

	static $PLACEHOLDER_DATE;
	private $jwplayer;
	private $wp_query;

	private $image_helper;

	public function __construct() {
		$this->image_helper     = ( Factory::get_instance() )->create( 'Image' );
		self::$PLACEHOLDER_DATE = getenv( 'PLACEHOLDER_DATE' );
	}

	public function set_jwplayer( $jwplayer ) {
		$this->jwplayer = $jwplayer;
	}

	public function set_wp_query( $wp_query ) {
		$this->wp_query = $wp_query;
	}

	public function get_post_object_simple_details( \WP_Post $post ) {

		$authors = [
			$this->get_author_info( $post->post_author ),
		];

		$co_author = get_field( 'co_author', $post->ID );
		if ( isset( $co_author['ID'] ) ) {
			$authors[] = $this->get_author_info( $co_author['ID'] );
		}

		$date     = new \DateTime( $post->post_date );
		$date_gmt = new \DateTime( $post->post_date_gmt );
		$date_modified = new \DateTime( $post->post_modified );

		$link = get_permalink( $post->ID );

		if ( 'tile' === $post->post_type ) {
			$link = get_field( 'url', $post->ID );
		}

		return [
			'id'        => $post->ID,
			'date'      => $date->format( 'Y-m-d\TH:i:s' ),
			'date_modified' => $date_modified->format( 'Y-m-d\TH:i:s' ),
			'date_gmt'  => $date_gmt->format( 'Y-m-d\TH:i:s' ),
			'link'      => $link,
			'title'     => [
				'rendered' => $post->post_title,
			],
			'acf'       => $this->filter_category(
				$this->get_acf_data( $post->ID, 'simplified' )
			),
			'word_count' => $this->word_count($post->ID),
			'_embedded' => [
				'author' => $authors,
			],
			'sticky'    => in_array( $post->ID, get_option( 'sticky_posts' ), true ),
		];
	}

	public function filter_category( $acf_data ) {
		if ( ! empty( $acf_data['category']->parent ) ) {
			$acf_data['category']->parent = get_term( $acf_data['category']->parent );
		}

		return $acf_data;
	}

	public function filter_tags( $acf_data ) {
		if ( ! isset( $acf_data['tags'] ) || ! is_array( $acf_data['tags'] ) ) {
			return $acf_data;
		}

		$clean_tags = [];
		foreach ( $acf_data['tags'] as $tag ) {
			$clean_tags[] = (object) [
				'name' => $tag->name,
				'slug' => $tag->slug,
			];
		}
		$acf_data['tags'] = $clean_tags;

		return $acf_data;
	}

	public function get_author_info( $author_id ) {

		$author        = get_user_by( 'id', $author_id );
		$user_image_id = get_field( 'user_image', 'user_' . $author_id );

		return [
			'id'           => $author->ID,
			'name'         => $author->data->display_name,
			'url'          => $author->data->user_url,
			'description'  => get_user_meta( $author->ID, 'description', true ),
			'author_image' => $user_image_id ? $this->image_helper->get_attachment_metadata( $user_image_id ) : [],
			'link'         => get_author_posts_url( $author->ID ),
			'slug'         => sanitize_title( $author->user_login ),
		];
	}

	public function expand_hero_image( $images ) {
		if ( $this->image_helper->placeholder ) {
			return [
				$this->image_helper->get_attachment_metadata( Image::$PLACEHOLDER_IMAGE_ID ),
			];
		}

		if ( empty( $images ) ) {
			return [];
		}

		$return_images = [];
		foreach ( $images as $image ) {
			if ( isset( $image['ID'] ) ) {
				$return_images[] = $image;
				break;
			}

			$return_images[] = $this->image_helper->get_attachment_metadata( $image );
		}

		return $return_images;
	}

	public function get_tagbot_data( $post_id ) {
		$emotions = get_post_meta( $post_id, 'tagbot_emotions', true );
		if ( ! empty( $emotions ) ) {
			foreach ( $emotions as &$emotion ) {
				$emotion = (float) $emotion;
			}
		}

		$sentiment = get_post_meta( $post_id, 'tagbot_sentiments', true );
		if ( ! empty( $sentiment ) ) {
			$score = (float) $sentiment;

			$name = 'neutral';
			if ( $score < -0.25 ) {
				$name = 'negative';
			}

			if ( $score > 0.25 ) {
				$name = 'positive';
			}

			$sentiment = [
				'score' => $score,
				'name'  => $name,
			];
		}

		$categories = get_post_meta( $post_id, 'tagbot_categories', true );
		if ( ! empty( $categories ) ) {
			foreach ( $categories as &$category ) {
				$category->score = (float) $category->score;
			}
		}

		return [
			'categories' => $categories ?: [],
			'sentiment'  => $sentiment ?: new \stdClass(),
			'emotions'   => $emotions ?: new \stdClass(),
		];
	}

	public function get_acf_data( $post_id, $type = 'simplified', $parent = null ) {
		if ( 'simplified' === $type ) {
			return $this->get_simplified_acf_data( $post_id );
		}

		if ( ! $parent ) {
			return $this->apply_filters( get_fields( $post_id ) );
		}

		$parent_fields = get_fields( $parent->ID );

		foreach ( get_fields( $post_id ) as $key => $field ) {
			if ( empty( $field ) ) {
				continue;
			}
			$parent_fields[ $key ] = $field;
		}

		return $this->apply_filters( $parent_fields );
	}

	private function apply_filters( array $fields ) {
		return $this->filter_category(
			$this->filter_tags(
				$fields
			)
		);
	}

	private function get_simplified_acf_data( $post_id ) {

		$short_headline = get_field( 'short_headline', $post_id );
		$sponsor_logo   = get_field( 'sponsor_logo', $post_id );
		$sponsor_banner = get_field( 'sponsor_banner', $post_id );
		$brand_logo     = get_field( 'brand_logo', $post_id );
		$canonicalUrl   = get_field( 'canonical_url', $post_id );

		$return = [
			'short_headline'  => $short_headline ?: get_the_title( $post_id ), // for tiles
			'canonical_url'   =>  $canonicalUrl ? get_permalink( $post_id ) : null,
			'sell'            => get_field( 'sell', $post_id ),
			'project_id'      => intval( get_field( 'sponsor_details_project_id', $post_id ) ),
			'sponsor_name'    => get_field( 'sponsor_details_sponsor_name', $post_id ),
			'sponsor_link'    => get_field( 'sponsor_details_sponsor_link', $post_id ),
			'sponsor_label'   => get_field( 'sponsor_label', $post_id ),
			'sponsor_logo'    => ! empty( $sponsor_logo ) ? $this->image_helper->get_attachment_metadata( $sponsor_logo ) : '',
			'sponsor_banner'  => ! empty( $sponsor_banner ) ? $this->image_helper->get_attachment_metadata( $sponsor_banner ) : '',
			'hero_images'     => [ $this->get_hero_info( $post_id ) ],
			'fullscreen_hero' => get_field( 'fullscreen_hero', $post_id ) ?? '',
			'brand_logo'      => ! empty( $brand_logo ) ? $this->image_helper->get_attachment_metadata( $brand_logo ) : '',
			'category'        => $this->get_category_info( $post_id ),
			'series'          => $this->get_series_info( $post_id ),
			'package_ids'     => $this->get_premium_info( $post_id ),
			'review_rating'   => get_field( 'review_rating', $post_id ),
			'tagbot_data'     => $this->get_tagbot_data( $post_id ),
		];

		if ( ! in_array( get_post( $post_id )->post_type, [ 'sponsored_post', 'sponsored_longform', 'sponsored_quiz_post', 'email', 'video_post' ] ) ) {
			unset( $return['sponsored'] );
			unset( $return['project_id'] );
			unset( $return['sponsor_name'] );
			unset( $return['sponsor_logo'] );
			unset( $return['sponsor_banner'] );
			unset( $return['sponsor_link'] );
			unset( $return['sponsor_type'] );
			unset( $return['sponsor_label'] );
		}

		if ( get_post( $post_id )->post_type == 'video_post' && get_field( 'is_live_video', $post_id ) ) {

			$return['is_live_video']        = get_field( 'is_live_video', $post_id );
			$return['video_url']        = get_field( 'video_url', $post_id );
			$return['video_live_date']  = date('d/m/Y' , strtotime(get_field( 'video_live_date', $post_id)));
			$return['video_start_time'] = get_field( 'video_start_time', $post_id );
			$return['video_end_time']   = get_field( 'video_end_time', $post_id );
		}

		return $return;
	}

	private function get_series_info( $post_id ) {
		$series = get_field( 'series', $post_id );
		if ( empty( $series ) ) {
			return [];
		}

		$series_info = get_fields( $series );

		if ( ! isset( $series_info['series_badge'] ) ) {
			return [];
		}

		return [
			'series_badge' => $this->image_helper->get_attachment_metadata( $series_info['series_badge'] ),
			'slug'         => $series->slug,
		];
	}

	private function get_premium_info( $post_id ) {
		$premium_packages = get_field( 'premium', $post_id );
		if ( empty( $premium_packages ) ) {
			return [];
		}

		$cms_subscription_ids = [];
		foreach ( $premium_packages as $package ) {

			$cms_subscription_ids[] = $package->term_id;
			if ( $package->parent != 0 ) {
				$cms_subscription_ids[] = $package->parent;
			}

			$cms_subscription_ids = array_merge( $cms_subscription_ids, get_term_children( $package->term_id, 'premium' ) );
		}

		$alexandria_subscription_ids = [];
		foreach ( $cms_subscription_ids as $sub_id ) {
			$alexandria_subscription_ids[] = get_term_meta( $sub_id, 'telemetry_subscription_id', true );
		}
		return $alexandria_subscription_ids;
	}

	private function get_category_info( $post_id ) {

		$id = wp_get_post_categories( $post_id );

		if ( empty( $id ) ) {
			return [];
		}

		return get_term( $id[0] );
	}

	private function get_hero_info( $post_id ) {
		if ( $this->show_placeholder( $post_id ) ) {
			return $this->image_helper->get_attachment_metadata(
				Image::$PLACEHOLDER_IMAGE_ID
			);
		}

		$hero_image = get_post_meta( $post_id, 'hero_images', true ) ?: get_post_meta( $post_id, 'image', true ); // for tiles
		if ( empty( $hero_image ) ) {
			return [];
		}

		if ( is_array( $hero_image ) ) {
			$hero_image = $hero_image[0];
		}

		return $this->image_helper->get_attachment_metadata( $hero_image );
	}

	public function show_placeholder( int $post_id ) : bool {
		return $this->is_old_article( $post_id ) &&
			! get_field( 'old_article_image_override', $post_id );
	}

	private function is_old_article( int $post_id ) : bool {
		$original_post_date = get_post_meta( $post_id, 'article_catfish_importer_post_date', true );
		if ( empty( $original_post_date ) ) {
			return false;
		}

		$placeholder_date = self::$PLACEHOLDER_DATE;
		if ( empty( $placeholder_date ) ) {
			return false;
		}

		$placeholder_date   = new \DateTime( $placeholder_date );
		$original_post_date = new \DateTime( $original_post_date );
		$post               = get_post( $post_id );
		$current_date       = new \DateTime( $post->post_date );

		return $original_post_date < $placeholder_date && $current_date < $placeholder_date;
	}

	public function set_video_header_metadata( $acf, $post_id ) {

		if ( isset( $acf['media_type'] ) && $acf['media_type'] === 'vimeo_video' ) {
			$acf['header_vimeo_landscape_id'] = get_post_meta( $post_id, 'header_vimeo_landscape_id', true );
			$acf['header_vimeo_portrait_id'] = get_post_meta( $post_id, 'header_vimeo_portrait_id', true );
		} elseif ( isset( $acf['media_type'] ) && $acf['media_type'] === 'parallax' ) {

			$image_collection        = [];
			$mobile_image_collection = [];
			foreach ( $acf['parallax_hero'] as $image ) {
				$image_collection[]        = $this->image_helper->get_attachment_metadata( $image['image'] );
				$mobile_image_collection[] = $image['mobile_image'] ? $this->image_helper->get_attachment_metadata( $image['mobile_image'] ) : $this->image_helper->get_attachment_metadata( $image['image'] );
			}
			$acf['parallax_hero']                            = [];
			$acf['parallax_hero']['image_collection']        = $image_collection;
			$acf['parallax_hero']['mobile_image_collection'] = $mobile_image_collection;

		} elseif ( isset( $acf['media_type'] ) && $acf['media_type'] === 'looping_video' ) {

			$looping_video_meta                              = get_post_meta( $post_id, 'looping_video_metadata', true );
			$acf['looping_video_meta']['video']              = $looping_video_meta['video'];
			$acf['looping_video_meta']['placeholder']        = $this->image_helper->get_attachment_metadata( $looping_video_meta['desktop_image_id'] );
			$acf['looping_video_meta']['mobile_video']       = $looping_video_meta['mobile_video'];
			$acf['looping_video_meta']['mobile_placeholder'] = $this->image_helper->get_attachment_metadata( $looping_video_meta['mobile_image_id'] );
			$acf['looping_video_meta']['width']              = $looping_video_meta['width'];
			unset( $acf['header_vimeo_landscape_id'] );
			unset( $acf['header_vimeo_portrait_id'] );
		}

		return $acf;

	}

	public function set_next_article( $post_id, $acf ) {
		if($acf['no_pagination']) {
			return null;
		}

		$manual_article = $acf['next_article_manual'][0] ?? false;

		if ( ! empty( $manual_article ) ) {
			return $manual_article;
		}

		$post = get_post( $post_id );
		if ( ! in_array( $post->post_type, $this->get_public_post_types(), true ) ) {
			return null;
		}

		$category     = $acf['category'] ?? false;
		$taxonomy_obj = ! empty( $series ) ? $series : $category;

		if ( $taxonomy_obj === false ) {
			return null;
		}

		$next_article_id = $this->generate_next_article(
			$post_id,
			$taxonomy_obj->taxonomy,
			$taxonomy_obj->term_id
		);

		return $next_article_id ?? null;
	}

	public function get_time_to_read( $post_id ) {
		return get_post_meta( $post_id, 'time_to_read', true ) ?: 0;
	}

	protected function generate_next_article( $post_id, $taxonomy, $taxonomy_id ) {

		$tax_query[] = [
			'taxonomy' => $taxonomy,
			'field'    => 'term_id',
			'terms'    => $taxonomy_id,
		];

		if ( is_plugin_active( 'croissant-premium/plugin.php' ) ) {
			$tax_query[] = [
				'taxonomy' => 'premium',
				'operator' => 'NOT EXISTS',
			];
		}

		$now = new \DateTime();
		$now->sub( new \DateInterval( 'P14D' ) ); // 14 days
		$post_date = new \DateTime( get_post( $post_id )->post_date );

        $args = [
			'fields'         => 'ids',
			'post__not_in'   => [ $post_id ],
			'post_type'      => $this->get_public_post_types(),
			'post_status'    => 'publish',
			'posts_per_page' => 1,
			'tax_query'      => [ $tax_query ],
			'meta_query'     => [
				[
					'key'   => 'no_pagination',
					'value' => '0',
					'compare' => '='
				]
			]
		];

		// recent posts
		if ( $post_date > $now ) {
			$args += [
				'no_found_rows' => 1,
				'date_query'    => [
					[
						'column' => 'post_date',
						'before' => $post_date->format( 'Y-m-d h:i' ),
					],
				],
			];
		} else { // old posts
			$args += [
				'orderby'    => 'date',
				'order'      => 'DESC',
				'date_query' => [
					'before' => date( 'Y-m-d h:i' ),
				],
			];
		}

		$post = $this->wp_query->query( $args );

		if ( ! empty( $post ) && is_array( $post ) ) {
			return $post[0];
		}

		return false;
	}

	// manually unset tiles and feature lists
	protected function get_public_post_types() {
		$post_types = array_merge(
			[ 'post' => 'post' ], get_post_types(
				[
					'public'              => true,
					'exclude_from_search' => false,
					'_builtin'            => false,
				]
			)
		);
		unset(
			$post_types['tile'],
			$post_types['feature_list'],
			$post_types['longform'],
			$post_types['quiz_post'],
			$post_types['sponsored_quiz_post'],
			$post_types['amp_story'],
			$post_types['course']
		);

		return $post_types;
	}

	public function word_count( $post_id ) {

		$widgets = get_field( 'widgets', $post_id );
		if ( empty( $widgets ) ) {
			return 0;
		}

		$text = '';
		foreach ( $widgets as $widget ) {
			if ( $widget['acf_fc_layout'] === 'paragraph' ) {
				if ( ! empty( $text ) ) {
					$text .= ' ';
				}

				$text .= trim(strip_tags($widget['paragraph']));
				continue;
			}

			if ( $widget['acf_fc_layout'] === 'listicle' ) {
				$text .= $this->get_listicle_paragraphs( $widget['item'] );
			}
		}

		$count = count( explode( ' ', $text ) );

		return $count;
	}

	public function get_listicle_paragraphs( array $items ) {
		$text = '';
		foreach ( $items as $item ) {
			if ( ! empty( $text ) ) {
				$text .= ' ';
			}

			$text .= trim(strip_tags($item['paragraph']));
		}

		return $text;
	}

	public function get_vertical_details( $vertical ) {

		if ( ! $vertical ) {
			return new \WP_Error( 'no_vertical', 'Vertical does not exist', [ 'status' => 404 ] );
		}

		$return = [
			'term_id' => $vertical->term_id,
			'name'    => $vertical->name,
			'description' => $vertical->description,
			'slug'    => $vertical->slug,
		];

		$return['parent'] = empty( $vertical->parent ) ? false : get_term( $vertical->parent );

		$email_app_footer_image = get_field( 'email_app_footer_image', 'option' ) ?? '';

		if(!empty($email_app_footer_image)) {
			$email_app_footer_image = $this->image_helper->get_attachment_metadata( $email_app_footer_image );
		}

		$fields = get_fields( $vertical );

		// Removes the vertical signup widget colour fields
		unset( $fields['background_colour'] );
		unset( $fields['text_colour'] );
		unset( $fields['button_colour'] );
		unset( $fields['button_text_colour'] );

		$fields['accent_colour']      = strlen( $fields['accent_colour'] ) > 0 ? $fields['accent_colour'] : '#000000';
		$fields['thumbnail_image']    = $this->image_helper->get_attachment_metadata( $fields['thumbnail_image'] );
		$fields['header_image']       = $this->image_helper->get_attachment_metadata( $fields['header_image'] );
		$fields['footer_image']       = $this->image_helper->get_attachment_metadata( $fields['footer_image'] );
		$fields['telemetry_vertical'] = (int) get_term_meta( $fields['telemetry_vertical'], 'telemetry_vertical_id', true );
		$fields['paid'] = $fields['vertical_paid'];
		$fields['email_footer_app_image'] = $email_app_footer_image;
		unset( $fields['vertical_paid'] );

		return ( array_merge( $return, $fields ) );
	}
}
