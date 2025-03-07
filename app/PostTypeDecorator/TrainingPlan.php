<?php
declare(strict_types=1);

namespace CroissantApi\PostTypeDecorator;

class TrainingPlan extends PostTypeDecoratorAbstract implements PostTypeDecorator {

	public function decorate_list( array $fields ) : array {
		$fields['acf']['duration'] = get_field(
			'training_plan_duration',
			$fields['id']
		);
		$fields['acf']['classes']  = get_field(
			'training_plan_classes',
			$fields['id']
		);

		$widgets                      = get_field( 'widgets', $fields['id'] );
		$fields['acf']['amp_stories'] = [];
		if ( ! empty( $widgets ) ) {
			$fields['acf']['amp_stories'] = $this->get_amp_stories( $widgets );
		}

		return $fields;
	}

	private function get_amp_stories( array $widgets ) {
		$amp_stories = [];
		foreach ( $widgets as $widget ) {
			foreach (
				$widget['widget_amp_story_picker_amp_stories'] as $amp
			) {
				$amp_stories[] = $amp['amp_story'];
			}
		}

		return $amp_stories;
	}
}
