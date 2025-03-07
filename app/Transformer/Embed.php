<?php
declare(strict_types=1);

namespace CroissantApi\Transformer;

class Embed extends TransformerAbstract implements Transformer {

	public function apply_transformation( array $widget ) : array {
		if ( ! $widget['autoplay'] ) {
			return $widget;
		}

		preg_match(
			'/https:\/\/embeds.stylist.co.uk\/(.*?)"/i',
			$widget['embed'],
			$matches
		);

		$url          = parse_url( str_replace( '"', '', $matches[0] ) );
		$url['query'] = isset( $url['query'] ) ? $url['query'] . '&autoplay=1&mute=1' : 'autoplay=1&mute=1';

		$widget['embed'] = preg_replace(
			'/https:\/\/embeds.stylist.co.uk\/(.*?)"/i',
			'https://embeds.stylist.co.uk' . $url['path'] . '?' . $url['query'] . '"',
			$widget['embed']
		);

		if ( ! empty( $widget['mobile_embed'] ) ) {
			preg_match(
				'/https:\/\/embeds.stylist.co.uk\/(.*?)"/i',
				$widget['mobile_embed'],
				$matches
			);

			$url          = parse_url( str_replace( '"', '', $matches[0] ) );
			$url['query'] = isset( $url['query'] ) ? $url['query'] . '&autoplay=1&mute=1' : 'autoplay=1&mute=1';

			$widget['mobile_embed'] = preg_replace(
				'/https:\/\/embeds.stylist.co.uk\/(.*?)"/i',
				'https://embeds.stylist.co.uk' . $url['path'] . '?' . $url['query'] . '"',
				$widget['mobile_embed']
			);
		}

		return $widget;
	}
}
