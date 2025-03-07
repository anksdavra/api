<?php

namespace CroissantApi\Endpoint;

use WpTapestryPlugin\Service\PostInterface;
use CroissantApi\Util\Image;
use CroissantApi\Service\SeoSchemaGenerator;

class SeriesApi {

	private $post_service;
	private $image_helper;
	private $seo_schema_generator;

	public function __construct( PostInterface $post_service, Image $image_helper, SeoSchemaGenerator $seo_schema_generator ) {
		$this->post_service = $post_service;
		$this->image_helper = $image_helper;
		$this->seo_schema_generator = $seo_schema_generator;
	}

	public function init() {
		add_action( 'rest_api_init', [ $this, 'register_route' ] );
	}

	public function register_route() {
		register_rest_route(
			'croissant/v1', '/series', [
				'methods'  => 'GET',
				'callback' => [ $this, 'get_series_info' ],
				'args'     => [
					'slug' => [
						'required' => true,
					],
				],
			]
		);
	}

	public function get_series_info( $data ) {
		$slug   = $data->get_param( 'slug' );
		$series = get_term_by( 'slug', $slug, 'series' );

		if ( ! $series ) {
			return new \WP_Error( 'no_series', 'Series ' . $slug . ' does not exist', [ 'status' => 404 ] );
		}
		$series->link = get_term_link( $series, 'series' );

		$schema = $this->seo_schema_generator->generateSeriesSchema( $series );

		$acf_fields                         = get_fields( $series );
		$acf_fields['series_nav_label']     = $this->image_helper->get_attachment_metadata( $acf_fields['series_nav_label'] );
		$acf_fields['series_badge']         = $this->image_helper->get_attachment_metadata( $acf_fields['series_badge'] );
		$acf_fields['series_banner']        = $this->image_helper->get_attachment_metadata( $acf_fields['series_banner'] );
		$acf_fields['series_mobile_banner'] = $this->image_helper->get_attachment_metadata( $acf_fields['series_mobile_banner'] );

		// Older series do not automatically pick up these new acf fields unless we update them (or migrate), this is the reason for the below.
		$acf_fields['seo_schema']           = json_encode($schema, true, JSON_UNESCAPED_SLASHES);
		$acf_fields['seo_title']            = $acf_fields['seo_title'] ?? '';
		$acf_fields['seo_description']      = $acf_fields['seo_description'] ?? '';
		$acf_fields['series_canonical_url'] = $series->link;
		$acf_fields['project_id']           = $acf_fields['project_id'] ?? '';

		return array_merge( (array) $series, $acf_fields );
	}
}
