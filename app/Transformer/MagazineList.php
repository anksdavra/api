<?php
declare(strict_types=1);

namespace CroissantApi\Transformer;

class MagazineList extends TransformerAbstract implements Transformer {

	public function apply_transformation( array $widget ) : array {

		$params = [
			'post_type'      => 'issue',
			'orderby'        => 'date',
			'order'          => 'DESC',
			'posts_per_page' => 5,
		];

		$widget['magazine_list'] = $this->post_service->get_post_list( $params );

		return $widget;
	}
}
