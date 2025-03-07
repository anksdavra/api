<?php

namespace CroissantApi\Slackbot;

class UserLookupApi {


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
			'croissant/v1/slackbot', '/users', [
				'methods'  => 'POST',
				'callback' => [ $this, 'get_user_info' ],
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

	public function get_user_info( $data ) {
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

		$query = new \WP_User_Query( array( 'search' => "*$text*" ) );
		$users = $query->get_results();
		if ( empty( $users ) ) {
			return $this->get_empty_users_response();
		}

		$blocks = $this->get_users( $users );

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
		return $this->build_slack_response( 406, 'Oops! You forgot to enter a name.', 'https://media.giphy.com/media/7YCRcFcDeDuSOAHXlr/200w_d.gif', 'Oops' );
	}

	private function get_empty_users_response() {
		return $this->build_slack_response( 200, "Hm. Sorry, I couldn't find any authors like that... You should create one!", 'https://media.giphy.com/media/1fkd6ZTBsxSosV4UTS/200w_d.gif', 'You should create one!' );
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

	private function get_users( $users ) {

		$blocks = [
			[
				'type' => 'section',
				'text' => [
					'type' => 'mrkdwn',
					'text' => 'Yippee! I have found these authors of interest:',
				],
			],
		];

		foreach ( $users as $user ) {
			$name  = $user->data->display_name;
			$roles = $this->get_user_roles( $user->roles );

			$block = [
				'type' => 'section',
				'text' => [
					'type' => 'mrkdwn',
					'text' => '*' . $name . '*: ' . $roles['roles'],
				],
			];

			if ( $roles['button'] ) {
				$block['accessory'] = $this->get_select( $name, $user->ID );
			}
			$blocks[] = $block;
			$blocks[] = [ 'type' => 'divider' ];
		}

		return $blocks;
	}

	private function get_user_roles( $user_roles ) {
		$roles  = '';
		$button = false;
		foreach ( $user_roles as $role ) {
			if ( $role === 'purgatory' || $role === 'subscriber' || $role === 'author' ) {
				$button = true;
			}
			$roles .= '`' . $role . '` ';
		}
		return [
			'roles'  => $roles,
			'button' => $button,
		];
	}

	private function get_select( $name, $id ) {
		return [
			'type'        => 'static_select',
			'placeholder' => [
				'type'  => 'plain_text',
				'text'  => "Revive $name as",
				'emoji' => true,
			],
			'options'     => [
				[
					'text'  => [
						'type'  => 'plain_text',
						'text'  => 'Author',
						'emoji' => true,
					],
					'value' => "$id,$name,author",
				],
				[
					'text'  => [
						'type'  => 'plain_text',
						'text'  => 'Editor',
						'emoji' => true,
					],
					'value' => "$id,$name,editor",
				],
				[
					'text'  => [
						'type'  => 'plain_text',
						'text'  => 'Commercial editor',
						'emoji' => true,
					],
					'value' => "$id,$name,commercial editor",
				],
			],
		];
	}
}
