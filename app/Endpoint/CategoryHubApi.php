<?php

namespace CroissantApi\Endpoint;

/**
 * Implements an API endpoint to get the hub page details associated with a given category.
 **/


class CategoryHubApi {

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
			'croissant/v1', '/category-hub', [
				'methods'  => 'GET',
				'callback' => [
					$this,
					'get_hub_page_info',
				],
				'args'     => [
					'slug'     => [
						'required' => true,
					],
					'page'     => [
						'required' => false,
					],
					'per_page' => [
						'required' => false,
					],
				],
			]
		);
	}

	public function get_hub_page_info( $data ) {
		$slug     = $data->get_param( 'slug' );
		$category = get_term_by( 'slug', $slug, 'category' );

		if ( ! $category ) {
			return new \WP_Error( 'no_category', 'Category ' . $slug . ' does not exist', [ 'status' => 404 ] );
		}

		if ( ! get_field( 'is_hub_page', 'category_' . $category->term_id ) ) {
			return new \WP_Error( 'no_hub_page', 'A hub page has not been associated with this category', [ 'status' => 418 ] );
		}

		$hub_page_id = get_field( 'hub_page', 'category_' . $category->term_id );
		return [
			'category_name' => $category->name,
			'latest_posts'  => $this->get_latest_posts( $data ),
			'popular_posts' => $this->get_popular_posts( $hub_page_id ),
			'acf'           => get_fields( $hub_page_id ),
		];
	}

	private function get_latest_posts( $data ) {

		if ( $data->get_param( 'per_page' ) == null ) {
			$data->set_param( 'per_page', 24 );
		}

		$latest = $this->post_list->get_posts( $data );
		if ( is_wp_error( $latest ) ) {
			return [];
		}
		return $latest;
	}

	private function get_popular_posts( $hub_page_id ) {
		$popular_posts = get_post_meta( $hub_page_id, 'popular_posts', true );

		if ( ! $popular_posts ) {
			return [];
		}

		return $popular_posts;
	}
}
