<?php
declare(strict_types=1);

namespace CroissantApi\Transformer;

class NewsletterSignup extends TransformerAbstract implements Transformer {

	public function apply_transformation( array $widget ) : array {
		$widget = array_merge(
			$widget,
			$this->get_vertical_details( $widget['widget_newsletter_signup_vertical'] )
		);
		unset( $widget['widget_newsletter_signup_vertical'] );

		return $widget;
	}

	private function get_vertical_details( $term_id ) {
		$vertical       = get_term( $term_id, 'vertical' );
		$acf            = get_fields( $vertical );
		$telem_vertical = (int) get_term_meta( $acf['telemetry_vertical'], 'telemetry_vertical_id', true );

		$primary_colour = $acf['background_colour'] ?? '';
		if ( empty( $primary_colour ) ) {
			$primary_colour = $acf['primary_colour'];
		}

		$accent_colour = $acf['text_colour'] ?? '';
		if ( empty( $accent_colour ) ) {
			$accent_colour = $acf['accent_colour'];
		}

		return [
			'name'                  => $vertical->name,
			'parent'                => $vertical->parent !== 0 ? get_term( $vertical->parent )->slug : null,
			'slug'                  => $vertical->slug,
			'telemetry_vertical'    => $telem_vertical,
			'telemetry_vertical_id' => $telem_vertical,
			'success_message'       => $acf['success_message'],
			'sell'                  => $acf['sell'],
			'background_colour'     => $acf['background_colour'] ?? '',
			'text_colour'           => $acf['text_colour'] ?? '',
			'button_colour'         => $acf['button_colour'] ?? '',
			'button_text_colour'    => $acf['button_text_colour'] ?? '',
			'primary_colour'        => $primary_colour ?? '',
			'accent_colour'         => $accent_colour ?? '',
		];
	}
}
