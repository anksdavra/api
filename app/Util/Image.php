<?php
declare(strict_types=1);

namespace CroissantApi\Util;

class Image implements Util {

	static $PLACEHOLDER_IMAGE_ID;

	private $images_domain;
	public $placeholder;

	public function __construct() {
		self::$PLACEHOLDER_IMAGE_ID = getenv( 'PLACEHOLDER_IMAGE_ID' );
		$this->placeholder          = false;
	}

	public function set_images_domain( string $images_domain ) {
		$this->images_domain = $images_domain;
	}

	public function get_attachment_metadata( $attachment_id ) : array {
		$lennon_image_sizes = [
			'thumbnail',
			'square',
			'portrait',
			'landscape',
			'letterbox',
		];

		$doris_image_sizes = [
			'10_13',
			'5_4',
			'10_8',
			'16_9',
			'25_9',
			'square',
		];

		// Get the metadata for the attachment
		$metadata = get_post_meta( $attachment_id, '_wp_attachment_metadata', true ) ?: [];
		$post     = get_post( $attachment_id );

		// Get the image URL from WP Offload Media metadata
		$image_url = as3cf_get_attachment_url( $attachment_id );

		// Parse the path from the URL
		$image_path = filter_var( $image_url, FILTER_VALIDATE_URL ) ? parse_url( $image_url, PHP_URL_PATH ) : $image_url;

		// If the image is not offloaded, use the local path
		if ( empty( $image_path ) ) {
			$url = wp_get_attachment_url( $attachment_id );
			$imagepath = filter_var( $url, FILTER_VALIDATE_URL ) ? parse_url( $url, PHP_URL_PATH ) : $url;
			$alt = get_post_meta( $attachment_id, '_wp_attachment_image_alt', true );

			// Handle local images, pass $is_local = true
			$lennon_sizes = $this->get_image_crops( $lennon_image_sizes, (string)$imagepath, $metadata, true );
			$doris_sizes  = $this->kebab_to_snake_case(
				$this->get_image_crops( $doris_image_sizes, (string)$imagepath, $metadata, true )
			);
			return [
				'url'         => ( $url !== false ) ? $url : null,
				'alt'         => ( $alt !== false ) ? $alt : null,
				'width'       => $metadata['width'] ?? null,
				'height'      => $metadata['height'] ?? null,
				'caption'     => $post->post_excerpt ?? null,
				'description' => $post->post_content ?? null,
				'sizes'       => $lennon_sizes ?? [],
				'doris_sizes' => $doris_sizes ?? [],
			];
		}

		// Load the Doris and Lennon sizes
		if ( ! empty( $metadata )) {
			$lennon_sizes = $this->get_image_crops( $lennon_image_sizes, $image_path, $metadata, false );
			$doris_sizes  = $this->kebab_to_snake_case(
				$this->get_image_crops( $doris_image_sizes, $image_path, $metadata, false )
			);
		}

		return [
			'url'         => $image_url,
			'alt'         => get_post_meta( $attachment_id, '_wp_attachment_image_alt', true ),
			'width'       => $metadata['width'] ?? null,
			'height'      => $metadata['height'] ?? null,
			'caption'     => $post->post_excerpt ?? null,
			'description' => $post->post_content ?? null,
			'sizes'       => $lennon_sizes ?? [],
			'doris_sizes' => $doris_sizes ?? [],
		];
	}

	/**
	 * Applies the metadata for the image crops
	 * based on the sizes list given
	 * Lennon and Doris have different ones
	 * so I will keep them separated
	 */
	private function get_image_crops( array $size_list, string $image_path, array $metadata, bool $is_local = false) {
		$sizes     = [];
		$image_dir = dirname( $image_path );

		// Safely retrieve width and height
		$image_width = $metadata['width'] ?? null;  // Use null if not set
		$image_height = $metadata['height'] ?? null; // Use null if not set

		foreach ( $size_list as $size ) {
			if ( ! isset( $metadata['sizes'][ $size ] ) ) {
				$metadata['sizes'][ $size ]['file']   = basename( $image_path );
				$metadata['sizes'][ $size ]['width']  = $image_width;
				$metadata['sizes'][ $size ]['height'] = $image_height;
			}

			// Use the local domain if the image is local, otherwise use the CloudFront domain
			$domain = $is_local ? getenv('WP_HOME') : 'https://' . $this->images_domain;

			$sizes[ $size ]             = $domain . str_replace( '//', '/', '/' . $image_dir . '/' . $metadata['sizes'][ $size ]['file'] );
			$sizes[ $size . '-width' ]  = $metadata['sizes'][ $size ]['width'];
			$sizes[ $size . '-height' ] = $metadata['sizes'][ $size ]['height'];
		}

		return $sizes;
	}

	private function kebab_to_snake_case( array $size_list ) {
		$return_array = [];
		foreach ( $size_list as $key => $value ) {
			$key                  = str_replace( '-', '_', $key );
			$return_array[ $key ] = $value;
		}

		return $return_array;
	}
}
