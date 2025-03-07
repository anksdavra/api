<?php

namespace CroissantApi\Endpoint;

use WpTapestryPlugin\Service\PostInterface;

/**
 * @SuppressWarnings(PHPMD)
 */
class PostListApi extends PostList {

	protected $post_service;
	protected $route_args = [
		'ids'                => [
			'required' => false,
		],
		'tags'               => [
			'required' => false,
		],
		'tags_exclude'       => [
			'required' => false,
		],
		'categories'         => [
			'required' => false,
		],
		'categories_exclude' => [
			'required' => false,
		],
		'authors'            => [
			'required' => false,
		],
		'authors_exclude'    => [
			'required' => false,
		],
		'post_types'         => [
			'required' => false,
		],
		'per_page'           => [
			'required' => false,
		],
		'page'               => [
			'required' => false,
		],
		'offset'             => [
			'required' => false,
		],
		'orderby'            => [
			'required' => false,
		],
		'order'              => [
			'required' => false,
		],
		'series'             => [
			'required' => false,
		],
		'series_exclude'     => [
			'required' => false,
		],
		'packages'           => [
			'required' => false,
		],
		'packages_exclude'   => [
			'required' => false,
		],
		'sticky'             => [
			'required' => false,
		],
		'visibility'         => [
			'required' => false,
		],
		'visibility_exclude' => [
			'required' => false,
		],
		'posts_exclude'      => [
			'required' => false,
		],
	];

	public function __construct( PostInterface $post_service ) {
		parent::__construct( $post_service );
	}

	public function register_route() {
		register_rest_route(
			'croissant/v1', '/posts', [
				'methods'  => 'GET',
				'callback' => [ $this, 'get_posts' ],
				'args'     => $this->route_args,
			]
		);
	}

	public function get_posts( $data ) {
		$params = $this->prepare_params( $data );

		if ( $params instanceof \WP_Error ) {
			return $params;
		}

		return $this->get_post_list( $params );
	}

	protected function prepare_params( $data ) {
		$data_params = $this->process_params( $data );
		if ( empty( $data_params ) ) {
			return new \WP_Error( 'no_posts', 'No posts returned for this filter', [ 'status' => 404 ] );
		}

		$per_page = $data->get_param( 'per_page' );
		if ( $per_page && $per_page > 200 ) {
			$per_page = 200;
		}

		$params = [
			'paged'               => $data->get_param( 'page' ) ?: 1,
			'posts_per_page'      => $per_page ?: 10,
			'tag__in'             => $data_params['tags'],
			'tag__not_in'         => $this->get_tag_params( $data->get_param( 'tags_exclude' ) ),
			'cat'                 => $data_params['categories'],
			'category__not_in'    => $this->get_category_params( $data->get_param( 'categories_exclude' ) ),
			'author__in'          => $data_params['authors'],
			'author__not_in'      => $this->get_author_params( $data->get_param( 'authors_exclude' ) ),
			'post_type'           => $data_params['post_types'],
			'ignore_sticky_posts' => 1,
			'order'               => $data->get_param( 'order' ) ?: 'DESC',
		];

		$excluded_posts = $data->get_param( 'posts_exclude' );
		if ( $excluded_posts ) {
			$excluded_posts         = explode( ',', $excluded_posts );
			$params['post__not_in'] = $excluded_posts;
		}

		$fav_ids_string = $data->get_param( 'ids' );

		if ( strlen( trim( $fav_ids_string ) ) > 0 ) {
			$fav_ids            = explode( ',', $fav_ids_string );
			$params['post__in'] = $fav_ids;
		}

		$offset = $data->get_param( 'offset' );
		if ( ! empty( $offset ) && is_numeric( $offset ) ) {
			$params['offset'] = $offset;
		}

		if ( ! empty( $data_params['orderby'] ) ) {
			$params['orderby'] = $data_params['orderby'];
		}

		if ( ! empty( $data_params['series'] ) ) {
			$series = [
				'taxonomy' => 'series',
				'fields'   => 'term_id',
				'terms'    => $data_params['series'],
			];
		}

		$series_exclude = $this->get_series_params( $data->get_param( 'series_exclude' ) );
		if ( ! empty( $series_exclude ) ) {
			$series_exclude = [
				'taxonomy' => 'series',
				'fields'   => 'term_id',
				'terms'    => $series_exclude,
				'operator' => 'NOT IN',
			];
		}

		if ( ! empty( $data_params['packages'] ) ) {
			$packages = [
				'taxonomy' => 'premium',
				'fields'   => 'term_id',
				'terms'    => $data_params['packages'],
			];
		}

		$packages_exclude = $this->get_premium_params( $data->get_param( 'packages_exclude' ) );
		if ( ! empty( $packages_exclude ) ) {
			$packages_exclude = [
				'taxonomy' => 'premium',
				'fields'   => 'term_id',
				'terms'    => $packages_exclude,
				'operator' => 'NOT IN',
			];
		}

		if ( ! empty( $data_params['visibility'] ) ) {
			$visibility = [
				'taxonomy' => 'visibility',
				'fields'   => 'term_id',
				'terms'    => $data_params['visibility'],
			];
		}

		$visibility_exclude = $this->get_visibility_params( $data->get_param( 'visibility_exclude' ) );
		if ( ! empty( $visibility_exclude ) ) {
			$visibility_exclude = [
				'taxonomy' => 'visibility',
				'fields'   => 'term_id',
				'terms'    => $visibility_exclude,
				'operator' => 'NOT IN',
			];
		}

		foreach ( [
			$series ?? false,
			$series_exclude ?? false,
			$packages ?? false,
			$packages_exclude ?? false,
			$data_params['visibility'] ?? false,
			$visibility_exclude ?? false,
		] as $tax ) {
			if ( ! $tax ) {
				continue;
			}

			$params['tax_query'][] = $tax;
		}

		$sticky = $data->get_param( 'sticky' );
		if ( $sticky !== null ) {
			$post_param            = (bool) $sticky === true ? 'post__in' : 'post__not_in';
			$params[ $post_param ] = get_option( 'sticky_posts' );
		}

		return $params;
	}

	protected function get_post_list( array $params ) {
		return $this->post_service->get_post_list( $params );
	}

	private function process_params( $data ) {
		$tags = $this->get_tag_params( $data->get_param( 'tags' ) );
		if ( $tags === false ) {
			return [];
		}

		$categories = $this->get_category_params( $data->get_param( 'categories' ) );
		if ( $categories === false ) {
			return [];
		}

		$authors = $this->get_author_params( $data->get_param( 'authors' ) );
		if ( $authors === false ) {
			return [];
		}

		$series = $this->get_series_params( $data->get_param( 'series' ) );
		if ( $series === false ) {
			return [];
		}

		$packages = $this->get_premium_params( $data->get_param( 'packages' ) );
		if ( $series === false ) {
			return [];
		}

		$post_types = $this->get_post_type_params( $data->get_param( 'post_types' ) );
		if ( $post_types === false ) {
			return [];
		}

		$visibility = $this->get_visibility_params( $data->get_param( 'visibility' ) );
		if ( $visibility === false ) {
			return [];
		}

		$valid_orderby = [
			'ID',
			'title',
			'type',
			'date',
			'modified',
			'parent',
			'rand',
		];
		$orderby       = $data->get_param( 'orderby' );
		if ( ! empty( $orderby ) && ! in_array( $orderby, $valid_orderby, true ) ) {
			return [];
		}

		return [
			'tags'       => $tags,
			'categories' => $categories,
			'authors'    => $authors,
			'series'     => $series,
			'packages'   => $packages,
			'post_types' => $post_types,
			'visibility' => $visibility,
			'orderby'    => $orderby,
		];
	}

	private function get_tag_params( $tags ) {
		if ( empty( $tags ) ) {
			return [];
		}

		$tag_ids = [];
		foreach ( explode( ',', $tags ) as $tag ) {
			$tag = get_term_by( 'slug', $tag, 'post_tag' );
			if ( ! $tag ) {
				continue;
			}

			$tag_ids[] = $tag->term_id;
		}

		return ! empty( $tag_ids ) ? $tag_ids : false;
	}

	private function get_category_params( $categories ) {
		if ( empty( $categories ) ) {
			return [];
		}

		$cat_ids = [];
		foreach ( explode( ',', $categories ) as $cat ) {
			$cat = get_term_by( 'slug', $cat, 'category' );
			if ( ! $cat ) {
				continue;
			}

			$cat_ids[] = $cat->term_id;
		}

		return ! empty( $cat_ids ) ? $cat_ids : false;
	}

	private function get_author_params( $authors ) {
		if ( empty( $authors ) ) {
			return [];
		}

		$user_ids = [];
		foreach ( explode( ',', $authors ) as $user ) {
			$user = get_user_by( 'slug', $user );
			if ( ! $user ) {
				continue;
			}

			$user_ids[] = $user->ID;
		}

		return ! empty( $user_ids ) ? $user_ids : false;
	}

	private function get_post_type_params( $post_types ) {
		if ( empty( $post_types ) ) {
			$post_types = $this->get_public_post_types();
			unset( $post_types['tile'] );
			unset( $post_types['list'] );

			return $post_types;
		}

		$return = [];
		foreach ( explode( ',', $post_types ) as $post_type ) {
			if ( ! post_type_exists( $post_type ) ) {
				continue;
			}

			$return[] = $post_type;
		}

		return ! empty( $return ) ? $return : false;
	}

	private function get_series_params( $series ) {
		return $this->get_taxonomy_params( $series, 'series' );
	}

	private function get_premium_params( $premium ) {
		return $this->get_taxonomy_params( $premium, 'premium' );
	}

	private function get_visibility_params( $visibility ) {
		return $this->get_taxonomy_params( $visibility, 'visibility' );
	}

	protected function get_taxonomy_params( $value, $taxonomy_name ) {
		if ( empty( $value ) ) {
			return [];
		}

		$taxonomy_ids = [];
		foreach ( explode( ',', $value ) as $taxonomy ) {
			$tax = get_term_by( 'slug', $taxonomy, $taxonomy_name );
			if ( ! $tax ) {
				continue;
			}

			$taxonomy_ids[] = $tax->term_id;
		}

		return ! empty( $taxonomy_ids ) ? $taxonomy_ids : false;
	}
}
