<?php
declare(strict_types=1);

namespace CroissantApi\PostTypeDecorator;

class Salespage extends PostTypeDecoratorAbstract implements PostTypeDecorator {

	public function decorate_single( array $fields ) : array {
		$fields                     = $this->check_schema_json( $fields );
		$fields['salespage_banner'] = $this->image_helper->get_attachment_metadata( $fields['salespage_banner'] ) ?? [];
		$fields['short_headline'] =  $fields['short_headline'] ?? '';
		unset( $fields['co_author'] );
		$fields = $this->add_no_index_value( $fields );
		$fields = $this->add_affiliate_links_notice( $fields );
		return $this->decorate_sponsor_images( $fields );
	}

	public function decorate_list( array $fields ) : array {

		$image_id = get_field(
			'salespage_banner',
			$fields['id']
		);

		$fields['acf']['salespage_banner'] = $this->image_helper->get_attachment_metadata( $image_id );
		$fields['acf']['short_headline']     = get_field( 'short_headline', $fields['id'] ) ?? '';
		$fields['acf']['course_description']     = get_field( 'course_description', $fields['id'] ) ?? '';
		$fields['acf']['course_duration']        = get_field( 'course_duration', $fields['id'] ) ?? '';
		$fields['acf']['course_price']           = get_field( 'course_price', $fields['id'] ) ?? '';
		$fields['acf']['course_publishing_date'] = get_field( 'course_publishing_date', $fields['id'] ) ?? '';
		$fields['acf']['course_category']        = get_field( 'course_category', $fields['id'] ) ?? '';

		unset(
			$fields['acf']['sell'],
			$fields['acf']['category'],
			$fields['acf']['series'],
			$fields['acf']['fullscreen_hero'],
			$fields['acf']['brand_logo'],
			$fields['acf']['review_rating'],
			$fields['acf']['hero_images'],
			$fields['acf']['package_ids']
		);

		return $fields;
	}
}
