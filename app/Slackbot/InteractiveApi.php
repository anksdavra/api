<?php

namespace CroissantApi\Slackbot;

class InteractiveApi {


	private $signing_secret;
	private $team;
	private $channel;

	public function __construct( string $signing_secret, string $slack_team, string $slack_channel ) {
		$this->signing_secret = $signing_secret;
		$this->team           = $slack_team;
		$this->channel        = $slack_channel;
	}

	public function init() {
		add_action( 'rest_api_init', [ $this, 'register_route' ] );
	}

	public function register_route() {
		register_rest_route(
			'croissant/v1', '/slackbot', [
				'methods'  => 'POST',
				'callback' => [ $this, 'transform_payload' ],
			]
		);
	}

	public function transform_payload( $data ) {
		// Validate request
		$timestamp = $data->get_header( 'X-Slack-Request-Timestamp' );
		$signature = $data->get_header( 'X-Slack-Signature' );
		$body      = $data->get_body();

		$time  = $this->validate_timestamp( $timestamp );
		$valid = $this->validate_request( $timestamp, $signature, $body );
		if ( $time !== true || $valid !== true ) {
			return $this->get_unauthorized_response();
		}

		$transform_payload = urldecode( $body );
		$payload_array     = explode( '=', $transform_payload );
		$payload           = json_decode( $payload_array[1] );

		$team_id = $payload->team->id;
		$channel = $payload->channel->name;

		if ( $team_id !== $this->team ) {
			return $this->get_invalid_team_response();
		}

		if ( $channel !== $this->channel ) {
			return $this->get_wrong_channel_response( $channel, $this->channel );
		}

		$response_url = $payload->response_url;
		$value        = $payload->actions[0]->selected_option->value;

		if ( strpos( $value, 'post,' ) === 0 ) {
			$data = $this->change_post( $value );
		} else {
			$data = $this->change_user( $value );
		}

		return wp_remote_post(
			$response_url, [
				'headers' => [ 'Content-type' => 'application/json' ],
				'body'    => json_encode( $data ),
			]
		);
	}

	private function change_post( $value ) {
		$text_array = explode( ',', $value );
		$id         = $text_array[1];
		$post_type  = $text_array[2];

		$return = wp_update_post(
			[
				'ID'        => $id,
				'post_type' => $post_type,
			]
		);

		if ( $return instanceof \WP_Error ) {
			return $this->get_error_response();
		}

		return [
			'status'        => 200,
			'response_type' => 'in_channel',
			'attachments'   => [
				[
					'blocks' => [
						[
							'type' => 'section',
							'text' => [
								'type' => 'mrkdwn',
								'text' => "Post $id is now a $post_type.",
							],
						],
						[
							'type'      => 'image',
							'title'     => [
								'type'  => 'plain_text',
								'text'  => "Let's dance",
								'emoji' => true,
							],
							'image_url' => 'https://media.giphy.com/media/35HTaxVJWzp2QOShct/200w_d.gif',
							'alt_text'  => "Let's dance",
						],
					],
				],
			],
		];
	}

	private function change_user( $value ) {
		$user = explode( ',', $value );
		$id   = $user[0];
		$name = $user[1];
		$role = $user[2];

		$wp_roles = [
			'author'            => [ 'author' ],
			'editor'            => [ 'editor', 'longforms_editor', 'quiz_post_editor', 'email_editor' ],
			'commercial editor' => [ 'editor', 'sponsored_longforms_editor', 'sponsored_posts_editor', 'sponsored_quiz_post_editor', 'email_editor' ],
		];
		$caps     = $wp_roles[ $role ];

		$wp_user = new \WP_User( $id );
		$wp_user->remove_all_caps();

		foreach ( $caps as $cap ) {
			$wp_user->add_cap( $cap );
		}

		return [
			'status'        => 200,
			'response_type' => 'in_channel',
			'attachments'   => [
				[
					'blocks' => [
						[
							'type' => 'section',
							'text' => [
								'type' => 'mrkdwn',
								'text' => "$name is now a Stylist $role.",
							],
						],
						[
							'type'      => 'image',
							'title'     => [
								'type'  => 'plain_text',
								'text'  => "Let's dance",
								'emoji' => true,
							],
							'image_url' => 'https://media.giphy.com/media/35HTaxVJWzp2QOShct/200w_d.gif',
							'alt_text'  => "Let's dance",
						],
					],
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

	private function get_error_response() {
		return $this->build_slack_response( 500, 'Error! please try again later', 'https://http.cat/500.jpg', 'Meow.' );
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
}
