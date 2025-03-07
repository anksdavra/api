<?php
declare(strict_types=1);

namespace CroissantApi\PostTypeDecorator;

class SponsoredQuizPost extends PostTypeDecoratorAbstract implements PostTypeDecorator {
	public function decorate_single( array $fields ) : array {
		$fields = $this->decorate_sponsor_images( $fields );
		$fields = $this->add_no_index_value( $fields );
		return $this->add_affiliate_links_notice( $fields );
	}
}
