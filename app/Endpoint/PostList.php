<?php

namespace CroissantApi\Endpoint;

use WpTapestryPlugin\Service\PostInterface;

/**
 * This is an abstraction for API endpoints
 * which returns a list of posts, this class
 * removes a bunch of duplications within the
 * plugin's code.
 */
abstract class PostList {


	protected $post_service;

	/**
	 * Construtor that is used for every `return-a-post-list` API endpoint
	 * Uses a `$post_service` param for querying WordPress internally
	 * For further information, please check `CroissantApi\Services\CroissantPostService`
	 */
	public function __construct( PostInterface $post_service ) {
		$this->post_service = $post_service;
	}

	public function init() {
		add_action( 'rest_api_init', [ $this, 'register_route' ] );
	}

	protected function get_public_post_types() {
		return array_merge(
			[ 'post' => 'post' ], get_post_types(
				[
					'public'              => true,
					'exclude_from_search' => false,
					'_builtin'            => false,
				]
			)
		);
	}

	/**
	 * Forces all the classes to have a `register_route` method
	 * which should implement WordPress `register_rest_route`
	 * function
	 */
	abstract protected function register_route();
}
