<?php
declare(strict_types=1);

namespace CroissantApi\Util;

class Factory {

	private static $instance;

	public $instances;

	private function __construct() {
	}

	public static function get_instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new Factory();
		}

		return self::$instance;
	}

	public function create( $class ) : ?Util {
		$class = 'CroissantApi\\Util\\' . $class;

		if ( ! isset( $this->instances[ $class ] ) ) {
			$this->instances[ $class ] = new $class();
		}

		return $this->instances[ $class ];
	}
}
