<?php
declare(strict_types=1);

namespace CroissantApi\Transformer;

class Listicle extends TransformerAbstract implements Transformer {

	public function apply_transformation( array $widget ) : array {

		if ( empty( $widget['item'] ) ) {
			return $widget;
		}

		foreach ( $widget['item'] as &$item ) {
			$factory = TransformerFactory::get_instance();

			$type = $item['media_type'];
			if ( $item['media_type'] === 'loop' ) {
				$type = 'looping_video';
			}

			$transformer = $factory->create( $type, $this->post_service );
			$item        = $transformer->apply_transformation( $item );

			if ( $item['width'] === true ) {
				$item['width'] = 'full';
			}

			if ( $this->is_longform() && $item['width'] !== 'full' ) {
				$item['width'] = 'medium';
			}
		}

		return $widget;
	}

	private function is_longform() : bool {
		$post_type = get_post_type(
			substr(
				$_SERVER['REQUEST_URI'],
				strrpos( $_SERVER['REQUEST_URI'], '/' ) + 1
			)
		);

		return $post_type === 'longform' || $post_type === 'sponsored_longform';
	}
}
