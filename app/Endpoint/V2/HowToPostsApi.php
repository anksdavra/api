<?php
namespace CroissantApi\Endpoint\V2;

use CroissantApi\Endpoint\V2\PostListApiV2;


class HowToPostsApi {

	protected $post_list;
	protected $image_helper;


	public function __construct( $post_list, $image_helper ) {
		$this->post_list    = $post_list;
		$this->image_helper = $image_helper;
	}

	public function init() {
		add_action( 'rest_api_init', [ $this, 'register_route' ] );
	}

	public function register_route() {
		register_rest_route(
			'croissant/v2', '/how-to', [
				'methods'  => 'GET',
				'callback' => [
					$this,
					'get_how_to_posts',
				],
				'args'     => [
					'muscle_group' => [
						'required' => false,
					],
					'exercise_type1' => [
						'required' => false,
					],
				],
			]
		);
	}

	public function get_how_to_posts( $data ) {

		$data->set_param( 'post_types', 'how_to' );
		$exercise_type = $data->get_param( 'exercise_type_how_to');
		$data->set_param( 'exercise_type_how_to', $exercise_type );

		$muscle_group = $data->get_param( 'muscle_group');
		$data->set_param( 'muscle_group', $muscle_group );

		$posts = $this->post_list->get_results( $data );
		if ( is_wp_error( $posts ) ) {
			return $posts;
		}

		return $this->apply_how_to_fields( $this->post_list->get_results( $data ) );
	}

	private function apply_how_to_fields( $posts ) {

		if ( is_wp_error( $posts['posts'] ) ) {
			return $posts;
		}

		foreach ( $posts['posts'] as &$post ) {

			$package_ids = $post['acf']['package_ids'];
			$post['acf'] = [
				'video_url'         => get_field( 'how_to_video_url', $post['id'] ),
				'placeholder_image' => $this->image_helper->get_attachment_metadata( get_field( 'how_to_placeholder_image', $post['id'] ) ),
				'package_ids'       => $package_ids,

			];
		}

		return $posts;
	}
}
