<?php

namespace CroissantApi\Endpoint;

use WpTapestryPlugin\Service\PostInterface;

/**
 * Gets the campaign name (info that comes from message gears)
 * and returns the email CMS id (telemetry cannot link the info)
 */
class CampaignApi {

	protected $wp_query;

	public function __construct( $wp_query ) {
		$this->wp_query = $wp_query;
	}

	public function init() {
		add_action( 'rest_api_init', [ $this, 'register_route' ] );
	}

	public function register_route() {
		register_rest_route(
			'croissant/v1', '/campaign', [
				'methods'  => 'GET',
				'callback' => [ $this, 'get_email' ],
				'args'     => [
					'slug' => [
						'required' => true,
					],
				],
			]
		);
	}

	public function get_email( $data ) {

		$slug = urldecode( $data->get_param( 'slug' ) );
		if ( empty( $slug ) ) {
			return new \WP_Error( 'empty_slug', 'paramater is empty', [ 'status' => 404 ] );
		}

		$post = $this->wp_query->query(
			[
				'fields'     => 'ids',
				'post_type'  => 'email',
				'meta_query' => [
					[
						'key'     => 'campaign_name',
						'value'   => $slug,
						'compare' => '=',
					],
				],
			]
		);

		if ( ! $post ) {
			return new \WP_Error( 'undefined_email', 'There is no email with campaign name ' . $slug, [ 'status' => 404 ] );
		}

		return [
			'id'           => $post[0],
			'sponsor_name' => get_field( 'sponsor_name', $post[0] ) ?? null,
			'project_id'   => intval( get_field( 'project_id', $post[0] ) ) !== 0 ? intval( get_field( 'project_id', $post[0] ) ) : null ?? null,
		];
	}
}
