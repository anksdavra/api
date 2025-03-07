<?php

namespace CroissantApi\Endpoint;

class TagbotCategoriesApi {


	public function init() {
		add_action( 'rest_api_init', [ $this, 'register_route' ] );
	}

	public function register_route() {
		register_rest_route(
			'croissant/v1', '/tagbot_categories/(?P<id>[\d]+)', [
				'methods'  => 'GET',
				'callback' => [ $this, 'get_tagbot_categories' ],
			]
		);
	}

	public function get_tagbot_categories( $data ) {
		$id                = $data->get_url_params()['id'];
		$tagbot_categories = get_post_meta( $id, 'tagbot_categories', true );

		if ( ! $tagbot_categories ) {
			return new \WP_Error( 'undefined_tagbot_category', 'Tagbot category doesn\'t exist for this post', [ 'status' => 404 ] );
		}

		return [
			'categories' => $tagbot_categories,
		];
	}
}
