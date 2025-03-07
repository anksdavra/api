<?php

declare(strict_types=1);

namespace CroissantApi\Transformer;

class SnippetPicker extends TransformerAbstract implements Transformer {

	public function apply_transformation( array $widget ) : array {
		$snippet_fields          = $this->get_snippet_fields( $widget['collection'] );
		$widget['collection']    = $snippet_fields['collection'];
		$widget['layout_header'] = is_array( $snippet_fields['header'] ) ? $snippet_fields['header'] : ( $widget['layout_header'] === '' ? null : $widget['layout_header'] );
		$widget['authors']       = $snippet_fields['authors'];

		return $widget;
	}

	private function get_snippet_fields( $snippets ) {
		$posts        = [];
		$header_image = false;
		$author_ids   = [];
		$authors      = [];

		foreach ( $snippets as $post ) {
			if ( $header_image === false ) {
				$header_image = $this->get_header_image_from_snippet_tag( $post->ID );
			}

			$author_ids[] = $post->post_author;

			$sponsored = false;
			if ( get_field( 'sponsored', $post->ID ) == 1 ) {
				$sponsored = [
					'name'  => get_field( 'sponsor_name', $post->ID ),
					'label' => get_field( 'sponsor_label', $post->ID ),
					'link'  => get_field( 'sponsor_link', $post->ID ),
				];
			}
			$snippet_button_link = get_field( 'snippet_button_link', $post->ID );

			$posts[] = [
				'headline'    => get_field( 'snippet_headline', $post->ID ),
				'sell'        => get_field( 'snippet_sell', $post->ID ),
				'paragraph'   => get_field( 'snippet_paragraph', $post->ID ),
				'button_text' => get_field( 'snippet_button_text', $post->ID ),
				'button_link' => strpos( $snippet_button_link, 'mailto://' ) === 0 ? str_replace( 'mailto://', 'mailto:', $snippet_button_link ) : $snippet_button_link,
				'sponsor'     => $sponsored,
				'image'       => $this->image_helper->get_attachment_metadata( get_field( 'snippet_image', $post->ID ) ),
				'project_id'  => get_field( 'snippet_project_id', $post->ID ),

			];
		}

		$author_ids = array_unique( $author_ids );

		foreach ( $author_ids as $id ) {
			$authors[] = $this->get_author_info( $id );
		}

		return [
			'collection' => $posts,
			'header'     => $header_image,
			'authors'    => $authors,
		];
	}

	private function get_header_image_from_snippet_tag( $post_id ) {
		$tags = get_field( 'tags', $post_id );
		if ( empty( $tags ) ) {
			return false;
		}

		$header_image = [];
		foreach ( $tags as $tag ) {
			$image_object = get_field( 'header_image', $tag );
			if ( ! empty( $image_object ) ) {
				$header_image['url'] = $this->image_helper->get_attachment_metadata( $image_object )['url'];
				break;
			}
		}

		return $header_image;
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
}
