<?php
/*
 * Theme options CPT and functions
 */

namespace DC9;


/**
 * Class ThemeOptions
 *
 * Class for theme options, mostly layout, wchic van be set form admin
 * @package DC9
 */
class ThemeOptions {

	/**
	 * Slug for this CPT
	 * @var string
	 */
	private  $cpt_slug = 'theme_options';

	/**
	 * Base name for post id caching transient
	 * @var string
	 */
	private $post_id_transient_base = 'theme_options_id_';

	/**
	 * Constructor run on every pageload
	 */
	public function __construct() {

		/*
		 * Register CPT
		 */
		add_action( 'init', array( $this, 'registerThemeOptionsCPT' ) );

		/*
		 * Check for transient deletion
		 */
		add_action( 'save_post', array( $this, 'checkPostIDTransient' ) );

	}

	/**
	 * WP function for register CPT
	 *
	 * add action on init
	 */
	public function registerThemeOptionsCPT() {
		$labels = array(
			'name'               => 'Možnosti šablony',
			'singular_name'      => 'Možnosti šablony',
			'add_new'            => 'Přidat',
			'add_new_item'       => 'Přidat skupinu vlastností',
			'edit_item'          => 'Upravit skupinu',
			'new_item'           => 'Nová skupina vlastností',
			'all_items'          => 'Všechny skupiny',
			'view_item'          => 'Ukázat skupinu',
			'search_items'       => 'Hledat skupinu',
			'not_found'          => 'Žádná skupina nenalezena',
			'not_found_in_trash' => 'Žádná skupina nenalezena ani v koši',
			'parent_item_colon'  => 'Nadřazená skupina',
			'menu_name'          => 'Možnosti šablony'
		);
		register_post_type( $this->cpt_slug, array(
			'labels' => $labels,
			'public' => false,
			'show_ui' => true,
			'show_in_admin_bar' => false,
			'supports' => array( 'title', 'revisions' )
		) );
	}

	/**
	 * Get option value based on field name
	 * @param $field_name string
	 * @return mixed
	 */
	public function getField( $field_name ) {
		$post_id = $this->getThemeOptionsPostID();
		return get_field( $field_name, $post_id );
	}

	/**
	 * Get option based on field name and echo it rightaway
	 * @param $field_name string
	 * @return mixed
	 */
	public function getTheField( $field_name ) {
		echo $this->getField( $field_name );
		return true;
	}

	/**
	 * Helper for geting specific post ID based on language
	 *
	 * and caching
	 * @return int
	 */
	private function getThemeOptionsPostID() {
		/*
		 * Caching based on language ID - to avoid querying whole WP query
		 */
		$post_id = get_transient( 'theme_options_id_' . ICL_LANGUAGE_CODE );
		if( $post_id === false ) {
			$args = array(
				'post_type' => $this->cpt_slug,
				'posts_per_page' => 1
			);
			$query = new \WP_Query( $args );
			$post_id = $query->post->ID;
			set_transient( $this->post_id_transient_base . ICL_LANGUAGE_CODE, $post_id, YEAR_IN_SECONDS );
		}

		return $post_id;
	}

	/**
	 * Checking post id transient
	 * @param $post_id
	 */
	public function checkPostIDTransient( $post_id ) {
		if( wp_is_post_revision( $post_id ) ){
			return;
		}
		if( get_post_type( $post_id ) === $this->cpt_slug ) {
			//we saved post type options and need to delete transients for all languages
			$languages = icl_get_languages( 'skip_missing=0' );
			foreach( $languages as $lang ){
				delete_transient( $this->post_id_transient_base . $lang['language_code'] );
			}
		}
	}
}