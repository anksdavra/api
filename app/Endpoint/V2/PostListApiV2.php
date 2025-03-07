<?php

namespace CroissantApi\Endpoint\V2;


use CroissantApi\Endpoint\PostListApi;
/**
 * @SuppressWarnings(PHPMD)
 */
class PostListApiV2 extends PostListApi {

	public function init() {
		parent::init();
		add_filter( 'posts_where', [ $this, 'title_filter' ], 10, 2 );
	}

	public function register_route() {

		$this->route_args['show_authors'] = [
			'required' => false,
		];

		$this->route_args['show_taxonomies'] = [
			'required' => false,
		];

		$this->route_args['title_search'] = [
			'required' => false,
		];

		$this->route_args['sort_tax'] = [
			'required' => false,
		];

		$this->route_args['sort_terms'] = [
			'required' => false,
		];

		$this->route_args['exercise_type'] = [
			'required' => false,
		];

		$this->route_args['ordering'] = [
			'required' => false,
		];

		register_rest_route(
			'croissant/v2', '/posts', [
				'methods'  => 'GET',
				'callback' => [ $this, 'get_results' ],
				'args'     => $this->route_args,
			]
		);
	}

	public function get_results( $data ) {

		$params = $this->prepare_params( $data );
		if ( is_wp_error( $params ) ) {
			return $params;
		}

		$ordering = $data->get_param( 'ordering' );

		$title_search_filter = $data->get_param( 'title_search' );
		if ( ! empty( $title_search_filter ) ) {
			$params['title_search'] = $title_search_filter;
		}

		$sorting_tax   = $data->get_param( 'sort_tax' );
		$sorting_term  = $data->get_param( 'sort_term' );
		$priority_term = get_term_by( 'slug', $sorting_term, $sorting_tax );

		if ( $priority_term && empty($ordering)) {
			$params['orderby'] = 'title';
			$params['order']   = 'ASC';
		}


		if( $ordering == 'created_date') {
			$params['orderby'] = 'post_date';
			$params['order']   = 'DESC';
		}

		if( $ordering == 'modified_date' ) {
			$params['orderby'] = 'post_modified';
			$params['order']   = 'DESC';
		}

		$posts_params = $params;

		// if data has params show_taxonomies and show taxonomies params are in the query string call the taxonomies filter
		$taxonomies = $data->get_param( 'show_taxonomies' );
		if ( ! empty( $taxonomies ) ) {
			$taxonomies_filter = $this->get_filtered_taxonomies( $taxonomies, $data );
			foreach ( $taxonomies_filter as $tax => $term ) {
				$posts_params['tax_query'][] = [
					'taxonomy' => $tax,
					'fields'   => 'term_id',
					'terms'    => $this->get_taxonomy_params( $term, $tax ),
				];
			}
		}

		$exercise_types = $data->get_param( 'exercise_type' );
		if ( ! empty( $exercise_types ) ) {
			$exercise_type_filter = $this->get_filtered_data( $exercise_types );
			$posts_params['meta_query'] = [ 'relation' => 'OR' ];
			foreach ( $exercise_type_filter as $value ) {
				$posts_params['meta_query'][] = [
					'key'       => 'exercise_type',
					'value'     => $value,
					'compare'   => 'LIKE'
				];
			}
		}

		$exercise_types_how_to = $data->get_param( 'exercise_type_how_to' );
		if ( ! empty( $exercise_types_how_to ) ) {
			$exercise_type_filter = $this->get_filtered_data( $exercise_types_how_to );
			$exercise_type_terms = [];
			foreach ( $exercise_type_filter as $value ) {
				 $exercise_type_terms = $value;
			}
			$posts_params['tax_query'][] = [
				'taxonomy' => 'exercise_type',
				'field'    => 'slug',
				'terms'    => $exercise_type_terms,
				'operator' => 'IN',
			];
		}

		$muscle_group = $data->get_param( 'muscle_group' );
		if ( ! empty( $muscle_group ) ) {
			$muscle_group_filter = $this->get_filtered_data( $muscle_group );
			$muscle_group_terms = [];
			foreach ( $muscle_group_filter as $value ) {
				$muscle_group_terms = $value;
			}
			$posts_params['tax_query'][] = [
				'taxonomy' => 'muscle_group',
				'field'    => 'slug',
				'terms'    => $muscle_group_terms,
				'operator' => 'IN',
			];
		}

		$posts = $this->get_post_list( $posts_params );
		// it is important to call just after the main post query otherwise the count will be incorrect
		$post_count = $this->post_service->get_post_count();

		if ( $priority_term ) {
			$sorted_posts = $this->sort_posts( $posts, $sorting_tax, $sorting_term, $priority_term );
		}

		return [
			'posts'      => $priority_term ? $sorted_posts : $posts,
			'authors'    => $data->has_param( 'show_authors' ) ? $this->get_authors( $params, $title_search_filter ) : [],
			'taxonomies' => $taxonomies ? $this->get_taxonomies( $params, $title_search_filter, $taxonomies ) : [],
			'post_count' => $post_count,
		];
	}

	private function sort_posts( $posts, $sorting_tax, $sorting_term, $priority_term ) {
		$first_half  = [];
		$second_half = [];

		foreach ( $posts as $post ) {
			if ( isset( $post['acf'][ $sorting_tax ] ) && $priority_term->slug === $post['acf'][ $sorting_tax ]->slug ) {
				$first_half[] = $post;
			} else {
				$second_half[] = $post;
			}
		}

		return array_merge( $first_half, $second_half );
	}

	protected function get_authors( array $params, $title_search_filter ) {
		$params['posts_per_page'] = 100;
		$params['paged']          = 1;

		// empty all authors filters
		$params['author__in']     = [];
		$params['author__not_in'] = [];

		$posts = $this->get_post_list( $params );

		$authors = [];
		foreach ( $posts as $post ) {
			$author = [
				'name'         => $post['_embedded']['author'][0]['name'],
				'first_name'   => get_the_author_meta( 'first_name', $post['_embedded']['author'][0]['id'] ),
				'last_name'    => get_the_author_meta( 'last_name', $post['_embedded']['author'][0]['id'] ),
				'slug'         => $post['_embedded']['author'][0]['slug'],
				'author_image' => $post['_embedded']['author'][0]['author_image'],
				'link'         => $post['_embedded']['author'][0]['link'],
			];
			if ( ! in_array( $author, $authors ) ) {
				$authors[] = $author;
			}
		}

		return $authors;
	}

	protected function get_taxonomies( array $params, $title_search_filter, $taxonomies ) {
		$params['posts_per_page'] = 100;
		$params['paged']          = 1;

		$posts = $this->get_post_list( $params );

		$taxonomy_params = explode( ',', $taxonomies );

		$result = [];
		foreach ( $taxonomy_params as $taxonomy ) {
			$taxonomies = [];
			if ( $taxonomy === 'tags' ) {
				foreach ( $posts as $post ) {
					if ( get_the_tags( $post['id'] ) ) {

						$taxonomies = array_merge( $taxonomies, get_the_tags( $post['id'] ) );
					}
				}
				$result[ $taxonomy ] = $this->get_unique_taxonomy_names( $taxonomies );
			} elseif ( ! taxonomy_exists( $taxonomy ) ) {
				$result[ $taxonomy ] = [];
			} else {
				$taxonomies = [];
				foreach ( $posts as $post ) {

					if ( get_the_terms( $post['id'], $taxonomy ) ) {
						$taxonomies = array_merge( $taxonomies, get_the_terms( $post['id'], $taxonomy ) );
					}
				}
				$result[ $taxonomy ] = $this->get_unique_taxonomy_names( $taxonomies );
			}
		}

		return $result;
	}

	private function get_unique_taxonomy_names( $taxonomies ) {

		$simplified_tax = [];
		foreach ( $taxonomies as $taxonomy ) {

			$simple_taxonomy = [
				'name' => $taxonomy->name,
				'slug' => $taxonomy->slug,
			];

			if ( ! in_array( $simple_taxonomy, $simplified_tax ) ) {
				$simplified_tax[] = $simple_taxonomy;
			}
		}

		return $simplified_tax;
	}

	private function get_filtered_taxonomies( $taxonomies, $data ) {
		$taxonomies = explode( ',', $taxonomies );
		if ( empty( $taxonomies ) ) {
			return [];
		}
		$filtering_tax = [];
		foreach ( $taxonomies as $tax ) {
			$params = $data->get_param( $tax );
			if ( ! empty( $params ) && strlen( $params ) > 0 ) {
				$filtering_tax[ $tax ] = $params;
			}
		}

		return $filtering_tax;
	}

	private function get_filtered_data( $data ) {
		$data = explode( ',', $data );
		if ( empty( $data ) ) {
			return [];
		}

		return $data;
	}

	/**
	 * Filter query by title by query in `title_search`
	 *
	 * if empty or < 3 characters, ignore title_search
	 */
	public function title_filter( $where, $wp_query ) {
		if ( empty( $wp_query->query['title_search'] ) ) {
			return $where;
		}

		if ( strlen( $wp_query->query['title_search'] ) < 3 ) {
			return $where;
		}

		$where .= " AND post_title LIKE '%" . esc_sql( $wp_query->query['title_search'] ) . "%'";

		return $where;
	}
}
