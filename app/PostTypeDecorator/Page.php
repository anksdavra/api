<?php
declare(strict_types=1);

namespace CroissantApi\PostTypeDecorator;

class Page extends PostTypeDecoratorAbstract implements PostTypeDecorator {

	public function decorate_single( array $fields ) : array {
		unset(
			$fields['tagbot_data'],
			$fields['series'],
			$fields['co_author'],
			$fields['premium'],
			$fields['package_ids'],
			$fields['affiliate_links'],
		);

		return $fields;
	}
}
