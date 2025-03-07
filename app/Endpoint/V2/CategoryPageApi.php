<?php

namespace CroissantApi\Endpoint\V2;

/**
 * Implements an API endpoint to get the response for a specific category page
 **/

class CategoryPageApi {

	private $post_service;
	private $image_helper;

	public function __construct(
		$post_service,
		$image_helper
	) {
		$this->post_service = $post_service;
		$this->image_helper = $image_helper;
	}

	public function init() {
		add_action( 'rest_api_init', [ $this, 'register_route' ] );
	}

	public function register_route() {
		register_rest_route(
			'croissant/v2', '/category', [
				'methods'  => 'GET',
				'callback' => [
					$this,
					'get_category_page',
				],
				'args'     => [
					'term' => [
						'required' => true,
					],
				],
			]
		);
	}

	public function get_category_page( $data ) {
		$term = $data->get_param( 'term' );

		if ( $term == '' ) {
			return new \WP_Error( 'rest_missing_callback_param', 'Missing parameter: term', [ 'status' => 400 ] );
		}

		$term_id = get_term_by( 'slug', $term, 'category' )->term_id ?? false;

		if ( ! $term_id ) {
			return new \WP_Error( 'rest_term_not_found', 'Term not found: ' . $term, [ 'status' => 404 ] );
		}

		return [
			'first_article' => $this->get_first_sticky( $term_id ) ?? $this->get_published_post($term_id),
			'authors'       => $this->get_authors( $term_id ),
			'articles'      => $this->get_articles( $term_id ),
		];
	}

	private function get_first_sticky( $term_id ) {

		$params = [
			'posts_per_page' => 1,
			'post_type'      => [ 'post', 'longform', 'quiz_post' ],
			'cat'            => $term_id,
			'post__in'       => get_option( 'sticky_posts' ),
			'tax_query'      => [
				[
					'taxonomy' => 'visibility',
					'operator' => 'NOT EXISTS',
				],
			],
		];

		return $this->post_service->get_post_list( $params )[0] ?? null;
	}

	private function get_published_post( $term_id ) {

		$params = [
			'posts_per_page' => 1,
			'post_type'      => [ 'post', 'longform', 'quiz_post' ],
			'cat'            => $term_id,
			'orderby' => 'date',
			'order' => 'DESC',
			'ignore_sticky_posts' => 1,
			'tax_query'      => [
				[
					'taxonomy' => 'visibility',
					'operator' => 'NOT EXISTS',
				],
			],
		];

		return $this->post_service->get_post_list( $params )[0] ?? null;
	}

	private function get_articles( $term_id ) {

		$params = [
			'posts_per_page' => 20,
			'post_type'      => [ 'post', 'longform', 'quiz_post' ],
			'cat'            => $term_id,
			'tax_query'      => [
				[
					'taxonomy' => 'visibility',
					'operator' => 'NOT EXISTS',
				],
			],
		];

		$articles = $this->post_service->get_post_list( $params );
		return array_chunk( $articles, 5, false ) ?? false;
	}

	private function get_authors( $term_id ) {
		$authors = [];
		$options = get_field( 'categories_authors', 'option' );

		foreach ( $options as $option ) {
			if ( $option['category'] == $term_id ) {
				$authors = $option['authors'];
				break;
			}
		}

		foreach ( $authors as &$author ) {
			$args                          = [
				'post_type'  => 'any',
				'author' => $author['ID'],
				'cat'    => $term_id,
				'meta_key'   => 'magnum_opus',
				'meta_value' => 1,
			];
			$representative_post           = $this->post_service->get_post_list( $args )[0] ?? false;
			$author['representative_post'] = $representative_post;
			$author['user_image']          = $this->image_helper->get_attachment_metadata( get_field( 'user_image', 'user_' . $author['ID'] ) );
		}
		return $authors;
	}
}
