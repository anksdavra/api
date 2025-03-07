<?php
declare(strict_types=1);

namespace CroissantApi\Util;

interface ElasticsearchSearcherInterface {
	public function search(string $query, int $page = 1, int $size = 20);
}
