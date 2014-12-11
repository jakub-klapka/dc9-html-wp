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
	 * Constructor run on every pageload
	 */
	public function __construct() {

		if( function_exists('acf_add_options_page') ) {

			acf_add_options_page(array(
				'page_title' 	=> 'Obecná nastavení',
				'menu_title'	=> 'Obecná nastavení',
				'menu_slug' 	=> 'general-settings',
				'capability'	=> 'edit_posts'
			));

		}

	}


	/**
	 * Get option value based on field name
	 * @param $field_name string
	 * @return mixed
	 */
	public function getField( $field_name ) {
		return get_field( $field_name, 'option' );
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



}