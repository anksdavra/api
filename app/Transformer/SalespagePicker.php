<?php
declare(strict_types=1);

namespace CroissantApi\Transformer;

class SalespagePicker extends TransformerAbstract implements Transformer {

	public function apply_transformation( array $widget ) : array {

		$widget['salespage'] = $this->post_helper->get_post_object_simple_details( get_post( $widget['salespage'] ) ) ?? false;
		return $widget;
	}
}
