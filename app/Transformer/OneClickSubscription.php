<?php
declare(strict_types=1);

namespace CroissantApi\Transformer;

class OneClickSubscription extends TransformerAbstract implements Transformer {

	public function apply_transformation( array $widget ) : array {

		// Initialises the array that will hold the repeater fields
		$verticals = [];

		foreach ( $widget['vertical']  as $vertical ) {

			$widget_overwritten = [
				$vertical['sell'],
				$vertical['thumbnail_image'],
				$vertical['button_text'],
			];

			$verticals[] = $this->get_vertical_details( $vertical['widget_one_click_subscription_vertical']->term_id, $widget_overwritten );
		}

		$results = [
			'acf_fc_layout'         => $widget['acf_fc_layout'],
			'title'                 => 'One Click Subscription Widget',
			'layout_heading'        => $widget['title'] ?? '',
			'layout_description'    => $widget['description'],
			'verticals'             => $verticals,
			'hide_widget_from_page' => $widget['hide_widget_from_page'],
		];

		return $results;
	}

	// Short method that checks for the length of a string stripped of html tags.
	private function check_for_empty_strings( string $string ) : bool {

		$content_no_tags = strip_tags( $string );
		$clean_string    = str_replace( '&nbsp;', '', $content_no_tags );
		$content_length  = strlen( trim( $clean_string ) );

		if ( $content_length > 0 ) {
			return true;
		}
		return false;
	}

	private function get_vertical_details( int $term_id, array $widget_overwritten ) : array {

		[ $overwritten_sell, $overwritten_thumbnail, $overwritten_button_text ] = $widget_overwritten;
		$vertical       = (object) get_term( $term_id, 'vertical' );
		$acf            = (array) get_fields( $vertical );
		$telem_vertical = (int) get_term_meta( $acf['telemetry_vertical'], 'telemetry_vertical_id', true );

		$sell        = $this->check_for_empty_strings( $overwritten_sell ) ? $overwritten_sell : $acf['sell'];
		$thumbnail   = $overwritten_thumbnail ? $overwritten_thumbnail : $acf['thumbnail_image'];
		$button_text = $overwritten_button_text ? $overwritten_button_text : 'Sign Up Now';

		return [
			'name'            => $vertical->name ?? '',
			'slug'            => $vertical->slug ?? '',
			'sell'            => $sell ?? '',
			'vertical_id'     => $telem_vertical ?? 0,
			'thumbnail_image' => $this->image_helper->get_attachment_metadata( $thumbnail ) ?? [],
			'button_text'     => $button_text,
		];
	}
}
