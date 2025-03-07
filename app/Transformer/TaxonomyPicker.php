<?php
declare(strict_types=1);

namespace CroissantApi\Transformer;

class TaxonomyPicker extends TransformerAbstract implements Transformer {
	
	public function apply_transformation( array $widget ) : array {

		$widget['quote_article'] = $widget['quote_article_option'] ? $this->post_helper->get_post_object_simple_details( get_post( $widget['quote_article'] ) ) : false;
		$widget                  = $this->get_posts_by_taxonomy( $widget );

		unset( $widget['taxonomy_option'] );
		unset( $widget['category_choice'] );
		unset( $widget['series_choice'] );
		unset( $widget['premium_choice'] );

		return $widget;
	}

	private function get_posts_by_taxonomy( $widget ) {

		$tax     = $widget['taxonomy_option'];

		if($tax == 'category') {
			$term_id = $widget['category_choice'];
		}

		if($tax == 'series') {
			$term_id = $widget['series_choice'];
		}

		if($tax == 'premium') {
			$term_id = $widget['premium_choice'];
		}

		$term    = get_term( $term_id, $tax );

		$params = [
			'post_type'      => [ 'post', 'longform', 'sponsored_post', 'sponsored_longform', 'quiz', 'sponsored_quiz', 'video_post' ],
			'posts_per_page' => 11,
		    'tax_query'      => [
					'relation' => 'AND',
					[
						'taxonomy' => $tax,
						'field'    => 'slug',
						'terms'    => [
							$term->slug,
						],
					],
					[
						'taxonomy' => 'visibility',
						'operator' => 'NOT EXISTS',
					],
				],
		];

		$posts                     = $this->post_service->get_post_list( $params );
		$widget['latest_articles'] = $posts;
		$widget['term_name']       = $term->name;

		return $widget;
	}
}
