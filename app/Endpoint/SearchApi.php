<?php

namespace CroissantApi\Endpoint;

use WpTapestryPlugin\Service\PostInterface;
use CroissantApi\Util\GuzzleHttpClient;
use CroissantApi\Util\ElasticsearchSearcher;

class SearchApi {

	protected $post_service;
	protected $httpClient;
	protected $searcher;

	public function __construct(PostInterface $post_service, GuzzleHttpClient $httpClient = null, ElasticsearchSearcher $searcher = null) {
		$this->post_service = $post_service;

		// Default values if not provided
		$ep_host = getenv('EP_HOST');
		$ep_index = getenv('WP_ENV');
		$this->httpClient = $httpClient ?: new GuzzleHttpClient($ep_host);
		$this->searcher = $searcher ?: new ElasticsearchSearcher($this->httpClient, $ep_index);
	}

	public function init() {
		add_action('rest_api_init', [$this, 'register_route']);
	}

	public function register_route() {
		register_rest_route(
			'croissant/v1', '/search', [
				'methods'  => 'GET',
				'callback' => [$this, 'get_post'],
				'args'     => [
					's'        => [
						'required' => true,
					],
					'per_page' => [
						'required' => false,
					],
					'page'     => [
						'required' => false,
					],
				],
			]
		);
	}

	public function get_post($data) {

		$query = filter_var($data->get_param('s'), FILTER_SANITIZE_FULL_SPECIAL_CHARS);
		$page = $data->get_param('page') ?: 1;
		$per_page = $data->get_param('per_page') ?: 20;

		if ($per_page > 25) {
			$per_page = 25;
		}

		// Perform the search
		$post_ids = $this->searcher->search($query, $page, $per_page);

		if (!empty($post_ids)) {
			$params = [
				'post__in'            => $post_ids,
				'posts_per_page'      => -1, // Get all posts matching the ID
				'orderby'             => 'post__in',
				'ignore_sticky_posts' => 1,
			];
			$posts = $this->post_service->get_post_list($params);
			return $posts;
		} else {
			return new \WP_Error('undefined_post', 'This post does not exist', ['status' => 404]);
		}
	}
}
