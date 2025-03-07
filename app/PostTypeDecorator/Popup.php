<?php
declare(strict_types=1);

namespace CroissantApi\PostTypeDecorator;

use CroissantApi\Transformer\TransformerFactory;

class Popup extends PostTypeDecoratorAbstract implements PostTypeDecorator {

	public function decorate_list( array $fields ) : array {
		$fields['acf']['popup_layout']        = get_field( 'popup_select_layout', $fields['id'] );

		if($fields['acf']['popup_layout'] == 'vertical') {
			$fields['acf']['popup_text'] = get_field('popup_text', $fields['id']);
			$fields['acf']['popup_logo'] = get_field('popup_logo', $fields['id']) ? $this->image_helper->get_attachment_metadata(get_field('popup_logo', $fields['id'])) : [];
			$fields['acf']['popup_image'] = get_field('popup_image', $fields['id']) ? $this->image_helper->get_attachment_metadata(get_field('popup_image', $fields['id'])) : [];
			$fields['acf']['popup_background_colour'] = get_field('popup_background_colour', $fields['id']);
			$fields['acf']['popup_text_colour'] = get_field('popup_text_colour', $fields['id']);
			$fields['acf']['popup_button_colour'] = get_field('popup_button_colour', $fields['id']);
			$fields['acf']['popup_button_text_colour'] = get_field('popup_button_text_colour', $fields['id']);
			$fields['acf']['popup_button_text'] = get_field('popup_button_text', $fields['id']);
			$fields['acf']['popup_button_link'] = get_field('popup_button_link', $fields['id']);
			$fields['acf']['external_url'] = get_field('external_url', $fields['id']);
			$fields['acf']['email_capture'] = get_field('popup_email_capture', $fields['id']);
		}

		if ($fields['acf']['popup_layout'] == 'vertical') {
			if($fields['acf']['email_capture'] == 1) {
				$telemetry_data = $this->get_telemetry_vertical_id($fields['id'], $fields['acf']['popup_layout']);
				$fields['acf']['telemetry_vertical_id'] = $telemetry_data[0]['id'];
				$fields['acf']['telemetry_vertical_name'] = $telemetry_data[0]['name'];
				$fields['acf']['success_message'] = get_field('popup_email_capture_success_message', $fields['id']);
				$fields['acf']['duplicate_email_message'] = get_field('popup_email_capture_duplicate_message', $fields['id']);
			}
		}

		if($fields['acf']['popup_layout'] == 'full-screen') {
			$telemetry_data = $this->get_telemetry_vertical_id( $fields['id'], $fields['acf']['popup_layout'] );
			$fields['acf']['telemetry_vertical_id']   = $telemetry_data[0]['id'];
			$fields['acf']['telemetry_vertical_name']   = $telemetry_data[0]['name'];
			$fields['acf']['description'] = $telemetry_data[0]['description'];
			$fields['acf']['success_message']   = $telemetry_data[0]['success_message'];
			$fields['acf']['logo_svg']   = $telemetry_data[0]['logo_svg'];
			$fields['acf']['popup_background_colour']   = $telemetry_data[0]['popup_background_colour'];
			$fields['acf']['popup_text_colour']   = $telemetry_data[0]['popup_text_colour'];
			$fields['acf']['popup_button_colour']   = $telemetry_data[0]['popup_button_colour'];
			$fields['acf']['popup_button_text_colour']   = $telemetry_data[0]['popup_button_text_colour'];
		}

		unset(
			$fields['acf']['sell'],
			$fields['acf']['category'],
			$fields['acf']['series'],
			$fields['acf']['fullscreen_hero'],
			$fields['acf']['brand_logo'],
			$fields['acf']['review_rating'],
			$fields['acf']['hero_images'],
			$fields['acf']['package_ids'],
			$fields['acf']['short_headline'],
			$fields['_embedded'],
			$fields['sticky']
		);

		return $fields;
	}

	public function decorate_single( array $fields ) : array {

		$popUpId = (int) $_GET['p'];
		$fields['popup_layout'] = $fields['popup_select_layout'];
		$telemetry_data = $this->get_telemetry_vertical_id($popUpId, $fields['popup_select_layout']);
		if($fields['popup_select_layout'] == 'vertical') {
			$fields['popup_logo'] = $fields['popup_logo'] ? $this->image_helper->get_attachment_metadata($fields['popup_logo']) : [];
			$fields['popup_image'] = $fields['popup_image'] ? $this->image_helper->get_attachment_metadata($fields['popup_image']) : [];
			$fields['email_capture'] = $fields['popup_email_capture'];
			$fields['telemetry_vertical_id'] = $telemetry_data[0]['id'];
			$fields['telemetry_vertical_name'] = $telemetry_data[0]['name'];
			$fields['success_message'] = $fields['popup_email_capture_success_message'];
			$fields['duplicate_email_message'] = $fields['popup_email_capture_duplicate_message'];
		}
		if($fields['popup_select_layout'] == 'full-screen') {
			$fields['description'] = $telemetry_data[0]['description'];
			$fields['telemetry_vertical_id'] = $telemetry_data[0]['id'];
			$fields['telemetry_vertical_name'] = $telemetry_data[0]['name'];
			$fields['success_message']   = $telemetry_data[0]['success_message'];
			$fields['logo_svg']   = $telemetry_data[0]['logo_svg'];
			$fields['popup_background_colour'] = $telemetry_data[0]['popup_background_colour'];
			$fields['popup_text_colour'] = $telemetry_data[0]['popup_text_colour'];
			$fields['popup_button_colour'] = $telemetry_data[0]['popup_button_colour'];
			$fields['popup_button_text_colour'] = $telemetry_data[0]['popup_button_text_colour'];
		}

		unset(
			$fields['widget_newsletter_signup_vertical'],
			$fields['popup_email_capture'],
			$fields['popup_select_layout'],
			$fields['popup_email_capture_success_message'],
			$fields['popup_email_capture_duplicate_message']
		);

		return $fields;
	}

	private function generate_segment_ids( $tax_objects ) {

		if ( ! $tax_objects ) {
			return [];
		}

		$permutive_ids = [];

		foreach ( $tax_objects as $tax ) {

			if ( isset( $tax->taxonomy ) && isset( $tax->term_id ) ) {
				$permutive_ids[] = get_field( 'segment_id', $tax->taxonomy . '_' . $tax->term_id );
			}
		}
		return $permutive_ids;
	}

	private function generate_taxonomy_slugs( $tax_objects ) {

		if ( ! $tax_objects ) {
			return false;
		}

		$slugs = [];

		foreach ( $tax_objects as $tax ) {

			if ( $tax->slug ) {
				$slugs[] = $tax->slug;
			}
		}
		return $slugs;
	}

	private function get_telemetry_vertical_id( $popup_id , $layout) {

		$term_id  = get_field( 'popup_widget_newsletter_signup_vertical', $popup_id );
		$vertical = get_term( $term_id, 'vertical' );
		$acf      = get_fields( $vertical );

		if ( ! isset( $acf['telemetry_vertical'] ) ) {
			return false;
		}

		$acf_vertical   = (int) $acf['telemetry_vertical'];
		$term_name = get_term( $acf_vertical )->name;
		$telem_vertical = get_term_meta( $acf_vertical, 'telemetry_vertical_id', true ) ?? false;

        if($layout == 'vertical') {
			$data[] = [
				'id' => $telem_vertical,
				'name' => $term_name
			];
		} else {
			$data[] = [
				'id' => $telem_vertical,
				'name' => $term_name,
				'description' => $vertical->description,
				'success_message' => $acf['success_message'],
				'logo_svg' => $acf['logo_svg'],
				'popup_background_colour' => $acf['background_colour'],
				'popup_text_colour' => $acf['text_colour'],
				'popup_button_colour' => $acf['button_colour'],
				'popup_button_text_colour' => $acf['button_text_colour']
			];
		}
		return $data;
	}
}
