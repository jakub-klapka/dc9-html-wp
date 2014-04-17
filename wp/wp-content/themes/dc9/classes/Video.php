<?php

namespace DC9;


class Video {

	public $cpt_slug = 'video';

	public $taxonomy_slug = 'video-category';

	public function __construct() {

		/*
		 * Register post type
		 */
		add_action( 'init', array( $this, 'register_CPT' ) );

		/*
		 * Register taxonomy
		 */
		add_action( 'init', array( $this, 'register_taxonomy' ) );

		/*
		 * check_term_links_transient
		 */
		add_action( 'create_term', array( $this, 'check_term_links_transient' ) );
		add_action( 'edit_term', array( $this, 'check_term_links_transient' ) );
		add_action( 'delete term', array( $this, 'check_term_links_transient' ) );

		/**
		 * Video category in english menu
		 */
		add_filter( 'nav_menu_link_attributes', array( $this, 'change_url_to_en_in_menu' ), 10, 3 );

	}

	public function register_CPT() {
		$labels = array(
			'name'               => 'Videa',
			'singular_name'      => 'Video',
			'add_new'            => 'Přidat',
			'add_new_item'       => 'Přidat video',
			'edit_item'          => 'Upravit video',
			'new_item'           => 'Nové video',
			'all_items'          => 'Všechny videa',
			'view_item'          => 'Ukázat video',
			'search_items'       => 'Hledat videa',
			'not_found'          => 'Videa nenalezeny',
			'not_found_in_trash' => 'Videa nenalezeny ani v koši',
			'parent_item_colon'  => 'Nadřazené video',
			'menu_name'          => 'Videa'
		);

		$args = array(
			'labels' => $labels,
			'public' => true,
			'capability_type' => array( 'video', 'videos' ),
			'map_meta_cap' => true,
			'supports' => array( 'title', 'revisions' ),
			'taxonomies' => array( $this->taxonomy_slug ),
			'has_archive' => true
		);

		register_post_type( $this->cpt_slug, $args );
	}

	public function register_taxonomy() {

		$labels = array(
			'name'              => 'Kategorie videí',
			'singular_name'     => 'Kategorie videa',
			'search_items'      => 'Hledat kategorie',
			'all_items'         => 'Všechny kategorie',
			'parent_item'       => 'Nadřazená kategorie',
			'parent_item_colon' => 'Nadřazená kategorie:',
			'edit_item'         => 'Upravit kategorii',
			'update_item'       => 'Aktualizovat kategorii',
			'add_new_item'      => 'Přidat kategorii',
			'new_item_name'     => 'Jméno nové kategorie',
			'menu_name'         => 'Kategorie videí',
		);

		$args = array(
			'labels' => $labels,
			'show_admin_column' => true,
			'hierarchical' => true,
			'rewrite' => array(
				'hierarchical' => true,
				'slug' => 'videa'
			),
			'capabilities' => array(
				'manage_terms' => 'manage_video-categories',
				'edit_terms' => 'manage_video-categories',
				'delete_terms' => 'manage_video-categories',
				'assign_terms' => 'edit_videos'
			)
		);

		register_taxonomy( $this->taxonomy_slug, $this->cpt_slug, $args );

	}


	/**
	 * @param $url
	 * @return array    [0] => bolean - if error occured
	 *                  [1] => ID of video if true, error if false
	 */
	public function get_id_from_url( $url ) {
		preg_match( '/(v=|\/v\/|youtu.be\/)(.+?)(&|\#|$)/', $url, $matches );
		if( isset( $matches[2] ) ) {
			return array( true, $matches[2] );
		} else {
			return array( false, 'ID of video wasn\'t found in URL: ' . $url );
		}
	}

	public function check_term_links_transient() {
		if( isset( $_POST['taxonomy'] ) && $_POST['taxonomy'] == 'video-category' ) {
			$languages = icl_get_languages( 'skip_missing=0' );
			foreach( $languages as $lang ) {
				delete_transient( 'video-category_term_links_' . $lang['language_code'] );
			}
		}
	}

	public function change_url_to_en_in_menu($atts, $item, $args)
	{
		if( ICL_LANGUAGE_CODE === 'en' && isset( $item->object ) && $item->object === 'video-category' && isset( $item->type ) && $item->type="taxonomy" ){
			//we are on english and currently displaying video cats
			global $sitepress;
			$atts['href'] = $sitepress->convert_url( $atts['href'], 'en' );
		}
		return $atts;
	}

}

/*
 * Instanciate
 */
$dc9_video = new Video;
