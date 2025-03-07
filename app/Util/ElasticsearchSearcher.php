<?php
declare(strict_types=1);

namespace CroissantApi\Util;

class ElasticsearchSearcher implements ElasticsearchSearcherInterface {
	private $client;
	private $index;

	public function __construct(HttpClientInterface $client, string $index) {
		$this->client = $client;
		$this->index = $index;
	}

	public function search(string $query, int $page = 1, int $size = 20) {

		$body = [
			'query' => [
				'bool' => [
					'must' => [
						[
							'multi_match' => [
								'query' => $query,
								'fields' => [
									'post_title',
									'terms.category.name',
									'meta.standfirst.value',
									'terms.post_tag.name',
									'post_author.display_name'
								],
								'operator' => 'or'
							]
						]
					],
					'filter' => [
						[
							'term' => [
								'post_status' => 'publish'
							]
						]
					]
				]
			],
			'sort' => [
				[
					'post_date' => [
						'order' => 'desc'
					]
				]
			],
			'from' => $page,
			'size' => $size
		];


		$response = $this->client->post("/{$this->index}/_search", [
			'json' => $body
		]);

		// Get the response from the server
		if ($response) {
			$responseBody = $response->getBody();
			$data = json_decode((string) $responseBody, true); // Ensure response body is a string

			// Extract and print the _id fields from the response
			$postIds = [];
			if (isset($data['hits']['hits'])) {
				foreach ($data['hits']['hits'] as $hit) {
					$postIds[] = (int) $hit['_id'];
				}
				return $postIds;
			} else {
				return [];
			}
		}
	}
}
