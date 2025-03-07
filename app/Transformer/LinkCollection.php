<?php
declare(strict_types=1);

namespace CroissantApi\Transformer;

class LinkCollection extends TransformerAbstract implements Transformer {

	public function apply_transformation( array $widget ) : array {
		$widget['posts'] = $this->get_links( $widget );

		return $widget;
	}

	private function get_links( $widget ) {

		$links = [];

		if ( ! $widget['posts'] ) {
			return $links;
		}

		foreach ( $widget['posts'] as $link ) {
			$post = [
				'headline' => get_field( 'short_headline', $link->ID ),
				'link'     => get_permalink( $link->ID ),
			];

			if ( $widget['expand_posts'] === true ) {
				$hero_image = ( get_field( 'hero_images', $link->ID ) )[0];

				if ( is_numeric( $hero_image ) ) {
					$hero_image = $this->image_helper->get_attachment_metadata( $hero_image );
				}

				$post['image']         = $hero_image;
				$post['sponsor_link']  = get_field( 'sponsor_link', $link->ID );
				$post['sponsor_name']  = get_field( 'sponsor_name', $link->ID );
				$post['sponsor_label'] = get_field( 'sponsor_label', $link->ID );
			}

			$links[] = $post;
		}

		return $links;
	}
}
