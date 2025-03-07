<?php
declare(strict_types=1);

namespace CroissantApi\Transformer;

class EmailPostCollection extends TransformerAbstract implements Transformer {

	public function apply_transformation( array $widget ) : array {
		foreach ( $widget['posts'] as &$post ) {
			if ( ! empty( $post['post'] ) ) {
				$post['post'] = $this->post_helper->get_post_object_simple_details( get_post( $post['post'] ) );
			}

			if ( ! empty( $post['image'] ) ) {
				$post['image'] = $this->image_helper->get_attachment_metadata( $post['image'] );
			}
		}

		return $widget;
	}
}
