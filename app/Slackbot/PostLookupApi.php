<?php

namespace CroissantApi\Slackbot;

class PostLookupApi {


	private $signing_secret;
	private $team;
	private $channel;
	private $wp_query;
	private $post_types = [
		'post',
		'longform',
		'sponsored_post',
		'sponsored_longform',
		'email',
		'feature_list',
		'snippet',
		'quiz_post',
		'sponsored_quiz_post',
	];

	public function __construct( string $signing_secret, string $slack_team, string $slack_channel, $wp_query ) {
		$this->signing_secret = $signing_secret;
		$this->team           = $slack_team;
		$this->channel        = $slack_channel;
		$this->wp_query       = $wp_query;
	}

	public function init() {
		add_action( 'rest_api_init', [ $this, 'register_route' ] );
	}

	public function register_route() {
		register_rest_route(
			'croissant/v1/slackbot', '/posts', [
				'methods'  => 'POST',
				'callback' => [ $this, 'get_post_info' ],
				'args'     => [
					'team_id'      => [
						'required' => true,
					],
					'channel_name' => [
						'required' => true,
					],
					'text'         => [
						'required' => true,
					],
				],
			]
		);
	}

	public function get_post_info( $data ) {
		// Validate request
		$timestamp = $data->get_header( 'X-Slack-Request-Timestamp' );
		$signature = $data->get_header( 'X-Slack-Signature' );
		$body      = $data->get_body();

		$time  = $this->validate_timestamp( $timestamp );
		$valid = $this->validate_request( $timestamp, $signature, $body );
		if ( $time !== true || $valid !== true ) {
			return $this->get_unauthorized_response();
		}

		if ( $data->get_param( 'team_id' ) !== $this->team ) {
			return $this->get_invalid_team_response();
		}

		if ( $data->get_param( 'channel_name' ) !== $this->channel ) {
			return $this->get_wrong_channel_response( $data->get_param( 'channel_name' ), $this->channel );
		}

		$post_id = $data->get_param( 'text' );
		if ( $post_id === '' || ! is_numeric( $post_id ) ) {
			return $this->get_empty_string_response();
		}

		$post = $this->wp_query->query(
			[
				'p'           => $post_id,
				'post_type'   => $this->post_types,
				'post_status' => [
					'publish',
					'draft',
					'future',
				],
			]
		);

		if ( empty( $post ) ) {
			return $this->get_empty_response();
		}

		$post   = $post[0];
		$blocks = $this->get_post_data( $post );

		return [
			'response_type' => 'in_channel',
			'attachments'   => [
				[
					'blocks' => $blocks,
				],
			],
		];
	}

	private function validate_timestamp( $timestamp ) {
		if ( ( time() - $timestamp ) > 60 * 5 ) {
			return false;
		}
		return true;
	}

	private function validate_request( $timestamp, $header_signature, $body ) {
		$sig_basestring      = 'v0:' . $timestamp . ':' . $body;
		$croissant_signature = 'v0=' . hash_hmac( 'sha256', $sig_basestring, $this->signing_secret );
		if ( $header_signature !== $croissant_signature ) {
			return false;
		}
		return true;
	}

	private function get_unauthorized_response() {
		return $this->build_slack_response( 401, 'Sorry, unauthorized request.', 'https://media.giphy.com/media/fxgVuoKyZwEOudRXuj/200w_d.gif', 'Booo!' );
	}

	private function get_invalid_team_response() {
		return $this->build_slack_response( 401, 'Sorry, invalid team ID.', 'https://media.giphy.com/media/fxgVuoKyZwEOudRXuj/200w_d.gif', 'Invalid team!' );
	}

	private function get_wrong_channel_response( $param, $env ) {
		return $this->build_slack_response( 404, 'Uh oh! ' . $param . ' is the wrong channel. Try #' . $env . ' instead!', 'https://media.giphy.com/media/3krrjoL0vHRaWqwU3k/giphy-downsized.gif', 'Phew.' );
	}

	private function get_empty_string_response() {
		return $this->build_slack_response( 406, 'Oops! You entered an invalid post ID or you forgot to put one.', 'https://media.giphy.com/media/7YCRcFcDeDuSOAHXlr/200w_d.gif', 'Oops' );
	}

	private function get_empty_response() {
		return $this->build_slack_response( 200, "Hm. Sorry, I couldn't find this post!", 'https://media.giphy.com/media/1fkd6ZTBsxSosV4UTS/200w_d.gif', 'You should create one!' );
	}

	private function build_slack_response( $status, $text, $image, $alt_text ) {
		return [
			'status'        => $status,
			'response_type' => 'in_channel',
			'attachments'   => [
				[
					'blocks' => [
						[
							'type' => 'section',
							'text' => [
								'type' => 'mrkdwn',
								'text' => $text,
							],
						],
						[
							'type'      => 'image',
							'title'     => [
								'type'  => 'plain_text',
								'text'  => $alt_text,
								'emoji' => true,
							],
							'image_url' => $image,
							'alt_text'  => $alt_text,
						],
					],
				],
			],
		];
	}

	private function get_post_data( $post ) {
		return [
			[
				'type' => 'section',
				'text' => [
					'type' => 'mrkdwn',
					'text' => 'Yippee! I have found this post:',
				],
			],
			[
				'type'      => 'section',
				'text'      => [
					'type' => 'mrkdwn',
					'text' => '*' . $post->post_title . '*: ' . $post->post_type,
				],
				'accessory' => $this->get_select( $post->ID ),
			],
			[
				'type' => 'divider',
			],
		];
	}

	private function get_select( $id ) {
		$options = [];
		foreach ( $this->post_types as $post_type ) {
			$options[] = [
				'text'  => [
					'type'  => 'plain_text',
					'text'  => $post_type,
					'emoji' => true,
				],
				'value' => "post,$id,$post_type",
			];
		}

		return [
			'type'        => 'static_select',
			'placeholder' => [
				'type'  => 'plain_text',
				'text'  => 'Change the post type',
				'emoji' => true,
			],
			'options'     => $options,
		];
	}
}
