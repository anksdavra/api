<?php
declare(strict_types=1);

namespace CroissantApi\PostTypeDecorator;

interface PostTypeDecorator {
	public function decorate_single( array $fields ) : array;
	public function decorate_list( array $fields ) : array;
}
