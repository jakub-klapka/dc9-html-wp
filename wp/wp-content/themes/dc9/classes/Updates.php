<?php
/**
 * Created by PhpStorm.
 * User: Kuba
 * Date: 6.1.14
 * Time: 13:11
 */

namespace DC9;


class Updates {

	private $updates_slug = 'updates';

	public function __construct() {

		/*
		 * Register CPT
		 */
		add_action( 'init', array( $this, 'register_cpt' ) );

	}

	public function register_cpt() {

		$labels = array(
			'name'               => 'Updaty',
			'singular_name'      => 'Update',
			'add_new'            => 'Přídat',
			'add_new_item'       => 'Přidate update',
			'edit_item'          => 'Upravit update',
			'new_item'           => 'Nový update',
			'all_items'          => 'Všechny updaty',
			'view_item'          => 'Ukázat update',
			'search_items'       => 'Hledat updaty',
			'not_found'          => 'Updaty nenalezeny',
			'not_found_in_trash' => 'Updaty nenalezeny ani v koši',
			'parent_item_colon'  => 'Nadřazený update',
			'menu_name'          => 'Updaty'
		);

		register_post_type( $this->updates_slug, array(
			'labels' => $labels,
			'public' => true,
			'exclude_from_search' => true,
			'publicly_queryable' => true,
			'capability_type' => array( 'update', 'updates' ),
			'map_meta_cap' => true,
			'supports' => array( 'title', 'editor', 'revisions' ),
			'rewrite' => array(
				'slug' => _x( 'aktualizace', 'Theme', 'URL rewrite slug' )
			),
			'has_archive' => true,
			'query_var' => false
		) );
	}

}

$dc9_updates = new Updates();