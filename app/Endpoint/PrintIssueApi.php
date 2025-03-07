<?php

namespace CroissantApi\Endpoint;

class PrintIssueApi {

	public function init() {
		add_action( 'rest_api_init', [ $this, 'register_route' ] );
	}

	public function register_route() {
		register_rest_route(
			'croissant/v1', '/print_issue', [
				'methods'  => 'GET',
				'callback' => [ $this, 'get_print_issue_info' ]
			]
		);
	}

	public function get_print_issue_info() {

		$print_magazine_delivery_date = get_field( 'print_magazine_delivery_date', 'option' );
		$beauty_sample_delivery_date   = get_field( 'beauty_sample_delivery_date', 'option' );

		return [
			'print_magazine_delivery_date' => $print_magazine_delivery_date,
			'beauty_sample_delivery_date' => $beauty_sample_delivery_date
		];
	}
}
