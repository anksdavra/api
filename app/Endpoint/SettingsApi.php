<?php

namespace CroissantApi\Endpoint;


use CroissantApi\Util\Image;

class SettingsApi {

	private $image_helper;

	public function __construct(
		Image $image_helper
	) {
		$this->image_helper         = $image_helper;
	}

	public function init() {
		add_action( 'rest_api_init', [ $this, 'register_route' ] );
	}

	public function register_route() {
		register_rest_route(
			'croissant/v1', '/settings', [
				'methods'  => 'GET',
				'callback' => [ $this, 'get_settings' ]
			]
		);
	}

	public function get_settings() {

		$app_onboarding_settings_data = $this->get_app_onboarding_details();

		return [
			'app_onboarding_settings' => $app_onboarding_settings_data
		];
	}

	private function get_app_onboarding_details(): array {

		$main_image_desktop              =   get_field( 'main_image_desktop', 'option' ) ?? '';
		$main_image_tablet               =   get_field( 'main_image_tablet', 'option' ) ?? '';
		$main_image_mobile               =   get_field( 'main_image_mobile', 'option' ) ?? '';
		$qr_code_google                  =   get_field( 'qr_code_google', 'option' ) ?? '';
		$qr_code_apple                   =   get_field( 'qr_code_apple', 'option' ) ?? '';
		$app_onboarding_title            =   get_field( 'app_onboarding_title', 'option' ) ?? '';
		$app_onboarding_description      =   get_field( 'app_onboarding_description', 'option' ) ?? '';

		return [
			'main_image_desktop'    =>   $this->image_helper->get_attachment_metadata($main_image_desktop),
			'main_image_tablet'     =>   $this->image_helper->get_attachment_metadata($main_image_tablet),
			'main_image_mobile'     =>   $this->image_helper->get_attachment_metadata($main_image_mobile),
			'qr_code_google'        =>   $this->image_helper->get_attachment_metadata($qr_code_google),
			'qr_code_apple'         =>   $this->image_helper->get_attachment_metadata($qr_code_apple),
			'title'                 =>   $app_onboarding_title,
			'description'           =>   $app_onboarding_description
		];
	}
}
