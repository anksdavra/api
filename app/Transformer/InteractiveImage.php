<?php
declare(strict_types=1);

namespace CroissantApi\Transformer;

class InteractiveImage extends TransformerAbstract implements Transformer {

	public function apply_transformation( array $widget ) : array {

		if ( $widget['layout_interaction'] === 'grid' ) {
			return $this->apply_grid_transformation( $widget );
		}

		if ( $widget['layout_interaction'] === 'parallax' && ! empty( $widget['image_collection_parallax'] ) ) {
			return $this->apply_parallax_transformation( $widget );
		}

		if ( ! empty( $widget['first_image'] ) ) {
			$widget['image_collection'][] = $this->image_helper->get_attachment_metadata( $widget['first_image'] );
		}

		if ( ! empty( $widget['second_image'] ) ) {
			$widget['image_collection'][] = $this->image_helper->get_attachment_metadata( $widget['second_image'] );
		}

		if ( ! empty( $widget['third_image'] ) ) {
			$widget['image_collection'][]= $this->image_helper->get_attachment_metadata( $widget['third_image'] );
		}

		unset( $widget['first_image'] );
		unset( $widget['second_image'] );
		unset( $widget['third_image'] );
		unset( $widget['image_collection_parallax'] );

		return $widget;
	}

	private function apply_grid_transformation( $widget ) {
		foreach ( $widget['image_collection'] as &$image ) {
			$image = $this->image_helper->get_attachment_metadata( $image['image'] );
		}
		unset( $widget['image_collection_parallax'] );

		return $widget;
	}

	private function apply_parallax_transformation( $widget ) {
		$widget['image_collection']        = [];
		$widget['mobile_image_collection'] = [];
		foreach ( $widget['image_collection_parallax'] as $image ) {
			$widget['image_collection'][]        = $this->image_helper->get_attachment_metadata( $image['image'] );
			$widget['mobile_image_collection'][] = $image['mobile_image'] ? $this->image_helper->get_attachment_metadata( $image['mobile_image'] ) : $this->image_helper->get_attachment_metadata( $image['image'] );
		}

		unset( $widget['image_collection_parallax'] );

		if ( ! empty( $widget['image_collection'] ) ) {
			unset( $widget['first_image'] );
			unset( $widget['second_image'] );
		}

		return $widget;
	}
}
