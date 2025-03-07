<?php
declare(strict_types=1);

namespace CroissantApi\Transformer;

class TelemetryAcquisition extends TransformerAbstract implements Transformer {

	public function apply_transformation( array $widget ) : array {

		if ( ! empty( $widget['voucher_brand_header'] ) ) {
			$widget['voucher_brand_header'] = $this->image_helper
				->get_attachment_metadata( $widget['voucher_brand_header'] );
		}

		if ( ! empty( $widget['voucher_hero_image'] ) ) {
			$widget['voucher_hero_image'] = $this->image_helper
				->get_attachment_metadata( $widget['voucher_hero_image'] );
		}

		return $widget;
	}
}
