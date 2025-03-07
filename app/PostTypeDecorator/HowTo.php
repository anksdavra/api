<?php
declare(strict_types=1);

namespace CroissantApi\PostTypeDecorator;

class HowTo extends PostTypeDecoratorAbstract implements PostTypeDecorator {

	public function decorate_list( array $fields ) : array {

		$image_id = get_field(
			'how_to_details_placeholder_image',
			$fields['id']
		);

		$fields['acf']['hero_images'][0] =
			$this->image_helper->get_attachment_metadata( $image_id );
		$fields['acf']['video_url']      = get_field(
			'how_to_details_video_url',
			$fields['id']
		);

		$fields['muscle_group']  = get_field( 'conditional_muscle_group', $fields['id'] ) ?? [];
		$fields['exercise_type'] = get_field( 'conditional_exercise_type', $fields['id'] ) ?? [];

		unset(
			$fields['acf']['sell'],
			$fields['acf']['category'],
			$fields['acf']['series'],
			$fields['acf']['fullscreen_hero'],
			$fields['acf']['brand_logo'],
			$fields['acf']['review_rating'],
			$fields['conditional_muscle_group'],
			$fields['conditional_exercise_type']
		);

		return $fields;
	}
}
