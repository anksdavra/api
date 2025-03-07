<?php
declare(strict_types=1);

namespace CroissantApi\Transformer;

class RelatedArticles extends TransformerAbstract implements Transformer {

	public function apply_transformation( array $widget ) : array {
		$return_posts = [];
		foreach ( $widget['posts'] as $post ) {
			if ( ! isset( $post['post'] ) || empty( $post['post'] ) ) {
				continue;
			}

			$expanded_post = $this->post_helper->get_post_object_simple_details( $post['post'] );

			empty( $post['post_title'] ) ?: $expanded_post['title']['rendered'] = $post['post_title'];
			$return_posts[] = (object) $expanded_post;
		}

		$widget['posts'] = $return_posts;

		return $widget;
	}
}
