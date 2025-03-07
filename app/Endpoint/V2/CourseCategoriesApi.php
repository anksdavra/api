<?php

namespace CroissantApi\Endpoint\V2;

class CourseCategoriesApi {

	public function init() {
		add_action( 'rest_api_init', [ $this, 'register_route' ] );
	}

	public function register_route() {
		register_rest_route(
			'croissant/v2', '/course_categories', [
				'methods'  => 'GET',
				'callback' => [ $this, 'get_course_category_info' ],
				'args'     => [
					'slug' => [
						'required' => true,
					],
				],
			]
		);
	}

	public function get_course_category_info( $data ) {
		$slug            = $data->get_param( 'slug' );
		$course_category = get_term_by( 'slug', $slug, 'course_category' );

		if ( ! $course_category ) {
			return new \WP_Error( 'no_course_category', 'Course category ' . $slug . ' does not exist', [ 'status' => 404 ] );
		}

		return [
			'id'       => $course_category->term_id,
			'count'    => $course_category->count,
			'link'     => get_term_link( $course_category, 'course_category' ),
			'name'     => $course_category->name,
			'slug'     => $course_category->slug,
			'parent'   => ! empty( $course_category->parent ) ? get_category( $course_category->parent )->slug : null,
			'taxonomy' => $course_category->taxonomy,
			'acf'      => [
				'sell' => $course_category->description ?? '',
			],
		];
	}
}
