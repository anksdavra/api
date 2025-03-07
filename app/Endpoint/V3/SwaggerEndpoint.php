<?php

namespace CroissantApi\Endpoint\V3;

use OpenApi\Annotations as OA;

class SwaggerEndpoint {

	public function init() {
		add_action( 'rest_api_init', [ $this, 'register_route' ] );
	}

	public function register_route() {
		register_rest_route(
			'croissant/v3',
			'/swagger',
			[
				'methods'  => 'GET',
				'callback' => [ $this, 'get_swagger_json' ],
			]
		);
	}

	public function get_swagger_json( $data ) {
		// Scan your annotated files (adjust the path if necessary)
		$openapi = \OpenApi\Generator::scan(['app']);

		// Generate the JSON output
		$json = $openapi->toJson();

		// Replace the placeholder with the current WP_HOME value
		$wp_home = getenv('WP_HOME') ?: 'https://default.local';
		$json = str_replace('{WP_HOME}', $wp_home, $json);

		// Optional: add caching here to avoid scanning on every request

		// Return the JSON response
		return json_decode($json, true);
	}
}
