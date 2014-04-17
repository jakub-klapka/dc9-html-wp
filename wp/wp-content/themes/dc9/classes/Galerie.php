<?php

namespace DC9;


class Galerie {

	public function __Construct() {

		/*
		 * Register CPT
		 */
		add_action( 'init', array( $this, 'register_cpt' ) );

		/*
		 * register gallery featured image size
		 */
		add_image_size( 'gallery_featured', 430, 200, true );

		/*
		 * Modify archive query to sort by last modified field
		 */
		add_action( 'pre_get_posts', array( $this, 'sort_main_query_by_modified' ) );

	}

	public function register_cpt() {

		$labels = array(
			'name'               => 'Galerie',
			'singular_name'      => 'Galerie',
			'add_new'            => 'Přidat',
			'add_new_item'       => 'Přidat galerii',
			'edit_item'          => 'Upravit galerii',
			'new_item'           => 'Nová galerie',
			'all_items'          => 'Všechny galerie',
			'view_item'          => 'Ukázat galerii',
			'search_items'       => 'Hledat galerie',
			'not_found'          => 'Galerie nenalezeny',
			'not_found_in_trash' => 'Galerie nenalezeny ani v koši',
			'parent_item_colon'  => 'Nadřazená galerie',
			'menu_name'          => 'Galerie'
		);

		register_post_type( 'galerie', array(
			'labels' => $labels,
			'public' => true,
			'capability_type' => array( 'gallery', 'galleries' ),
			'map_meta_cap' => true,
			'supports' => array( 'title', 'editor', 'revisions' ),
			'has_archive' => true,
			'rewrite' => array(
				'slug' => _x( 'galerie', 'URL Slug', 'Theme' ),
				'pages' => false
			)
		) );

	}

	public function sort_main_query_by_modified( $query ) {
		if( !is_admin() &&
			$query->is_main_query() &&
			isset( $query->query['post_type'] ) &&
			$query->query['post_type'] == 'galerie' &&
			!isset( $query->query['galerie'] ) )
			{
				//We are dealing with galerie archive page

				$query->set( 'orderby', 'meta_value_num' );
				$query->set( 'meta_key', 'gallery_last_modified' );
			}
	}

	public function extract_gallery_ids( $link ) {

		preg_match( '/\/photos\/(\d+?)\/albums\/(\d+)/', $link, $matches );

		if( isset( $matches[1] ) && isset( $matches[2] ) ) {
			$output = array(
				'user' => $matches[1],
				'album' => $matches[2]
			);
			return $output;
		}

		return false;

	}

}

$dc9_galerie = new Galerie();