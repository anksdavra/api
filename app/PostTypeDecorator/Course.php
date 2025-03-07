<?php
declare(strict_types=1);

namespace CroissantApi\PostTypeDecorator;

class Course extends PostTypeDecoratorAbstract implements PostTypeDecorator {

	public function decorate_single( array $fields ) : array {

		unset( $fields['co_author'] );
		return $fields;
	}

	public function decorate_list( array $fields ) : array {

		$fields['acf']['course_description']     = get_field( 'course_description', $fields['id'] ) ?? '';
		$fields['acf']['course_duration']        = get_field( 'course_duration', $fields['id'] ) ?? '';
		$fields['acf']['course_price']           = get_field( 'course_price', $fields['id'] ) ?? '';
		$fields['acf']['course_publishing_date'] = get_field( 'course_publishing_date', $fields['id'] ) ?? '';
		$fields['acf']['course_category']        = get_field( 'course_category', $fields['id'] ) ?? '';
		$fields['acf']['course_button_text']     = get_field( 'course_button_text', $fields['id'] ) ?? '';
		$fields['acf']['course_button_link']     = get_field( 'course_button_link', $fields['id'] ) ?? '';
		$fields['acf']['course_excerpt']         = get_field( 'course_excerpt', $fields['id'] ) ?? '';
		$fields['acf']['class_type']             = get_field( 'class_type', $fields['id'] ) ?? '';
		$fields['acf']['recording_type']         = get_field( 'recording_type', $fields['id'] ) ?? '';

		//single module class should link straight to the video_post
		if ( $fields['acf']['class_type'] === 'Single module class' ) {
			$video_post_id  = get_field( 'widgets', $fields['id'] )[0]['widget_classes_picker_video_posts'][0]['video_post'] ?? false;
			$fields['link'] = $video_post_id ? get_permalink( $video_post_id ) : $fields['link'];
		}

		unset(
			$fields['acf']['sell'],
			$fields['acf']['series'],
			$fields['acf']['brand_logo'],
			$fields['acf']['review_rating']
		);

		return $fields;
	}
}
