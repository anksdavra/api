<?php
declare(strict_types=1);

namespace CroissantApi\PostTypeDecorator;

abstract class PostTypeDecoratorAbstract {

	protected $image_helper;
	protected $post_helper;

	public function __construct( $image_helper, $post_helper ) {
		$this->image_helper = $image_helper;
		$this->post_helper  = $post_helper;
	}

	public function decorate_single( array $acf ) : array {

		$acf = $this->check_schema_json( $acf );
		$acf = $this->add_affiliate_links_notice( $acf );
		$acf = $this->add_no_index_value( $acf );
		return $this->decorate_list($acf);
	}

	public function decorate_list( array $acf ) : array {
		// Sponsor
		if (array_key_exists('sponsored', $acf) && $acf['sponsored']) {
			$acf = $this->decorate_sponsor_images($acf);
		} else {
			$acf = $this->remove_sponsor_details($acf);
		}

		//Parallax
		if(array_key_exists('parallax_hero', $acf) && count($acf['parallax_hero']) > 0) {
            $acf = $this->decorate_parallax_hero_images($acf);
		}
		return $acf;
	}

	// This method removes the sponsor fields that are still present on a non-sponsored post type after conversion from sponsored post type
	protected function remove_sponsor_details( array $acf ) : array {

		unset( $acf['sponsored'] );
		unset( $acf['project_id'] );
		unset( $acf['sponsor_name'] );
		unset( $acf['sponsor_logo'] );
		unset( $acf['sponsor_link'] );
		unset( $acf['sponsor_type'] );
		unset( $acf['sponsor_label'] );
		unset( $acf['sponsor_banner'] );

		return $acf;
	}

	// This method checks if seo schema into a json object TEMP TEMP - this is just a temporary solution. In the future, the json object will be created by the cms.
	protected function check_schema_json( array $acf ) : array {
		if ( ! isset( $acf['seo_schema'] ) || empty( $acf['seo_schema'] ) ) {
			return $acf;
		}

		$json_schema = json_encode($acf['seo_schema']);

		if ( ! is_object( $json_schema ) ) {
			$acf['seo_schema'] = '';
			return $acf;
		}
		$acf['seo_schema'] = $json_schema;
		return $acf;
	}

	protected function decorate_sponsor_images( array $acf ) : array {

		if ( ! empty( $acf['sponsor_logo'] ) && is_numeric( $acf['sponsor_logo'] ) ) {
			$acf['sponsor_logo'] = $this->image_helper->get_attachment_metadata( $acf['sponsor_logo'] );
		}

		if ( ! empty( $acf['sponsor_banner'] ) && is_numeric( $acf['sponsor_banner'] ) ) {
			$acf['sponsor_banner'] = $this->image_helper->get_attachment_metadata( $acf['sponsor_banner'] );
		}

		if ( ! empty( $acf['brand_logo'] ) && is_numeric( $acf['brand_logo'] ) ) {
			$acf['brand_logo'] = $this->image_helper->get_attachment_metadata( $acf['brand_logo'] );
		}

		return $acf;
	}

	protected function decorate_parallax_hero_images( array $acf ) : array {
		$i = 0;
		foreach($acf['parallax_hero'] as $data) {
			if (!empty( $data['image']) && is_numeric( $data['image'] )) {
				$acf['parallax_hero'][$i]['image'] = $this->image_helper->get_attachment_metadata($data['image']);
			}

			if (!empty( $data['mobile_image'] ) && is_numeric( $data['mobile_image'] )) {
				$acf['parallax_hero'][$i]['mobile_image'] = $this->image_helper->get_attachment_metadata( (int)$data['mobile_image']);
			}
			$i++;
		}
		return $acf;
	}

	protected function add_affiliate_links_notice( $acf ) {
		$has_affiliate_links           = $acf['affiliate_links'] ?? false;
		$acf['affiliate_links']        = $has_affiliate_links;
		$acf['affiliate_links_notice'] = $has_affiliate_links ? get_field( 'affiliate_links_admin_notice', 'option' ) ?? '' : '';
		return $acf;
	}

	protected function add_no_index_value( $acf ) {
		$acf['no_index'] = $acf['no_index'] ?? false;
		return $acf;
	}
}
