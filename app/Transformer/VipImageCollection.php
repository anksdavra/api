<?php
declare(strict_types=1);

namespace CroissantApi\Transformer;

class VipImageCollection extends TransformerAbstract implements Transformer {

	public function apply_transformation( array $widget ) : array {
		$placeholder_id = null;
		if ( $this->image_helper->placeholder ) {
			$placeholder_id = \CroissantApi\Util\Image::$PLACEHOLDER_IMAGE_ID;
		}

		if ( ! empty( $widget['vip_image_mobile'] ) ) {
			$widget['vip_image_mobile'] = $this->image_helper->get_attachment_metadata( $placeholder_id ?: $widget['vip_image_mobile'] );
		}

		if ( ! empty( $widget['vip_image_tablet'] ) ) {
			$widget['vip_image_tablet'] = $this->image_helper->get_attachment_metadata( $placeholder_id ?: $widget['vip_image_tablet'] );
		}

		if ( ! empty( $widget['vip_image_desktop'] ) ) {
			$widget['vip_image_desktop'] = $this->image_helper->get_attachment_metadata( $placeholder_id ?: $widget['vip_image_desktop'] );
		}

		return $widget;
	}
}
