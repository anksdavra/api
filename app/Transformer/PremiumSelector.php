<?php
declare(strict_types=1);

namespace CroissantApi\Transformer;

class PremiumSelector extends TransformerAbstract implements Transformer {

	public function apply_transformation( array $widget ) : array {
		$term = get_term_by('id', $widget['premium'], 'premium');
		$term_meta = get_term_meta( $term->term_id );
		$widget['premium'] = $term;
		$widget['premium']->renewal_type = $this->getRenewalType( $term_meta['renewal_type'][0] );
		$widget['premium']->what_is_included = get_field('what_is_included', $term->taxonomy . '_' . $term->term_id);
		$widget['premium']->price = get_field('package_price', $term->taxonomy . '_' . $term->term_id);
		$widget['premium']->hasDeliveryAddress = get_field('has_delivery_address', $term->taxonomy . '_' . $term->term_id);
		return $widget;
	}

	private function getRenewalType ( $renewalId ) : ?string {
		switch ( $renewalId ) {
			case 0:
				$renewalType = 'Monthly';
				break;
			case 1:
				$renewalType = 'Yearly';
				break;
			case 2:
				$renewalType = 'OneOff';
				break;
			case 3:
				$renewalType = 'Three Monthly';
				break;
			case 4:
				$renewalType = 'Six Monthly';
				break;
			default:
				$renewalType  = null;
		}

		return $renewalType;
	}
}
