<?php
namespace CroissantApi\Endpoint\V2;

class PopupApi {

	private $post_service;
	private $popup_decorator;
	private $filtering_taxonomies = ['premium', 'series', 'category'];

	public function __construct($post_service, $popup_decorator) {
		$this->post_service = $post_service;
		$this->popup_decorator = $popup_decorator;
	}

	public function init() {
		add_action('rest_api_init', [$this, 'register_route']);
	}

	public function register_route() {
		register_rest_route('croissant/v2', '/popup', [
			'methods'  => 'POST',
			'callback' => [$this, 'get_popup'],
		]);
	}

	public function get_popup($request) {
		$payload = json_decode($request->get_body());

		// Use the payload parameter "page_path"
		$page_path = isset($payload->page_path) && !empty($payload->page_path) ? $payload->page_path : false;
		if ($page_path) {
			if (!$this->should_popup_appear($page_path)) {
				return ['popup' => []];
			}
		}

		// Handle preview_popup_id if provided.
		$preview_popup_id = isset($payload->preview_popup_id) && is_numeric($payload->preview_popup_id) ? $payload->preview_popup_id : false;
		if ($preview_popup_id) {
			$revisions = wp_get_post_revisions($preview_popup_id);
			if (!empty($revisions)) {
				$revision_id = array_values($revisions)[0]->ID;
				$popup = $this->post_service->get_post_list([
					'post__in'    => [$revision_id],
					'post_type'   => 'revision',
					'post_status' => ['draft', 'auto-draft', 'inherit', 'publish'],
				]);
				if (!empty($popup)) {
					$popup = $this->popup_decorator->decorate_list($popup[0]);
					return ['popup' => $popup ?? []];
				}
			}
		}

		// If the payload key is_non_subscriber is explicitly set:
		if (isset($payload->is_non_subscriber)) {
			if ($payload->is_non_subscriber) {
				$popup = $this->get_nonsubscriber_popup();
				return ['popup' => $popup];
			} else {
				return ['popup' => []];
			}
		}

		// If no filtering parameters are provided, return the non-subscriber popup.
		if (
			!isset($payload->premium) &&
			!isset($payload->category) &&
			!isset($payload->series) &&
			!isset($payload->post_id)
		) {
			$popup = $this->get_nonsubscriber_popup();
			return ['popup' => $popup];
		}

		// Continue with filtering based on category, series, and premium.
		$category = isset($payload->category) && is_string($payload->category) ? $payload->category : false;
		$series   = isset($payload->series) && is_string($payload->series) ? $payload->series : false;
		$premium  = isset($payload->premium) && is_string($payload->premium) ? $payload->premium : false;

		$taxonomyGroup = 'popup_rules_group_popup_taxonomy_rule_';
		if ($category || $series || $premium) {
			$meta = [];
			if ($category) {
				$categoryTerm = get_term_by('name', $category, 'category');
				$categoryId = !empty($categoryTerm->term_id) ? $categoryTerm->term_id : '-999';
				if ($categoryId) {
					$meta[] = [
						'key'     => $taxonomyGroup . 'category',
						'value'   => $categoryId,
						'compare' => 'LIKE'
					];
				}
			}
			if ($series) {
				$seriesTerm = get_term_by('name', $series, 'series');
				$seriesId = !empty($seriesTerm->term_id) ? $seriesTerm->term_id : '-999';
				if ($seriesId) {
					$meta[] = [
						'key'     => $taxonomyGroup . 'series',
						'value'   => $seriesId,
						'compare' => 'LIKE'
					];
				}
			}
			if ($premium) {
				$premiumTerm = get_term_by('name', $premium, 'premium');
				$premiumId = !empty($premiumTerm->term_id) ? $premiumTerm->term_id : '-999';
				if ($premiumId) {
					$meta[] = [
						'key'     => $taxonomyGroup . 'premium',
						'value'   => $premiumId,
						'compare' => 'LIKE'
					];
				}
			}
			if (isset($payload->logged_user)) {
				$meta[] = [
					'key'     => 'popup_rules_group_popup_logged_user',
					'value'   => $payload->logged_user ? 1 : 0,
					'compare' => 'LIKE'
				];
			}
			$popup = $this->post_service->get_post_list([
				'post_type'   => 'popup',
				'post_status' => ['publish'],
				'meta_query'  => [
					'relation' => 'AND',
					$meta
				]
			]);
			if (empty($popup)) {
				return ['popup' => []];
			}
			$popup = $this->popup_decorator->decorate_list($popup[0]);
			return ['popup' => $popup ?? []];
		}

		$popups = $this->post_service->get_post_list([
			'post_type'      => 'popup',
			'posts_per_page' => -1,
		]);
		if (empty($popups)) {
			return ['popup' => []];
		}

		$permutive_segments = isset($payload->permutive_segment_ids) && is_array($payload->permutive_segment_ids) && !empty($payload->permutive_segment_ids)
			? $payload->permutive_segment_ids
			: false;
		$result = false;
		if ($permutive_segments) {
			$result = $this->get_popup_for_permutive_segments($permutive_segments, $popups);
		}
		if ($result) {
			return ['popup' => $result ?? []];
		}

		$taxonomy_filters = [];
		foreach ($this->filtering_taxonomies as $tax) {
			$taxonomy_filters[$tax] = isset($payload->{$tax}) ? $payload->{$tax} : false;
		}
		$post_id_is_set    = isset($payload->post_id) && is_numeric($payload->post_id);
		$needs_logged_user = isset($payload->logged_user) && is_bool($payload->logged_user) && $payload->logged_user ? true : false;
		if ($post_id_is_set) {
			$result = $this->get_popup_for_single_pages($payload->post_id, $popups, $needs_logged_user);
		} else {
			$result = $this->get_popup_for_listing_pages($taxonomy_filters, $popups, $needs_logged_user);
		}
		return ['popup' => $result ?? []];
	}

	private function should_popup_appear($page_path) {
		$exclusion_list = get_field('popup_exclusion_list', 'option') ?? false;
		if (!$exclusion_list || !$page_path) {
			return true;
		}
		$exclusion_list = explode("\r\n", $exclusion_list);
		foreach ($exclusion_list as &$excluded_path) {
			$excluded_path = trim($excluded_path);
		}
		foreach ($exclusion_list as $excluded_path) {
			if (strpos($page_path, $excluded_path) !== false) {
				return false;
			}
		}
		return true;
	}

	private function get_popup_for_permutive_segments($permutive_segments, $all_popups) {
		foreach ($all_popups as $popup) {
			if ($this->is_expired($popup['id'])) {
				continue;
			}
			if (!empty(array_intersect($permutive_segments, $popup['popup_rules']['segments']))) {
				return $popup;
			}
		}
		return false;
	}

	private function get_popup_for_single_pages($post_id, $all_popups, $needs_logged_user) {
		$post = get_post($post_id);
		if (!$post) {
			return new \WP_Error('popup_not_found', 'Popup was not found for these filters.', ['status' => 404]);
		}
		$post_taxonomies = [];
		foreach ($this->filtering_taxonomies as $tax) {
			// Ensure we always get an array.
			$post_taxonomies[$tax] = wp_get_post_terms($post_id, $tax, ['fields' => 'slugs']) ?: [];
		}
		return $this->check_popup_rules_against_requirements($all_popups, $post_taxonomies, $needs_logged_user);
	}

	private function get_popup_for_listing_pages($taxonomy_filters, $all_popups, $needs_logged_user) {
		$required_taxonomies = [];
		// Convert filtering values to arrays (or empty arrays if not set).
		$required_taxonomies['premium']  = isset($taxonomy_filters['premium']) && $taxonomy_filters['premium'] ? explode(',', $taxonomy_filters['premium']) : [];
		$required_taxonomies['series']   = isset($taxonomy_filters['series']) && $taxonomy_filters['series'] ? explode(',', $taxonomy_filters['series']) : [];
		$required_taxonomies['category'] = isset($taxonomy_filters['category']) && $taxonomy_filters['category'] ? explode(',', $taxonomy_filters['category']) : [];
		return $this->check_popup_rules_against_requirements($all_popups, $required_taxonomies, $needs_logged_user);
	}

	private function check_popup_rules_against_requirements($all_popups, $required_taxonomies, $needs_logged_user) {
		$rules_popup             = false;
		$no_rules_popup          = false;
		$no_taxonomy_rules_popup = false;

		// Ensure that required_taxonomies for each key is an array.
		foreach (['premium', 'series', 'category'] as $tax) {
			if (!isset($required_taxonomies[$tax]) || !is_array($required_taxonomies[$tax])) {
				$required_taxonomies[$tax] = [];
			}
		}

		// Get the most recent popup that has no rules at all.
		foreach ($all_popups as $popup) {
			if ($this->is_expired($popup['id'])) {
				continue;
			}
			if (empty($popup['popup_rules']['premium']) &&
				empty($popup['popup_rules']['series']) &&
				empty($popup['popup_rules']['category']) &&
				!$popup['popup_rules']['logged_user']) {
				$no_rules_popup = $popup;
				break;
			}
		}

		// Get the most recent popup that has no taxonomy rules but requires a logged user.
		foreach ($all_popups as $popup) {
			if ($this->is_expired($popup['id'])) {
				continue;
			}
			if (!empty($popup['popup_rules']['segments'])) {
				continue;
			}
			if (empty($popup['popup_rules']['premium']) &&
				empty($popup['popup_rules']['series']) &&
				empty($popup['popup_rules']['category']) &&
				$popup['popup_rules']['logged_user'] == $needs_logged_user) {
				$no_taxonomy_rules_popup = $popup;
				break;
			}
		}

		// Check for popups with matching taxonomy rules.
		foreach ($all_popups as $popup) {
			if ($this->is_expired($popup['id'])) {
				continue;
			}
			if (!empty($popup['popup_rules']['segments'])) {
				continue;
			}
			if ($popup['popup_rules']['logged_user'] != $needs_logged_user) {
				continue;
			}
			// Ensure the popup rules are arrays.
			$popup_premium = isset($popup['popup_rules']['premium']) && is_array($popup['popup_rules']['premium']) ? $popup['popup_rules']['premium'] : [];
			$popup_series  = isset($popup['popup_rules']['series']) && is_array($popup['popup_rules']['series']) ? $popup['popup_rules']['series'] : [];
			$popup_category = isset($popup['popup_rules']['category']) && is_array($popup['popup_rules']['category']) ? $popup['popup_rules']['category'] : [];

			if (!empty(array_intersect($popup_premium, $required_taxonomies['premium']))) {
				$rules_popup = $popup;
				break;
			}
			if (!empty(array_intersect($popup_series, $required_taxonomies['series']))) {
				$rules_popup = $popup;
				break;
			}
			if (!empty(array_intersect($popup_category, $required_taxonomies['category']))) {
				$rules_popup = $popup;
				break;
			}
		}

		if ($rules_popup === false && $no_rules_popup === false && $no_taxonomy_rules_popup === false) {
			return [];
		}

		return $rules_popup ?: $no_taxonomy_rules_popup ?: $no_rules_popup;
	}

	private function is_expired($popup_id) {
		$expiry_date_field = get_field('popup_unschedule_date', $popup_id);
		if (!$expiry_date_field) {
			return false;
		}
		$now = strtotime(date('Y-m-d H:i:s'));
		if (strtotime($expiry_date_field) <= $now) {
			return true;
		}
		return false;
	}

	/**
	 * Returns the popup whose meta key "popup_rules_group_popup_target_nonsubscribers" is set to true.
	 */
	private function get_nonsubscriber_popup() {
		$popup = $this->post_service->get_post_list([
			'post_type'      => 'popup',
			'post_status'    => ['publish'],
			'posts_per_page' => 1,
			'orderby'        => 'date',
			'order'          => 'DESC',
			'ignore_sticky_posts' => 1,
			'meta_query'     => [
				[
					'key'     => 'popup_rules_group_popup_target_nonsubscribers',
					'value'   => '1',
					'compare' => '='
				]
			]
		]);

		if (empty($popup)) {
			return [];
		}

		return $this->popup_decorator->decorate_list($popup[0]);
	}
}
