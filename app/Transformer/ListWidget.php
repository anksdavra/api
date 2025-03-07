<?php
declare(strict_types=1);

namespace CroissantApi\Transformer;

class ListWidget extends TransformerAbstract implements Transformer {

	public function apply_transformation( array $widget ) : array {

		$list_types                   = [ 'ingredients', 'review', 'equipment', 'generic' ];
		$transformed                  = [];
		$transformed['acf_fc_layout'] = 'list';
		$transformed['layout']        = $widget['list_type'];

		foreach ( $list_types as $type ) {
			if ( $widget['list_type'] == $type ) {
				$method_name = 'get_' . $type . '_fields';
				$transformed = $this->$method_name( $transformed, $widget );
				break;
			}
		}

		$transformed['hide_widget_from_page'] = $widget['hide_widget_from_page'];
		$transformed['publish_to_apple_news'] = $widget['publish_to_apple_news'];
		return $transformed;
	}

	private function get_ingredients_fields( array &$transformed, array $widget ): array  {
		$transformed['title']    = 'Ingredients';
		$transformed['contents'] = $widget['ingredients'];
		return $transformed;
	}

	private function get_review_fields( array &$transformed, array $widget ): array  {
		$transformed['positives'] = [
			'title'       => 'What we Loved',
			'notes' => $widget['good_points'],
		];

		$transformed['negatives'] = [
			'title'      => 'What we didn\'t Love',
			'notes' => $widget['bad_points'],
		];

		return $transformed;
	}

	private function get_equipment_fields( array &$transformed, array $widget ): array  {
		$transformed['title']    = 'Equipment';
		$transformed['contents'] = $widget['equipment'];
		return $transformed;
	}

	private function get_generic_fields( array &$transformed, array $widget ): array  {
		$transformed['title']    = $widget['heading'];
		$transformed['contents'] = $widget['generic'];
		return $transformed;
	}
}
