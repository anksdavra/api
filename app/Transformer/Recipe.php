<?php
declare(strict_types=1);

namespace CroissantApi\Transformer;

class Recipe extends TransformerAbstract implements Transformer {

	public function apply_transformation( array $widget ): array {
        $transformed = [
            'acf_fc_layout' => 'recipe',
        ];
		if(empty($widget['options'])){
           $transformed['hide_widget_from_page'] = true; 
           return $transformed;
        }
        
        foreach($widget['options'] as $option) {
            $transformed[$option] = $widget[$option];
        }
        $transformed['hide_widget_from_page'] = $widget['hide_widget_from_page'];
		return $transformed;
	}
}
