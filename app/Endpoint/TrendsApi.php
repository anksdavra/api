<?php

namespace CroissantApi\Endpoint;

class TrendsApi extends PostList {

	private $image_helper;

	public function __construct( $image_helper, $post_service ) {
		$this->image_helper = $image_helper;
		$this->post_service = $post_service;
	}

	public function register_route() {
		register_rest_route(
			'croissant/v1', '/trends', [
				'methods'  => 'GET',
				'callback' => [ $this, 'get_category_info' ],
				'args'     => [
					'id' => [
						'required' => true,
					],
				],
			]
		);
	}

	public function get_category_info( $data ) {
		if ( strlen( $data->get_param( 'id' ) ) == 0 ) {
			return new \WP_Error( 'rest_missing_callback_param', 'No values passed for the id parameter', [ 'status' => 400 ] );
		}

		$ids        = explode( ',', $data->get_param( 'id' ) );
		$categories = [];

		foreach ( $ids as $id ) {
			$category = get_term_by( 'id', $id, 'category' );
			if ( empty( $category ) ) {
				continue;
			}

			$image = get_field(
				'category_image',
				'category_' . $category->term_id
			);
			if ( $image ) {
				$image = $this->image_helper->get_attachment_metadata( $image );
			}

			$categories[] = [
				'id'       => $category->term_id,
				'count'    => $category->count,
				'link'     => get_term_link( $category, 'category' ),
				'name'     => $category->name,
				'slug'     => $category->slug,
				'parent'   => ! empty( $category->parent ) ? get_category( $category->parent )->slug : null,
				'taxonomy' => $category->taxonomy,
				'acf'      => [
					'sell' => $category->description ?? '',
				],
				'image'    => $image ?: null,
			];
		}

		if ( sizeof( $categories ) == 0 ) {

			return new \WP_Error( 'no_category', 'Categories not found', [ 'status' => 404 ] );
		}

		return $categories;
	}
}
