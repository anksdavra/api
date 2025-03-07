<?php
/**
 * @wordpress-plugin
 * Plugin Name:       Croissant Api
 * Description:       Custom endpoints for croissant-heavy
 * Version:           1.0.0
 * Author:            Shortlist Media
 * Author URI:        http://shortlistmedia.co.uk/
 * License:           MIT
 */

defined( 'ABSPATH' ) or die( ':)' );

if ( file_exists( __DIR__ . '/vendor/autoload.php' ) ) {
	require_once __DIR__ . '/vendor/autoload.php';
}

add_action( 'muplugins_loaded', function() {
	$container = require __DIR__ . '/container.php';

	$container['post_api']->init();
	$container['categories_api']->init();
	$container['category_hub']->init();
	$container['trends_api']->init();
	$container['emails_api']->init();
	$container['unsubscribe_api']->init();
	$container['pages_api']->init();
	$container['post_list_api']->init();
	$container['series_api']->init();
	$container['tagbot_categories']->init();
	$container['tags_api']->init();
	$container['users_api']->init();
	$container['verticals_api']->init();
	$container['search_api']->init();
	$container['popular_api']->init();
	$container['campaign_api']->init();
	$container['slackbot_user_lookup_api']->init();
	$container['slackbot_interactive_api']->init();
	$container['slackbot_create_user_api']->init();
	$container['slackbot_post_lookup_api']->init();
	$container['membership_api']->init();
	$container['settings_api']->init();
	$container['printissue_api']->init();

	// v2 endpoints
	$container['post_list_api_v2']->init();
	$container['how_to_posts_api']->init();
	$container['hub_page']->init();
	$container['popup']->init();
	$container['course_categories_api']->init();
	$container['homepage_api']->init();
	$container['category_page']->init();

	//v3 endpoints
	$container['swagger_api']->init();
	$container['homepage_api_v3']->init();

	/**
	 * Set the PostService to our Croissant version,
	 * This plugin is dependent of the tapestry plugin
	 */
	add_filter( 'tapestry_post_service', function( $class ) use ( $container ) {
		return $container['post_service'];
	} );

	/**
	 * Stop all the images being loaded via ACF defaults (they are very slow)
	 * to find more information about this filter, please check:
	 * /web/app/mu-plugins/acf/includes/acf-value-functions.php:117
	 */
	add_filter('acf/pre_format_value', function($null, $value, $post_id, $field) {
		if ( is_feed() ) {
			return null;
		}

		if ( $field['type'] !== 'image' && $field['type'] !== 'gallery' ) {
			return null;
		}

		$not_apply_post_types = [
			'feature_list',
			'quiz_post',
			'sponsored_quiz_post',
		];

		$post_type = get_post_type( $post_id );
		if ( $post_type === 'revision' ) {
			$post_type = get_post_type( get_post( $post_id )->post_parent );
		}

		if ( ! in_array( $post_type, $not_apply_post_types, true ) ) {
			return $value;
		}

		return null;
	}, 10, 4);
} );
