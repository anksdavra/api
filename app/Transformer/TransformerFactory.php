<?php
declare(strict_types=1);

namespace CroissantApi\Transformer;

use CroissantApi\Util\Factory;
use CroissantApi\Service\CroissantPostService;

class TransformerFactory {

	private static $instance;
	public $transformers = [];

	private function __construct() {
	}

	public static function get_instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new TransformerFactory();
		}

		return self::$instance;
	}

	public function create( $widget_name, CroissantPostService $post_service ) : ?Transformer {

		$class       = '';
		$widget_name = str_replace( '_', '-', $widget_name );
		foreach ( explode( '-', $widget_name ) as $widget_name_fragment ) {
			$class .= ucfirst( $widget_name_fragment );
		}

		$class = 'CroissantApi\\Transformer\\' . $class;
		if ( ! class_exists( $class ) ) {
			return null;
		}

		$factory = Factory::get_instance();

		if ( ! isset( $this->transformers[ $widget_name ] ) ) {
			$this->transformers[ $widget_name ] = new $class(
				$factory->create( 'Image' ),
				$factory->create( 'Post' ),
				$post_service,
			);
		}

		return $this->transformers[ $widget_name ];
	}
}
