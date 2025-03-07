<?php
declare(strict_types=1);

namespace CroissantApi\PostTypeDecorator;

class VideoPost extends PostTypeDecoratorAbstract implements PostTypeDecorator {

	public function decorate_single( array $fields ) : array {

			$fields['video_sidebar_heading'] = $fields['video_sidebar_heading'] ? $fields['video_sidebar_heading'] : 'More Videos';
			$fields['next_videos']           = $this->get_next_videos( $fields['next_videos'] );
			$fields['video_id']              = $this->get_video_id( $fields );
		    $fields['exercise_type'] = $fields['exercise_type'] ?? null;


		if ( isset( $fields['is_live_video'] ) && $fields['is_live_video'] === true ) {
			$fields['is_live_video'] = true;
			$fields['video_starting_datetime'] = date( 'Y-m-d', strtotime( $fields['video_live_date'] ) ) . ' ' . date( 'H:i:s', strtotime( $fields['video_start_time'] ) );
			$fields['video_ending_datetime']   = date( 'Y-m-d', strtotime( $fields['video_live_date'] ) ) . ' ' . date( 'H:i:s', strtotime( $fields['video_end_time'] ) );
		}
		else{
			$fields['is_live_video'] = false;
		}
		$fields = $this->add_no_index_value( $fields );
		$fields = $this->add_affiliate_links_notice( $fields );
		return $this->decorate_sponsor_images( $fields );
	}

	public function decorate_list( array $fields ) : array {

		if ( ! get_field( 'is_live_video', $fields['id'] ) ) {

			$fields['acf']['video_duration'] = get_field(
				'video_duration',
				$fields['id']
			);
			$fields['acf']['video_url']      = get_field(
				'video_url',
				$fields['id']
			);
		}

		$fields['acf']['exercise_type'] = get_field(
			'exercise_type',
			$fields['id']
		);

		return $fields;
	}

	private function get_video_id( $acf ) {
		$video_url = str_replace( '/event', '', $acf['video_url'] );
		$regex     = '/vimeo.com\/(\w+)/i';
		preg_match( $regex, $video_url, $matches );

		return $matches[1] ?? '';
	}

	private function get_next_videos( $next_videos_ids ) {
		if ( empty( $next_videos_ids ) ) {
			return false;
		}
		$next_videos = [];
		foreach ( $next_videos_ids as $video_id ) {
			$next_videos[] = [
				'post_title'      => get_the_title( $video_id ),
				'url'             => get_permalink( $video_id ),
				'thumbnails'      => $this->image_helper->get_attachment_metadata( get_field( 'hero_images', $video_id )[0] ),
				'video_live_date' => get_field( 'video_live_date', $video_id ),
				'series'          => get_the_terms( $video_id, 'series' )[0] ?? false,
			];
		}
		return $next_videos;
	}
}
