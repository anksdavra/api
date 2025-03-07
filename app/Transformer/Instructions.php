<?php
declare(strict_types=1);

namespace CroissantApi\Transformer;

class Instructions extends TransformerAbstract implements Transformer {

	public function apply_transformation( array $widget ) : array {

		for ( $i = 0; $i < count( $widget['steps'] ); $i++ ) {
			$step                = &$widget['steps'][ $i ];
			$step['step_number'] = $i + 1;

			if ( $step['image'] ) {
				$step['image'] = $this->image_helper->get_attachment_metadata( $step['image'] );
			}
		}
		return $widget;
	}
}
