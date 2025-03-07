<?php

namespace CroissantApi\Endpoint;

class MembershipApi {

	private $image_helper;
	private $post_helper;
	private $post_service;

	public function __construct( $image_helper, $post_helper, $post_service ) {
		$this->image_helper = $image_helper;
		$this->post_helper  = $post_helper;
		$this->post_service = $post_service;

	}

	public function init() {

		add_action( 'rest_api_init', [ $this, 'register_route' ] );
	}

	public function register_route() {
		register_rest_route(
			'croissant/v1', '/my-stylist', [
				'methods'  => 'GET',
				'callback' => [ $this, 'get_membership_details' ],
			]
		);
	}

	public function get_membership_details() {

		$membership_master_image = get_field( 'membership_master_image', 'option' );
		$membership_logo_image   = get_field( 'membership_logo_image', 'option' );
		$print_magazine_delivery_date = get_field( 'print_magazine_delivery_date', 'option' );
		$beauty_sample_delivery_date   = get_field( 'beauty_sample_delivery_date', 'option' );


		if ( ! $membership_master_image ) {
			$master_image = false;
		} else {
			$master_image = $this->image_helper->get_attachment_metadata( $membership_master_image ?? [] );
		}

		if ( ! $membership_logo_image ) {
			$logo_image = false;
		} else {
			$logo_image = $this->image_helper->get_attachment_metadata( $membership_logo_image ?? [] );
		}

		$result = [
			'navigation'          => $this->get_nav_items(),
			'faqs'                => get_field( 'faqs_topics', 'option' ) ?? false,
			'assets'              => [
				'master_image' => $master_image ?? false,
				'logo_image'   => $logo_image ?? false,
			],
			'popular_categories'  => $this->get_popular_categories() ?? [],
			'cancellation_survey' => get_field( 'cancellation_questions', 'option' ) ?? false,
			'exclusive_discounts'   => $this->get_exclusive_discounts(),
			'magazine_dates' => [
				'print_magazine_delivery_date' => $print_magazine_delivery_date,
				'beauty_sample_delivery_date' => $beauty_sample_delivery_date
			]
		];

		return  $result;
	}

	private function get_popular_categories() {

		$sorted_categories  = [];
		$popular_categories = get_option( 'popular_categories' );
		if ( ! $popular_categories ) {
			return [];
		}

		foreach ( $popular_categories as $category_id => $count ) {
			$term_details = get_term( $category_id );
			$image        = get_field( 'category_image', 'category_' . $category_id );
			if ( $image ) {
				$image = $this->image_helper->get_attachment_metadata( $image );
			}
			$sorted_categories[] = [
				'term_id' => $term_details->term_id,
				'name'    => $term_details->name,
				'image'   => $image ?: null,
			];
		}
		return $sorted_categories;
	}

	private function get_exclusive_discounts() {
		$discounts = get_field( 'membership_discounts_group', 'option' ) ?? false;

		if ( ! $discounts || count( $discounts ) == 0 ) {
			return false;
		}
		$result  = [];
		$counter = 1;
		$count   = 0;
		foreach ( $discounts as $discounts_block ) {

			$exclusive_company_name = $discounts_block['exclusive_company_name'] ?? [];
			$exclusive_hero_image = $this->image_helper->get_attachment_metadata( $discounts_block['exclusive_hero_image'] ?? [] );
			$exclusive_title = $discounts_block['exclusive_title'] ?? false;
			$expiry_date = $discounts_block['expiry_date'] ?? false;
			$discount_description = $discounts_block['discount_description'] ?? false;
			$discount_exclusion_code = $discounts_block['discount_exclusion_code'] ?? null;
			$button_label = $discounts_block['button_label'] ?? false;
			$cta_link = $discounts_block['cta_link'] ?? false;

			$offers_group = [
				'exclusive_hero_image' => $exclusive_hero_image,
				'exclusive_company_name' => $exclusive_company_name,
				'exclusive_title' => $exclusive_title,
				'expiry_date' => $expiry_date,
				'discount_description' => $discount_description,
				'discount_exclusion_code' => $discount_exclusion_code,
				'button_label' => $button_label,
				'cta_link' => $cta_link
			];

			$result[] = $offers_group;
		}
		return $result;
	}

	private function get_nav_items() {

		$cms_nav = get_field( 'custom_navigation', 'option' ) ?? false;

		if ( ! $cms_nav ) {
			return false;
		}

		foreach ( $cms_nav as &$menu_item ) {

			if ( $menu_item['second_level'] ) {

				foreach ( $menu_item['second_level'] as &$second_level ) {

					foreach ( $second_level['third_level'] as &$third_level ) {
						$third_level['type'] = 'list';
					}
				}
			}

			if ( ! $menu_item['second_level'] ) {
				$menu_item['second_level'] = [];
			}
		}

		$workouts_params = [
			'post_type'      => 'video_post',
			'posts_per_page' => 2,
			'tax_query'      => [
				[
					'taxonomy' => 'premium',
					'field'    => 'slug',
					'terms'    => [
						'strong-women-training-club',
					],
				],
			],
		];

		$latest_fitness_articles_params = [
			'post_type'      => [ 'post', 'longform', 'sponsored_post', 'sponsored_longform' ],
			'posts_per_page' => 5,
			'tax_query'      => [
				[
					'taxonomy' => 'series',
					'field'    => 'slug',
					'terms'    => [
						'strong-women',
						'training-club',
					],
				],
			],
		];

		$magazine_params = [
			'post_type'      => 'issue',
			'posts_per_page' => 1,
		];

		foreach ( $cms_nav as &$menu_item ) {

			if ( $menu_item['label'] == 'Fitness' ) {
				$menu_item['second_level'][] = [
					'label'       => 'Workouts',
					'third_level' => [
						[
							'post_list' => $this->post_service->get_post_list( $workouts_params ),
							'type'      => 'video',
						],
					],
				];
				$menu_item['second_level'][] = [
					'label'       => 'Latest Articles',
					'third_level' => [
						[
							'post_list' => $this->post_service->get_post_list( $latest_fitness_articles_params ),
							'type'      => 'article',
						],
					],
				];
			}

			if ( $menu_item['label'] == 'Stylist +' ) {
				$magazine_details = [
					'label'       => "This Month's Issue",
					'third_level' => [
						[
							'post_list' => $this->post_service->get_post_list( $magazine_params ),
							'type'      => 'issue',
						],
					],
				];
				array_unshift($menu_item['second_level'], $magazine_details);
			}
		}

		return $cms_nav;
	}
}
