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
class TagsApi extends PostList {

	private $seo_schema_generator;

	public function __construct( SeoSchemaGenerator $seo_schema_generator ) {
		$this->seo_schema_generator = $seo_schema_generator;
	}

	public function register_route() {
		register_rest_route(
			'croissant/v1', '/tags', [
				'methods'  => 'GET',
				'callback' => [ $this, 'get_tag_info' ],
				'args'     => [
					'slug' => [
						'required' => true,
					],
				],
			]
		);
	}

	public function get_tag_info( $data ) {
		$slug = $data->get_param( 'slug' );
		$tag  = get_term_by( 'slug', $slug, 'post_tag' );
		if ( ! $tag ) {
			return new \WP_Error( 'no_tag', 'Tag ' . $slug . ' does not exist', [ 'status' => 404 ] );
		}
		$seo_title = get_field( 'seo_title', $tag);
		$seo_description = get_field( 'seo_description', $tag);
		if($tag) {
			if (empty($seo_title)) {
				$seo_title = $tag->name . " news and features | Stylist Magazine";
			}

			if (empty($seo_description)) {
				$seo_description = "All the latest " . $tag->name . " news and features, brought to you by Stylist.";
			}
		}
		$schema = $this->seo_schema_generator->generateTagSchema( $tag );

		return [
			'id'              => $tag->term_id,
			'count'           => $tag->count,
			'link'            => get_term_link( $tag, 'post_tag' ),
			'name'            => $tag->name,
			'slug'            => $tag->slug,
			'taxonomy'        => $tag->taxonomy,
			'description'     => $tag->description ?: $tag->name . ', breaking news, photos, comments, social media posts on this topic.',
			'canonical_url'   => get_term_link( $tag, 'post_tag' ),
			'seo_title'       => $seo_title,
			'seo_description' => $seo_description,
			'seo_schema' =>  json_encode($schema, true, JSON_UNESCAPED_SLASHES)
		];
	}
}
