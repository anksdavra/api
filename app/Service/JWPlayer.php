<?php

namespace CroissantApi\Service;

class JWPlayer {


	public function get_video_metadata( $post_id, $type ) {
		$metadata = get_post_meta( $post_id, $type . '_video_metadata', true );

		if ( empty( $metadata ) ) {
			return;
		}

		$pub_date = new \DateTime( '@' . $metadata['playlist'][0]['pubdate'], new \DateTimeZone( 'Europe/London' ) );
		$minutes  = (int) floor( $metadata['playlist'][0]['duration'] / 60 );
		$seconds  = $metadata['playlist'][0]['duration'] % 60;

		return [
			'name'          => $metadata['title'],
			'description'   => $metadata['description'],
			'thumbnail_url' => $metadata['playlist'][0]['image'],
			'upload_date'   => $pub_date->format( 'c' ),
			'content_url'   => $metadata['playlist'][0]['sources'][4]['file'],
			'duration'      => "PT{$minutes}M{$seconds}S",
		];
	}
}
