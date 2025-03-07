<?php

namespace CroissantApi\Endpoint;

use WpTapestryPlugin\Service\PostInterface;

interface CroissantArticleInterface {
	public function get_article_data( array $params);
}
