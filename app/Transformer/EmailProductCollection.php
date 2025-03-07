<?php
declare(strict_types=1);

namespace CroissantApi\Transformer;

class EmailProductCollection extends ProductCarousel {

	public function apply_transformation( array $widget ) : array {
		$widget = parent::apply_transformation( $widget );

		if ( ! empty( $widget['header_image'] ) ) {
			$widget['header_image'] = $this->image_helper->get_attachment_metadata( $widget['header_image'] );
		}

		if ( ! empty( $widget['author_image'] ) ) {
			$widget['author_image'] = $this->image_helper->get_attachment_metadata( $widget['author_image'] );
		}

		return $widget;
	}
}
