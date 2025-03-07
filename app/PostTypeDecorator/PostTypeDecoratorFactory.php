<?php
declare(strict_types=1);

namespace CroissantApi\PostTypeDecorator;

use CroissantApi\Util\Factory;

class PostTypeDecoratorFactory {

	private static $instance;
	private $decorators = [];

	private function __construct() {
	}

	public static function get_instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new PostTypeDecoratorFactory();
		}

		return self::$instance;
	}

	public function create( $post_type ) : ?PostTypeDecorator {
		$class     = '';
		$post_type = str_replace( '_', '-', $post_type );
		foreach ( explode( '-', $post_type ) as $post_type_fragment ) {
			$class .= ucfirst( $post_type_fragment );
		}

		$class = 'CroissantApi\\PostTypeDecorator\\' . $class;
		if ( ! class_exists( $class ) ) {
			return null;
		}

		$factory = Factory::get_instance();
		if ( ! isset( $this->decorators[ $post_type ] ) ) {
			$this->decorators[ $post_type ] = new $class(
				$factory->create( 'Image' ),
				$factory->create( 'Post' )
			);
		}

		return $this->decorators[ $post_type ];
	}
}
