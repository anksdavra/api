<?php
declare(strict_types=1);

namespace CroissantApi\Transformer;

class ProductCarousel extends TransformerAbstract implements Transformer {

	public function apply_transformation( array $widget ) : array {
		foreach ( $widget['products'] as &$product ) {
			$product['thumbnail'] = $this->image_helper->get_attachment_metadata( $product['thumbnail'] );
		}

		return $widget;
	}
}
