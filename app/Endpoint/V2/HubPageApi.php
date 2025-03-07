<?php

namespace CroissantApi\Endpoint\V2;

/**
 * Implements an API endpoint to get the hub page details
 * associated with a given category.
 **/

class HubPageApi {

	private $post_list;

	public function __construct(
		$post_list
	) {
		$this->post_list = $post_list;
	}

	public function init() {
		add_action( 'rest_api_init', [ $this, 'register_route' ] );
	}

	public function register_route() {
		register_rest_route(
			'croissant/v2', '/hub-page', [
				'methods'  => 'GET',
				'callback' => [
					$this,
					'get_details',
				],
				'args'     => [
					'packages'   => [
						'required' => true,
					],
					'post_types' => [
						'required' => true,
					],
					'number_of_posts' => [
						'required' => false,
					],
				],
			]
		);
	}

	public function get_details( $data ) {

		if ( $data->get_param( 'packages' ) == '' || $data->get_param( 'post_types' ) == '' ) {
			return new \WP_Error( 'rest_missing_callback_param', 'Missing parameter(s): packages, post_types', [ 'status' => 400 ] );
		}

		$posts = $this->post_list->get_results( $data );

		if ( is_wp_error( $posts ) ) {
			return $posts;
		}

		$result = array_merge( $this->get_latest( $data ), $this->get_special_offers( $data ) );
		return $result;
	}

	private function get_latest( $data ) {

		$latest_posts = [];
		$posts_slugs  = explode( ',', $data->get_param( 'post_types' ) );
		$number_of_posts = $data->get_param( 'number_of_posts' ) ?? 5;
		foreach ( $posts_slugs as $slug ) {

			$data->set_param( 'post_types', $slug );
			$data->set_param( 'per_page', $number_of_posts );

			$posts = $this->post_list->get_results( $data );
			$key   = $slug . '_latest';

			$latest_posts[ $key ] = is_wp_error( $posts ) ? [] : $posts['posts'];

		}
		return $latest_posts;
	}

	private function get_special_offers( $data ) {

		$data->set_param( 'post_types', 'post' );
		$data->set_param( 'per_page', 3 );
		$data->set_param( 'categories', 'win' );
		$data->set_param( 'packages', 'strong-women-training-club' );

		$posts          = $this->post_list->get_results( $data );
		$special_offers = is_wp_error( $posts ) ? [] : $posts['posts'];
		return [ 'special_offers' => $special_offers ];
	}
}
