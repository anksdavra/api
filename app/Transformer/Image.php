<?php
declare(strict_types=1);

namespace CroissantApi\Transformer;

class Image extends TransformerAbstract implements Transformer {

	public function apply_transformation( array $widget ) : array {
		$placeholder_id = null;
		if ( $this->image_helper->placeholder ) {
			$placeholder_id = \CroissantApi\Util\Image::$PLACEHOLDER_IMAGE_ID;

			if ( ! empty( $widget['link'] ) ) {
				$widget['link'] = '';
			}
		}

		$widget['image'] = $this->image_helper->get_attachment_metadata( $placeholder_id ?: $widget['image'] );

		if ( ! empty( $widget['mobile_image'] ) ) {
			$widget['mobile_image'] = $this->image_helper->get_attachment_metadata( $placeholder_id ?: $widget['mobile_image'] );
		}

		return $widget;
	}
}
