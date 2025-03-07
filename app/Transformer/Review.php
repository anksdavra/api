<?php
declare(strict_types=1);

namespace CroissantApi\Transformer;

class Review extends TransformerAbstract implements Transformer {

	public function apply_transformation( array $widget ) : array {
		$review_types                 = [ 'movie', 'series', 'product', 'restaurant', 'class', 'book' ];
		$transformed                  = [];
		$transformed['acf_fc_layout'] = 'review';
		$transformed['layout']        = $widget['review_type'];
		$transformed['item_name']     = $widget['item_name'];
		$transformed['rating']        = $widget['rating'];

		foreach ( $review_types as $review ) {
			if ( $widget['review_type'] == $review ) {
				$method_name = 'get_' . $review . '_fields';
				$transformed = $this->$method_name( $transformed, $widget );
				break;
			}
		}

		if ( isset( $widget['free_text'] ) && $widget['free_text'] ) {
			$transformed['free_text']         = true;
			$transformed['free_text_title']   = $widget['free_text_title'];
			$transformed['free_text_content'] = $widget['free_text_content'];
		}
		$transformed['hide_widget_from_page'] = $widget['hide_widget_from_page'];
		return $transformed;
	}

	private function get_movie_fields( array &$transformed, array $widget ) : array {
		return $this->get_movie_or_series_fields( $transformed, $widget );
	}
	private function get_series_fields( array &$transformed, array $widget ) : array {
		return $this->get_movie_or_series_fields( $transformed, $widget );
	}

	private function get_product_fields( array &$transformed, array $widget ): array  {
		$transformed['cost'] = $widget['cost'];
		return $transformed;
	}

	private function get_restaurant_fields( array &$transformed, array $widget ): array  {
		$transformed['cuisine'] = $widget['cuisine'];
		return $transformed;
	}

	private function get_class_fields( array &$transformed, array $widget ): array  {
		return $transformed;
	}

	private function get_book_fields( array &$transformed, array $widget ): array  {
		$transformed['author'] = $widget['author'];
		return $transformed;
	}

	private function get_movie_or_series_fields( array &$transformed, array $widget ): array  {
		$transformed['broadcaster']   = $widget['broadcaster'];
		$transformed['director']      = $widget['director'];
		$transformed['release_date']  = $widget['release_date'];
		$transformed['genre']         = $widget['genre'];
		$transformed['duration']      = $widget['duration'];
		$transformed['duration_unit'] = $widget['duration_unit'];
		return $transformed;
	}
}
