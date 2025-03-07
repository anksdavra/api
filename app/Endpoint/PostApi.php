<?php

namespace CroissantApi\Endpoint;

use WpTapestryPlugin\Service\PostInterface;

/**
 * Implements an API endpoint to get a list of posts by a given author
 * This class extends PostList, which forces to have a `register_route`
 * method. Please check `CroissantApi\Endpoints\PostList` for further
 * information.
 */
class PostApi implements CroissantArticleInterface {

	protected $post_service;

	public function __construct( PostInterface $post_service ) {
		$this->post_service = $post_service;
	}

	public function init() {
		add_action( 'rest_api_init', [ $this, 'register_route' ] );
	}

	public function register_route() {
		register_rest_route(
			'croissant/v1', '/posts/(?P<id>[\d]+)', [
				'methods'  => 'GET',
				'callback' => [ $this, 'get_post' ],
			]
		);
	}

	public function get_post( $data ) {
		return $this->get_article_data(
			[
				'id' => $data->get_url_params()['id'],
			]
		);
	}

	public function get_article_data( array $params ) {

		$hidden = $this->check_post_visibility( $params['id'] );

		if ( $hidden ) {
			return new \WP_Error( 'unavailable_post', 'This post is not available', [ 'status' => 404 ] );
		}

		$post = $this->post_service->get_single_post(
			[
				'p'         => $params['id'],
				'post_type' => array_merge(
					[ 'post' => 'post' ], get_post_types(
						[
							'public'             => true,
							'publicly_queryable' => true,
							'_builtin'           => false,
						]
					)
				),
			]
		);

		if ( ! $post ) {
			return new \WP_Error( 'undefined_post', 'This post does not exist', [ 'status' => 404 ] );
		}

		return $post;
	}

	private function check_post_visibility( $id ) {
		$visibility = get_the_terms( $id, 'visibility' );

		if ( $visibility === false || $visibility[0]->slug !== 'hidden' ) {
			return false;
		}

		return true;
	}
}
