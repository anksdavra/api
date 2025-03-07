<?php
declare(strict_types=1);

namespace CroissantApi\Transformer;

interface Transformer {

	public function apply_transformation( array $widget ) : array;
}
