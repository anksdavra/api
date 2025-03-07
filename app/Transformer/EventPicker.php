<?php
declare(strict_types=1);

namespace CroissantApi\Transformer;

class EventPicker extends TransformerAbstract implements Transformer {

	public function apply_transformation( array $widget ) : array {

		$widget['event_image'] = $this->image_helper->get_attachment_metadata( $widget['event_image'] );
		$widget                = $this->get_event_details( $widget );
		return $widget;
	}

	private function get_event_details( $widget ) {

		$term = get_term( $widget['event'], 'premium' ) ?? false;

		if ( ! $term ) {
			return $widget;
		}

		$widget['title']           = $term->name;
		$widget['description']     = $term->description;
		$widget['button_link']     = get_field( 'link_url', $term->taxonomy . '_' . $term->term_id ) ?? false;
		$widget['event_starts_at'] = get_field( 'event_starts_at', $term->taxonomy . '_' . $term->term_id ) ?? false;
		unset( $widget['event'] );

		return $widget;
	}
}
