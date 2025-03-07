<?php

namespace CroissantApi\Endpoint;

use CroissantApi\Service\SeoSchemaGenerator;

/**
 * Implements an API endpoint to get a list of posts by a given tag
 * This class extends PostList, which forces to have a `register_route`
 * method. Please check `CroissantApi\Endpoints\PostList` for further
 * information.
 *
 * This class enables `/wp-json/wp/v2/croissant_categories?slug=category-name`
 * and `/wp-json/croissant/v1/categories?slug=category-name` API endpoints
 */
class CategoriesApi extends PostList {

	private $seo_schema_generator;

	public function __construct( SeoSchemaGenerator $seo_schema_generator ) {
		$this->seo_schema_generator = $seo_schema_generator;
	}

	public function register_route() {
		register_rest_route(
			'croissant/v1', '/categories', [
				'methods'  => 'GET',
				'callback' => [ $this, 'get_category_info' ],
				'args'     => [
					'slug' => [
						'required' => true,
					],
				],
			]
		);
	}

	public function get_category_info( $data ) {
		$slug     = $data->get_param( 'slug' );
		$category = get_term_by( 'slug', $slug, 'category' );
		$seo_title = get_field( 'seo_title', $category);
		$seo_description = get_field( 'seo_description', $category);
		$schema = $this->seo_schema_generator->generateCategorySchema( $category );

		if ( ! $category ) {
			return new \WP_Error( 'no_category', 'Category ' . $slug . ' does not exist', [ 'status' => 404 ] );
		}

		return [
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
			'canonical_url' => get_term_link( $category, 'category' ),
			'seo_title' => $seo_title,
			'seo_description' => $seo_description,
			'seo_schema' =>  json_encode($schema, true, JSON_UNESCAPED_SLASHES)
		];
	}
}
