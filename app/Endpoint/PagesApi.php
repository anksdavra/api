<?php

namespace CroissantApi\Endpoint;

class PagesApi {

	private $wp_query = null;

	public function set_wp_query( $wp_query ) {
		$this->wp_query = $wp_query;
	}

	public function init() {
		add_action( 'rest_api_init', [ $this, 'register_route' ] );
	}

	public function register_route() {
		register_rest_route(
			'croissant/v1', '/pages', [
				'methods'  => 'GET',
				'callback' => [ $this, 'get_page_info' ],
				'args'     => [
					'slug' => [
						'required' => true,
					],
				],
			]
		);
	}

	public function get_page_info( $data ) {
		$slug   = $data->get_param( 'slug' );
		$params = [
			'name'      => $slug,
			'post_type' => 'page',
		];
		$page   = $this->wp_query->query( $params );

		if ( ! $page ) {
			return new \WP_Error( 'no_page', 'Page ' . $slug . ' does not exist', [ 'status' => 404 ] );
		}

		$acf_fields = get_fields( $page[0] );

		return [
			'ID'           => $page[0]->ID,
			'title'        => [
				'rendered' => $page[0]->post_title,
			],
			'name'         => $page[0]->post_name,
			'canonical_url' => get_permalink( $page[0]->ID ),
			'link'         => get_permalink( $page[0]->ID ),
			'date'         => $page[0]->post_date,
			'date_gmt'     => $page[0]->post_date_gmt,
			'modified'     => $page[0]->post_modified,
			'modified_gmt' => $page[0]->post_modified_gmt,
			'status'       => $page[0]->post_status,
			'author'       => $page[0]->post_author,
			'type'         => $page[0]->post_type,
			'parent'       => $page[0]->post_parent,
			'acf'          => [
				'short_headline' => $acf_fields['short_headline'],
				'sell'           => $acf_fields['sell'],
				'widgets'        => $acf_fields['widgets'],
			],
		];
	}
}
