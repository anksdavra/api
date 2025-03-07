<?php
declare(strict_types=1);

namespace CroissantApi\Transformer;

class PackageCard extends TransformerAbstract implements Transformer {

	public function apply_transformation( array $widget ) : array {

		$widget['package_item'] = $widget['item'];

		foreach($widget['package_item'] as &$item) {
			$term = get_term_by('id', $item['card_tag'], 'premium');
			$term_meta = get_term_meta($term->term_id);
			$item['card_tag'] = $term;
			$item['card_tag']->renewal_type = $this->getRenewalType($term_meta['renewal_type'][0]);
			$item['card_tag']->what_is_included = get_field('what_is_included', $term->taxonomy . '_' . $term->term_id);
			$item['card_tag']->hasDeliveryAddress = get_field('has_delivery_address', $term->taxonomy . '_' . $term->term_id);
		}
		unset($widget['item']);
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
