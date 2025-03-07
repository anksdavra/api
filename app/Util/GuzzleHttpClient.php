<?php
declare(strict_types=1);

namespace CroissantApi\Util;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use InvalidArgumentException;

class GuzzleHttpClient implements HttpClientInterface {
	protected $client;

	public function __construct($baseUri, $timeout = 2.0) {
		if (empty($baseUri) || !filter_var($baseUri, FILTER_VALIDATE_URL)) {
			throw new InvalidArgumentException('Invalid or missing base URI.');
		}

		$this->client = new Client([
			'base_uri' => $baseUri,
			'timeout'  => $timeout,
		]);
	}

	public function post(string $uri, array $options) {
		try {
			return $this->client->request('POST', $uri, $options);
		} catch (RequestException $e) {
			if ($e->hasResponse()) {
				$response = $e->getResponse();
				$responseBody = $response->getBody();
				$errorData = json_decode((string) $responseBody, true);
				echo '<pre>Error Response: ' . json_encode($errorData, JSON_PRETTY_PRINT) . '</pre>';
			} else {
				echo 'HTTP Request failed: ' . $e->getMessage();
			}
		}
	}
}

