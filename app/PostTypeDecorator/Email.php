<?php
declare(strict_types=1);

namespace CroissantApi\PostTypeDecorator;

class Email extends PostTypeDecoratorAbstract implements PostTypeDecorator {
	public function decorate_single( array $fields ) : array {
		return $this->decorate_sponsor_images( $fields );
	}
}
