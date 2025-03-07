<?php

namespace CroissantApi\Endpoint\V3;

use OpenApi\Annotations as OA;

/**
 * @OA\PathItem(
 *     path="/croissant/v3/homepage"
 * )
 */
class HomepageApi {

	private $post_service;

	public function __construct( $post_service ) {
		$this->post_service = $post_service;
	}

	public function init() {
		add_action( 'rest_api_init', [ $this, 'register_route' ] );
	}

	public function register_route() {
		register_rest_route(
			'croissant/v3', '/homepage', [
				'methods'  => 'GET',
				'callback' => [ $this, 'get_homepage' ],
			]
		);
	}

	/**
	 * @OA\Get(
	 *     summary="Retrieve homepage content",
	 *     description="Fetches the homepage content along with the hero articles widget.",
	 *     tags={"Homepage"},
	 *     @OA\Response(
	 *         response=200,
	 *         description="Homepage successfully retrieved",
	 *         @OA\JsonContent(
	 *             type="object",
	 *             @OA\Property(
	 *                 property="acf",
	 *                 type="object",
	 *                 description="Advanced Custom Fields data including widgets",
	 *                 example={"widgets": [{"acf_fc_layout": "hero_articles", "posts": []}]}
	 *             )
	 *         )
	 *     ),
	 *     @OA\Response(
	 *         response=500,
	 *         description="Homepage not setup"
	 *     )
	 * )
	 */
	public function get_homepage( $data ) {
		$post_id = get_option( 'page_on_front' );

		if ( empty( $post_id ) ) {
			return new \WP_Error( 'no_home', 'Homepage not setup', [ 'status' => 500 ] );
		}

		return $this->add_hero_articles_widget(
			$this->post_service->get_single_post(
				[
					'p'         => $post_id,
					'post_type' => 'page',
				]
			)
		);
	}

	private function add_hero_articles_widget( array $page ) {
		$win_id   = get_term_by( 'slug', 'win', 'category' )->term_id;
		$promo_id = get_term_by( 'slug', 'promotions', 'category' )->term_id;

		$params = [
			'posts_per_page' => 4,
			'post_type'      => [ 'post', 'longform', 'quiz_post' ],
			'cat'            => "-$win_id,-$promo_id",
			'post__in'       => get_option( 'sticky_posts' ),
			'tax_query'      => [
				[
					'taxonomy' => 'visibility',
					'operator' => 'NOT EXISTS',
				],
			],
		];

		$posts = $this->post_service->get_post_list( $params );

		array_unshift(
			$page['acf']['widgets'], [
				'acf_fc_layout'         => 'hero_articles',
				'posts'                 => $posts,
				'hide_widget_from_page' => false,
			]
		);

		return $page;
	}
}
