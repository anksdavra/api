<?php
declare(strict_types=1);

namespace CroissantApi\Util;

interface HttpClientInterface {
	public function post(string $uri, array $options);
}
