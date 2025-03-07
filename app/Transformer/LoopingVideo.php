<?php
declare(strict_types=1);

namespace CroissantApi\Transformer;

class LoopingVideo extends TransformerAbstract implements Transformer {

	public function apply_transformation( $widget ) : array {
		$widget['placeholder'] = $this->image_helper->get_attachment_metadata( $widget['placeholder'] );

		if ( ! empty( $widget['mobile_placeholder'] ) ) {
			$widget['mobile_placeholder'] = $this->image_helper
				->get_attachment_metadata( $widget['mobile_placeholder'] );
		}

		$widget['width'] = $widget['width'] ? 'full' : 'medium';

		return $widget;
	}
}
