<?php

namespace CroissantApi\Slackbot;

class CreateUserApi {


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
			'croissant/v1/slackbot',
			'/create',
			[
				'methods'  => 'POST',
				'callback' => [ $this, 'create_user' ],
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

	public function create_user( $data ) {
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

		$text = $data->get_param( 'text' );
		if ( $text === '' ) {
			return $this->get_empty_string_response();
		}

		$params     = explode( '>', $text );
		$first_name = trim( $params[0], ' <' );
		$surname    = trim( $params[1], ' <' );

		if ( is_null( $first_name ) || is_null( $surname ) || $first_name === '' || $surname === '' ) {
			return $this->get_invalid_names_response();
		}

		if ( count( $params ) > 2 && $params[2] !== '' && ! is_null( $params[2] ) ) {
			$email = trim( $params[2], ' <' );
		} else {
			$email_name = preg_replace( '/[^A-Za-z0-9-]+/', '', $first_name . $surname );
			$email      = "freelance.shortlistmedia+$email_name@gmail.com";
		}

		if ( ! filter_var( $email, FILTER_VALIDATE_EMAIL ) ) {
			return $this->get_invalid_email_response();
		}

		$id = wp_insert_user(
			[
				'user_login' => $first_name . '-' . $surname,
				'user_email' => $email,
				'first_name' => $first_name,
				'last_name'  => $surname,
				'caps'       => 'author',
			]
		);

		$wp_user = new \WP_User( $id );
		$wp_user->add_cap( 'author' );

		return $this->build_slack_response( 200, "$first_name is now a Stylist author.", 'https://media.giphy.com/media/SbtWGvMSmJIaV8faS8/giphy-downsized.gif', 'Wow.' );
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
		return $this->build_slack_response( 406, 'Oops! You forgot to enter a name.', 'https://media.giphy.com/media/7YCRcFcDeDuSOAHXlr/200w_d.gif', 'Oops' );
	}

	private function get_invalid_names_response() {
		return $this->build_slack_response( 406, 'Oops! You entered a name incorrectly. Don\'t forget the triangles `<` `>` around the names and email address: `<Fairy> <Godmother> <optional@email.com>`', 'https://media.giphy.com/media/igGyenWV6Dy4rhXbc8/200w_d.gif', 'Oops' );
	}

	private function get_invalid_email_response() {
		return $this->build_slack_response( 406, 'Oops! *That is not a valid email*. Don\'t forget the triangles `<` `>` around the names and email address: `<Fairy> <Godmother> <optional@email.com>`', 'https://media.giphy.com/media/igGyenWV6Dy4rhXbc8/200w_d.gif', 'Oops' );
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
