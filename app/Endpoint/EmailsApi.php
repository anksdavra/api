<?php

namespace CroissantApi\Endpoint;

use WpTapestryPlugin\Service\PreviewHashGenerator;
use WpTapestryPlugin\Service\PostInterface;
use CroissantApi\Util\Image;
use CroissantApi\Util\Post;

class EmailsApi implements CroissantArticleInterface {

	private $post_service;
	private $hash;
	private $image_helper;

	public function __construct(
		PostInterface $post_service,
		PreviewHashGenerator $hash,
		Image $image_helper
	) {
		$this->post_service = $post_service;
		$this->hash         = $hash;
		$this->image_helper = $image_helper;
	}

	public function init() {
		add_action( 'rest_api_init', [ $this, 'register_route' ] );
	}

	public function register_route() {
		register_rest_route(
			'croissant/v1', '/emails/(?P<id>[\d]+)', [
				'methods'  => 'GET',
				'callback' => [
					$this,
					'get_email',
				],
				'args'     => [
					'tapestry_hash' => [
						'required' => false,
					],
					'p'             => [
						'required' => false,
					],
				],
			]
		);
	}

	public function get_email( $data ) {
		return $this->get_article_data(
			[
				'tapestry_hash' => $data->get_param( 'tapestry_hash' ) ?? '',
				'p'             => $data->get_param( 'p' ) ?? '',
				'id'            => $data->get_url_params()['id'] ?? '',
			]
		);
	}

	public function get_article_data( array $params ) {
		$tapestry_hash = $params['tapestry_hash'] ?? '';
		$id            = $params['p'] ?? '';
		$is_preview = ! empty( $tapestry_hash ) && ! empty( $id );

		if ( $is_preview && ! $this->hash->verify_hash( $tapestry_hash, $id ) ) {
			return new \WP_Error( 'no_post', 'Post does not exist', [ 'status' => 404 ] );
		}

		$id          = $params['id'];
		$post_type   = 'email';
		$post_status = 'publish';
		if ( $is_preview ) {
			$id          = array_values( wp_get_post_revisions( $id ) )[0]->ID;
			$post_type   = 'revision';
			$post_status = [ 'draft', 'auto-draft', 'inherit', 'publish' ];
		}

		$post = $this->post_service->get_single_post(
			[
				'p'           => $id,
				'post_type'   => $post_type,
				'post_status' => $post_status,
			]
		);

		if ( ! $post ) {
			return new \WP_Error( 'undefined_post', 'This post does not exist', [ 'status' => 404 ] );
		}

		if ( isset( $post['acf']['header_image'] ) && ! empty( $post['acf']['header_image'] ) ) {
			$post['acf']['header_image'] = $this->image_helper->get_attachment_metadata( $post['acf']['header_image'] ?? [] );
		}

		$image_credits = [];
		if ( ! empty( $post['acf']['email_vertical']['thumbnail_image']['description'] ) ) {
			$image_credits[] = $post['acf']['email_vertical']['thumbnail_image']['description'];
		}

		if ( isset( $post['acf']['sponsored'] ) && $post['acf']['sponsored'] && strlen( $post['acf']['accent_colour'] ) == 0 ) {
			$post['acf']['accent_colour'] = '#000000';
		}

		$widgets        = $post['acf']['widgets'];
		$widget_credits = [];
		foreach ( $widgets as $widget ) {
			if ( $widget['acf_fc_layout'] == 'email_post_collection' ) {
				$widget_credits = $this->get_image_credits_from_post_collection( $widget['posts'] );
			} else {
				$widget_credits = $this->get_image_credits( $widget ?? [] );
			}
			$image_credits = array_merge( $image_credits, $widget_credits );
		}

		$image_credits                = implode( '; ', array_unique( $image_credits ) ) ?? '';
		$post['acf']['image_credits'] = $image_credits;
		return $post;

	}

	public function get_image_credits( array $array ) {
		$credits = [];
		foreach ( $array as $key => $value ) {

			// if the current array is not the image array, keep looking
			if ( is_array( $value ) && ! array_key_exists( 'alt', $value ) ) {

				$credits = array_merge( $credits, $this->get_image_credits( $value ?? [] ) );

				// If the current array has the alt key, it must be the image details array
			} elseif ( is_array( $value ) && array_key_exists( 'alt', $value ) ) {

				if ( $value['description'] != null && $value['description'] != '' ) {
					$credits[] = $value['description'];
				}
			}
		}

		return $credits;
	}

	private function get_image_credits_from_post_collection( $post_collection_widget ) {
		$credits = [];
		foreach ( $post_collection_widget as $post ) {
			if ( empty( $post['image'] ) ) {
				$credits = array_merge( $credits, $this->get_image_credits( $post['post']['acf']['hero_images'] ?? [] ) );
			} elseif ( ! empty( $post['image']['description'] ) ) {
				$credits[] = $post['image']['description'];
			}
		}

		return $credits;
	}
}
