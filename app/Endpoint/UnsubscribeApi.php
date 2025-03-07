<?php

namespace CroissantApi\Endpoint;

class UnsubscribeApi {

	public function init() {
		add_action( 'rest_api_init', [ $this, 'register_route' ] );
	}

	public function register_route() {
		register_rest_route(
			'croissant/v1', '/unsubscribe-questions', [
				'methods'  => 'GET',
				'callback' => [ $this, 'get_unsubscribe_questions' ],
			]
		);
	}

	public function get_unsubscribe_questions( $data ) {
		$questions = get_field( 'unsubscribe_questions', 'option' );
		$result    = [];

		if ( ! $questions ) {
			return $result;
		}

		foreach ( $questions as $key => $question ) {
			shuffle( $question['answers'] );
			$answers_values = array_column( $question['answers'], 'answer' );
			$result[]       = [
				'topic_id'   => $key,
				'question'   => $question['question'],
				'answers'    => $answers_values,
				'resub_text' => $question['resub_text'],
			];
		}

		return $result;
	}
}
