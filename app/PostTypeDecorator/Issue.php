<?php
declare(strict_types=1);

namespace CroissantApi\PostTypeDecorator;

class Issue extends PostTypeDecoratorAbstract implements PostTypeDecorator {

	private $canvasflow_url = 'https://stylist.canvasflow.io/issues/';
	public function decorate_single( array $fields ) : array {

		$fields['issue_cover']          = $this->image_helper->get_attachment_metadata( $fields['issue_cover'] ) ?? [];
		$fields['hero_images']          = $fields['issue_cover'];
		$fields['issue_canvasflow_url'] = $fields['issue_canvasflow_id'] > 0 ? $this->canvasflow_url . strval( $fields['issue_canvasflow_id'] ) : '' ?? '';
		$fields['package_ids']          = $fields['package_ids'] ?? [];
		$fields['package_id']           = $fields['single_issue_premium'] ? $this->generate_single_package_id( $fields['single_issue_premium'] ) : '';
		unset( $fields['issue_canvasflow_id'] );
		unset( $fields['single_issue_premium'] );
		return $fields;
	}

	private function generate_single_package_id( $cms_sub ) {

		$cms_sub_id = $cms_sub->term_id ?? false;

		if ( ! $cms_sub_id ) {
			return '';
		}

		return get_term_meta( $cms_sub_id, 'telemetry_subscription_id', true ) ?? '';
	}

	public function decorate_list( array $fields ) : array {

		$cms_sub                     = get_field( 'single_issue_premium', $fields['id'] );
		$fields['acf']['package_id'] = $cms_sub ? $this->generate_single_package_id( $cms_sub ) : '';

		$image_id                     = get_field(
			'issue_cover',
			$fields['id']
		);
		$fields['acf']['issue_cover'] =
			$this->image_helper->get_attachment_metadata( $image_id );

		$fields['acf']['issue_description']    = get_field( 'issue_description', $fields['id'] ) ?? '';
		$canvasflow_id                         = get_field( 'issue_canvasflow_id', $fields['id'] );
		$fields['acf']['issue_canvasflow_url'] = $canvasflow_id > 0 ? $this->canvasflow_url . strval( $canvasflow_id ) : '' ?? '';
		$fields['acf']['issue_price']          = get_field( 'issue_price', $fields['id'] ) ?? '';
		$fields['acf']['publishing_date']      = get_field( 'issue_publishing_date', $fields['id'] ) ?? '';
		$fields['acf']['hero_images'] = $fields['acf']['issue_cover'];

		unset(
			$fields['acf']['sell'],
			$fields['acf']['category'],
			$fields['acf']['series'],
			$fields['acf']['fullscreen_hero'],
			$fields['acf']['brand_logo'],
			$fields['acf']['review_rating']
		);

		return $fields;
	}
}
