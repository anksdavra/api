<?php
declare(strict_types=1);

namespace CroissantApi\Transformer;
abstract class TransformerAbstract {

	protected $image_helper;
	protected $post_helper;
	protected $post_service;

	public function __construct( $image_helper, $post_helper, $post_service ) {
		$this->image_helper = $image_helper;
		$this->post_helper  = $post_helper;
		$this->post_service = $post_service;
	}
}
