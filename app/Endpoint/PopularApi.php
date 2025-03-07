<?php

namespace CroissantApi\Endpoint;

use WpTapestryPlugin\Service\PostInterface;
use PopularPosts\PopularPosts;

class PopularApi extends PostList {

	protected $post_service;
	private $popular;

	public function __construct( PostInterface $post_service, string $popular_endpoint ) {
		parent::__construct( $post_service );
		$this->popular = $popular_endpoint;
	}

	public function register_route() {
		register_rest_route(
			'croissant/v1', '/popular', [
				'methods'  => 'GET',
				'callback' => [ $this, 'get_posts' ],
				'args'     => [
					'start_date' => [
						'required' => true,
					],
					'end_date'   => [
						'required' => true,
					],
					'from_date'  => [
						'required' => false,
					],
					'results'    => [
						'required' => false,
					],
					'tag'        => [
						'required' => false,
					],
					'categories' => [
						'required' => false,
					],
				],
			]
		);
	}

	public function get_posts( $data ) {
		$results = $data->get_param( 'results' );
		$params['results'] = ! empty( $results ) && $results > 0 ? (int) $results : 5;

		$start_date     = $data->get_param( 'start_date' );
		$start_date_obj = \DateTime::createFromFormat(
			'Y-m-d H:i:s',
			$start_date . '00:00:00',
			new \DateTimeZone( 'Europe/London' )
		);

		if ( empty( $start_date_obj ) ) {
			return new \WP_Error( 'invalid_parameter', 'start_date is invalid', [ 'status' => 400 ] );
		}

		$params['start_date'] = $start_date;

		$end_date     = $data->get_param( 'end_date' );
		$end_date_obj = \DateTime::createFromFormat(
			'Y-m-d H:i:s',
			$end_date . '00:00:00',
			new \DateTimeZone( 'Europe/London' )
		);

		if ( empty( $end_date_obj ) ) {
			return new \WP_Error( 'invalid_parameter', 'end_date is invalid', [ 'status' => 400 ] );
		}

		$params['end_date'] = $end_date;

		$from_date = $data->get_param( 'from_date' );
		if ( ! empty( $from_date ) ) {
			$from_date_obj = \DateTime::createFromFormat(
				'Y-m-d H:i:s',
				$from_date . '00:00:00',
				new \DateTimeZone( 'Europe/London' )
			);

			if ( empty( $from_date_obj ) ) {
				return new \WP_Error( 'invalid_parameter', 'from_date is invalid', [ 'status' => 400 ] );
			}

			$params['from_date'] = $from_date;
		}

		$tag = $data->get_param( 'tag' );
		if ( ! empty( $tag ) ) {
			$params['tag'] = $tag;
		}

		$categories = $data->get_param( 'categories' );
		if ( ! empty( $categories ) ) {
			$categories       = explode( ',', $categories );
			$named_categories = [];
			foreach ( $categories as $cat ) {
				$obj = get_category_by_slug( $cat );
				if ( ! $obj instanceof \WP_Term ) {
					continue;
				}

				$named_categories[] = strtolower( $obj->name );
			}

			$named_categories   = implode( ',', $named_categories );
			$params['category'] = $named_categories;
		}

		$sponsored = $data->get_param( 'sponsored' );
		if ( ! empty( $sponsored ) ) {
			$params['sponsored'] = true;
		}

		$url       = $this->popular . '?' . http_build_query( $params );
		$cache_key = crc32( $url );
		$posts     = wp_cache_get( $cache_key, 'popular' );
		if ( ! empty( $posts ) ) {
			return $posts;
		}

		$response = wp_remote_get( $url );
		if ( $response instanceof \WP_Error ) {
			return new \WP_Error( 'internal_server_error', 'Internal server Error', [ 'status' => 500 ] );
		}

		$popular_posts = json_decode( $response['body'], true )['data'];
		if ( empty( $popular_posts ) ) {
			return [];
		}

		$posts = $this->post_service->get_post_list(
			[
				'post_type'           => $this->get_public_post_types(),
				'post__in'            => $popular_posts,
				'posts_per_page'      => $params['results'],
				'orderby'             => 'post__in',
				'ignore_sticky_posts' => 1,
			]
		);

		if ( empty( $posts ) ) {
			return new \WP_Error( 'no_posts', 'no posts to display', [ 'status' => 404 ] );
		}

		wp_cache_set( $cache_key, $posts, 'popular', 3600 );

		return $posts;
	}
}
