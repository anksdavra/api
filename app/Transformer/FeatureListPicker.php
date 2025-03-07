<?php
declare(strict_types=1);

namespace CroissantApi\Transformer;

class FeatureListPicker extends TransformerAbstract implements Transformer {

	public function apply_transformation( array $widget ) : array {
		$widget['author']     = $this->get_author_info( $widget['list']->post_author );
		$widget['standfirst'] = get_field( 'standfirst', $widget['list']->ID );
		$widget['list']       = $this->get_feature_list( $widget['list']->ID );

		return $widget;
	}

	private function get_author_info( $id ) {
		$author = get_user_by( 'id', $id );

		return [
			'id'   => $author->ID,
			'name' => $author->data->display_name,
			'link' => get_author_posts_url( $author->ID ),
			'slug' => sanitize_title( $author->user_login ),
		];
	}

	private function get_feature_list( $post_id ) {
		$list = get_field( 'feature_list_items', $post_id );
		if ( empty( $list ) ) {
			return [];
		}

		$posts = [];
		foreach ( $list as $post ) {
			$posts[] = [
				'title'       => $post['feature_list_title'],
				'image'       => isset( $post['feature_list_image']['ID'] ) ? $post['feature_list_image'] : $this->image_helper->get_attachment_metadata( $post['feature_list_image'] ),
				'paragraph'   => $post['feature_list_paragraph'],
				'button_text' => $post['button_text'],
				'button_link' => $post['button_link'],
			];
		}

		return $posts;
	}
}
