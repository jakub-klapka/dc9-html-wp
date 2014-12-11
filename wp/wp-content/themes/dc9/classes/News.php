<?php

namespace DC9;

global $classes_path;
require $classes_path . 'Pagination.php';


class News extends Pagination {

	private $english_slug = 'news';

	public function __construct() {

		/*
		 * Register CPT
		 */
		add_action( 'init', array( $this, 'register_cpt' ) );

		/*
		 * Add theme support for post thumbnails
		 */
		add_action( 'after_setup_theme', array( $this, 'support_post_thumbnails' ) );

		/*
		 * Enable showing all posts in archive
		 * Maybe
		 */
		/*global $sitepress;
		remove_filter('posts_join', array($sitepress,'posts_join_filter'), 10, 2);
		remove_filter('posts_where', array($sitepress,'posts_where_filter'), 10, 2);*/

		/*
		 * Tell pagination who we are
		 */
		$this->pagination_for = 'novinky';								add_filter( 'wpseo_canonical', array( $this, 'check_for_news_canonical_url' ) );		add_filter( 'wpseo_prev_rel_link', array( $this, 'check_for_news_canonical_url' ) );		add_filter( 'wpseo_next_rel_link', array( $this, 'check_for_news_canonical_url' ) );

	}

	public function register_cpt() {

		$labels = array(
			'name'               => 'Novinky',
			'singular_name'      => 'Novinka',
			'add_new'            => 'Přidat',
			'add_new_item'       => 'Přidat novinku',
			'edit_item'          => 'Upravit novinku',
			'new_item'           => 'Nová novinka',
			'all_items'          => 'Všechny novinky',
			'view_item'          => 'Ukázat novinku',
			'search_items'       => 'Hledat novinky',
			'not_found'          => 'Novinky nenalezeny',
			'not_found_in_trash' => 'Novinky nenalezeny ani v koši',
			'parent_item_colon'  => 'Nadřazená novinka',
			'menu_name'          => 'Novinky'
		);

		register_post_type( 'novinky', array(
			'labels' => $labels,
			'description' => 'Hlavní novinky postupu na projektu',
			'public' => true,
			'capability_type' => array( 'news', 'newses' ), //I know it's weird, but there has to be different plural to avoid bugs
			'map_meta_cap' => true,
			'supports' => array( 'title', 'editor', 'revisions', 'thumbnail', 'excerpt' ),
			'has_archive' => true,
			'rewrite' => array(
				'slug' => _x( 'novinky', 'URL slug', 'Theme' )
			)
		) );

	}

	public function support_post_thumbnails() {
		add_theme_support( 'post-thumbnails', array( 'novinky' ) );
		set_post_thumbnail_size( 100, 100, true );
	}

	public function enqueue_archive_style() {
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_archive_style_callback' ) );
	}

	public function enqueue_archive_style_callback() {
		wp_enqueue_style( 'home' );
	}

	public function the_post_thumbnail_url() {
		$image_src = wp_get_attachment_image_src( get_post_thumbnail_id(), 'post-thumbnail' );
		return $image_src[0];
	}

	public function news_has_more() {
		global $post;
		$post_content = $post->post_content;
		if( strpos( $post_content, '<!--more-->' ) !== false ) {
			return true;
		}
		return false;
	}

	public function set_home_news_query() {

		query_posts( array(
			'post_type' => 'novinky',
			'posts_per_page' => 4 //TODO: make it changeable
		) );

	}

	public function the_news_archive_link() {
		if( ICL_LANGUAGE_CODE == 'cs' ) {
			echo get_post_type_archive_link( 'novinky' );
		} elseif ( ICL_LANGUAGE_CODE == 'en' ) {
			echo get_bloginfo('url') . $this->english_slug . '/';
		}

		//TODO: handle click on ENGLISH when on english news archive
	}		public function check_for_news_canonical_url( $url ) {				if( strpos( $url, 'en/novinky/' ) !== false ) {			return str_replace( 'en/novinky/', 'en/news/', $url );		}				return $url;			}



}

