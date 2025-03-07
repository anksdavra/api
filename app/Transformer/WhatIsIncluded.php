<?php
declare(strict_types=1);

namespace CroissantApi\Transformer;

class WhatIsIncluded extends TransformerAbstract implements Transformer {

	public function apply_transformation( array $widget ) : array {
		$i=0;
		foreach($widget['benefits'] as $benefit) {
			if ( ! empty( $benefit['image'] ) ) {
				$benefitImage = $this->image_helper->get_attachment_metadata( $benefit['image'] );
				$widget['benefits'][$i]['image'] = $benefitImage;
			}
			$i++;
		}
		return $widget;
	}
}
