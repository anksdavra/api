<?php

namespace CroissantApi\Endpoint;

use WpTapestryPlugin\Service\PostInterface;
use CroissantApi\Util\Factory;

class VerticalsApi {

	private $post_service = null;
	private $wp_query     = null;

	public function __construct( PostInterface $post_service, $wp_query ) {
		$this->post_service = $post_service;
		$this->wp_query     = $wp_query;
	}

	public function init() {
		add_action( 'rest_api_init', [ $this, 'register_route' ] );
	}

	public function register_route() {
		register_rest_route(
			'croissant/v1', '/verticals', [
				'methods'  => 'GET',
				'callback' => [ $this, 'get_verticals' ],
			]
		);
	}

	public function get_verticals() {

		$verticals = get_terms(
			[
				'taxonomy'   => 'vertical',
				'hide_empty' => false,
				'meta_key'   => 'order',
				'orderby'    => 'meta_value',
			]
		);

		if ( ! is_array( $verticals ) || empty( $verticals ) ) {
			return new \WP_Error( 'no_verticals', 'No Verticals', [ 'status' => 404 ] );
		}

		$image_helper = ( Factory::get_instance() )->create( 'Image' );

		$info = [];
		foreach ( $verticals as $vertical ) {
			$acf_fields = get_fields( $vertical );

			if ( ! isset( $acf_fields['order'] ) || $acf_fields['order'] < 1 ) {
				continue;
			}

			$img_obj = $image_helper->get_attachment_metadata( $acf_fields['thumbnail_image'] );

			$parent = null;
			if ( isset( $vertical->parent ) ) {
				$parent = get_term( $vertical->parent );
				$parent = $parent instanceof \WP_Term ? $parent->slug : null;
			}

			$sigup_widget_colours = [
				'background_colour'  => $acf_fields['background_colour'] ?? '',
				'text_colour'        => $acf_fields['text_colour'] ?? '',
				'button_colour'      => $acf_fields['button_colour'] ?? '',
				'button_text_colour' => $acf_fields['button_text_colour'] ?? '',
			];

			$info[] = [
				'id'                   => $vertical->term_id,
				'name'                 => $vertical->name,
				'slug'                 => $vertical->slug,
				'parent'               => $parent,
				'description'          => $vertical->description,
				'sell'                 => $acf_fields['sell'] ?? '',
				'success_message'      => $acf_fields['success_message'] ?? '',
				'delivered'            => $acf_fields['delivered'] ?? '',
				'telemetry_vertical'   => (int) get_term_meta( $acf_fields['telemetry_vertical'], 'telemetry_vertical_id', true ),
				'primary_colour'       => $acf_fields['primary_colour'],
				'accent_colour'        => $acf_fields['accent_colour'],
				'thumbnail_image'      => ! empty( $img_obj ) ? $img_obj['url'] : '',
				'image'                => $img_obj,
				'crop'                 => ! empty( $acf_fields['crop'] ) ? $acf_fields['crop'] : 'original',
				'most_recent_email_id' => $this->get_most_recent_email( $vertical->term_id ),
				'signup_widget'        => $sigup_widget_colours,
				'paid'                 => $acf_fields['vertical_paid'] ?? false,
				'premium_package_id'      => $acf_fields['vertical_premium'],
				'salespage'            => get_post_permalink($acf_fields['vertical_salespage'])
			];

		}

		return $info;
	}

	private function get_most_recent_email( $vertical_id ) {
		$args  = [
			'posts_per_page' => 1,
			'fields'         => 'ids',
			'post_type'      => 'email',
			'tax_query'      => [
				[
					'taxonomy'         => 'vertical',
					'fields'           => 'term_id',
					'terms'            => $vertical_id,
					'include_children' => false,
				],
			],
		];
		$email = $this->wp_query->query( $args );

		return ! empty( $email ) ? $email[0] : null;
	}
}
