<?php
declare(strict_types=1);

namespace CroissantApi\Transformer;

class AuthorImage extends TransformerAbstract implements Transformer {

	public function apply_transformation( array $widget ) : array {
		if ( ! empty( $widget['image'] ) ) {
			$widget['image']      = $this->image_helper->get_attachment_metadata( $widget['image'] );
			$widget['image_crop'] = 'square';

			$hide_key = $widget['hide_widget_from_page'];
			unset( $widget['hide_widget_from_page'] );
			unset( $widget['disclaimer'] );
			$widget['hide_widget_from_page'] = $hide_key;
		}
		return $widget;
	}
}
