<?php

namespace CroissantApi\Endpoint;

use CroissantApi\Service\SeoSchemaGenerator;

class UsersApi {

	private $seo_schema_generator;

	public function __construct( SeoSchemaGenerator $seo_schema_generator ) {
		$this->seo_schema_generator = $seo_schema_generator;
	}
	public function init() {
		add_action( 'rest_api_init', [ $this, 'register_route' ] );
	}

	public function register_route() {
		register_rest_route(
			'croissant/v1', '/users', [
				'methods'  => 'GET',
				'callback' => [ $this, 'get_user_info' ],
				'args'     => [
					'slug' => [
						'required' => true,
					],
				],
			]
		);
	}

	public function get_user_info( $data ) {
		$slug = $data->get_param( 'slug' );
		$user = get_user_by( 'slug', $slug );

		if ( ! $user ) {
			return new \WP_Error( 'no_user', 'User ' . $slug . ' does not exist', [ 'status' => 404 ] );
		}

		$image_id = get_user_meta( $user->ID, 'user_image', true );

		$url = false;
		if ( $image_id ) {
			$url = wp_get_attachment_url( $image_id );
		}

		$seo_title = get_field('seo_title', 'user_' . $user->ID);
		$seo_description = get_field('seo_description', 'user_' . $user->ID);
		$schema = $this->seo_schema_generator->generateAuthorSchema( $user->ID );

		return [
			'id'          => $user->ID,
			'name'        => $user->data->display_name,
			'description' => $this->get_user_description( $user ),
			'link'        => $this->get_author_posts_url( $user->ID ),
			'slug'        => $slug,
			'type'        => 'author',
			'headshot'    => $url,
			'cannonical_url' => get_author_posts_url( $user->ID ),
			'seo_title'   => $seo_title,
			'seo_description' => $seo_description,
			'seo_schema' =>  json_encode($schema, true, JSON_UNESCAPED_SLASHES)
		];
	}

	private function get_user_description( $user ) {
		return get_user_meta( $user->ID, 'description', true ) ?:
			$user->data->display_name . ' - for <a href="https://www.stylist.co.uk">stylist.co.uk</a>' .
			', read the latest news stories, features and updates here.';
	}

	private function get_author_posts_url( $author_id, $author_nicename = '' ) {
		$auth_ID = (int) $author_id;
		$link    = '/author/%author%';
		if ( '' === $author_nicename ) {
			$user = get_userdata($author_id);
			if (!empty($user->user_nicename)) {
				$author_nicename = $user->user_nicename;
			}
		}

		$link = str_replace( '%author%', $author_nicename, $link );
		$link = home_url( user_trailingslashit( $link ) );

		return $link;
	}
}
