<?php
declare(strict_types=1);

namespace CroissantApi\PostTypeDecorator;

class Longform extends PostTypeDecoratorAbstract implements PostTypeDecorator {
	public function decorate_single( array $fields ) : array {

		if ( ! $fields['review_add_details'] ) {
			unset(
				$fields['review_type'],
				$fields['review_name'],
				$fields['review_rating'],
				$fields['release_date'],
				$fields['bechdel_test']
			);
		}
		$fields = $this->add_no_index_value( $fields );
		return $this->add_affiliate_links_notice( $fields );
	}
}
