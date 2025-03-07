<?php
declare(strict_types=1);

namespace CroissantApi\Transformer;

class Advert extends TransformerAbstract implements Transformer {

	public function apply_transformation( array $widget ) : array {

		foreach ( $widget['assets'] as &$advert ) {
			$advert['image'] = $this->image_helper->get_attachment_metadata( $advert['image'] );
		}

		if ( ! isset( $widget['layout'] ) ) {
			$widget['layout'] = 'mpu';
		}

		return $widget;
	}
}
