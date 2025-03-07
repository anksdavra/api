<?php
declare(strict_types=1);

namespace CroissantApi\Transformer;

class CampaignStats extends TransformerAbstract implements Transformer {

	public function apply_transformation( array $widget ) : array {

		if ( ! empty( $widget['campaign_image'] ) ) {
			$widget['campaign_image'] = $this->image_helper->get_attachment_metadata( $widget['campaign_image'] );
		}

		return $widget;
	}
}
