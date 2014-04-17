<?php

namespace DC9;


class PluginModifications {

	public function __construct() {

		/*
		 * Remove media attachement box in admin
		 */
		global $WPML_media;
		remove_action( 'icl_post_languages_options_after', array( $WPML_media, 'language_options' ) );

		/*
		 * Remove Copy content from Czech - as it dont work
		 */
		add_action( 'admin_head', array( $this, 'remove_copy_from_language_button' ) );

		/*
		 * Disable WP SEO scores
		 */
		add_filter( 'wpseo_use_page_analysis', array( $this, 'disable_wpseo_scores' ) );

		/*
		 * Remove PE2 styles
		 */
		add_action( 'wp_enqueue_scripts', array( $this, 'remove_default_pe2_styles' ) );

		/**
		 * WP SEO metabox lower prio
		 */
		add_filter( 'wpseo_metabox_prio', create_function(false, 'return "low";') );

		/**
		 * ICL css
		 */
		define( 'ICL_DONT_LOAD_LANGUAGE_SELECTOR_CSS', true );
		define( 'ICL_DONT_LOAD_LANGUAGES_JS', true );

		/**
		 * Flamingo roles
		 */
		remove_filter( 'map_meta_cap', 'flamingo_map_meta_cap' );


	}

	public function remove_copy_from_language_button() {
		global $sitepress;
		remove_action('icl_post_languages_options_after', array($sitepress, 'copy_from_original'));
	}

	public function disable_wpseo_scores() {
		return false;
	}

	public function remove_default_pe2_styles() {
		wp_dequeue_style( 'pe2-display.css' );
	}

}

$dc9_plugin_modifications = new PluginModifications();