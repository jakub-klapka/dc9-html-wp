<?php

$classes_path = dirname(__FILE__) . '/classes/';

/**
 * Loads class which is used on frontend templates
 *
 * @param $class_name
 * @return bool|\DC9\GalerieFrontend|\DC9\VideoFrontend|\DC9\UpdatesFrontend|\DC9\KontaktFrontend
 */
function load_template_class( $class_name ) {
	global $classes_path;
	require_once $classes_path . $class_name . '.php';
	return $$template_loader_instance_name;
}

load_textdomain( 'subscribe-reloaded', WP_LANG_DIR . '/plugins/subscribe-to-comments-reloaded-cs_CZ.mo' );

/**
 * Autoloading of classes
 */
$global = array();
$by_display = array();
$global = glob( $classes_path . '*.global.php' );
if( is_admin() ) {
	$by_display = glob( $classes_path . '*.admin.php' );
} else {
	$by_display = glob( $classes_path . '*.frontend.php' );
}
$files = array_merge( (array)$global, (array)$by_display );
if( !empty( $files ) ) {
	foreach( $files as $file ) {
		if( file_exists( $file ) ) {
			include_once $file;
		}
	}
}



/*
 * Load Layout and create public API
 */
require $classes_path . 'Layout.php';
$dc9_layout = new \DC9\Layout();

function dc9_generate_lang_switcher() {
	global $dc9_layout;
	$dc9_layout->generateLangSwitcher();
}

function dc9_generate_main_menu() {
	global $dc9_layout;
	$dc9_layout->generateMainMenu();
}

function enqueue_home_styles() {
	global $dc9_layout;
	$dc9_layout->enqueue_home_styles();
}

function get_url_from_anchor( $markup ) {
	global $dc9_layout;
	return $dc9_layout->get_url_from_anchor( $markup );
}


/**
 * Admin only functions
 */
if( is_admin() ){
	require_once $classes_path . 'Admin.php';
	require_once $classes_path . 'dashboard_activity.php';
}

/**
 * Frontend shortcodes
 */
if( !is_admin() ) {
	require_once $classes_path . 'ShortcodesFrontend.php';
}

/*
 * Theme options class and functions
 */
require $classes_path . 'ThemeOptions.php';
$dc9_theme_options = new \DC9\ThemeOptions();

function dc9_get_option( $field_name ) {
	global $dc9_theme_options;
	return $dc9_theme_options->getField( $field_name );
}

function dc9_get_the_option( $field_name ) {
	global $dc9_theme_options;
	return $dc9_theme_options->getTheField( $field_name );
}

/*
 * News
 */
require $classes_path . 'News.php';
$dc9_news = new \DC9\News();

function enqueue_news_archive_style() {
	global $dc9_news;
	$dc9_news->enqueue_archive_style();
}

function the_post_thumbnail_url() {
	global $dc9_news;
	echo $dc9_news->the_post_thumbnail_url();
}

function news_has_more() {
	global $dc9_news;
	return $dc9_news->news_has_more();
}

function get_news_pagination_links() {
	global $dc9_news;
	return $dc9_news->get_pagination_links();
}

function is_paginated() {
	global $dc9_news;
	return $dc9_news->is_paginated();
}

function is_first_page() {
	global $dc9_news;
	return $dc9_news->is_first_page();
}

function is_last_page() {
	global $dc9_news;
	return $dc9_news->is_last_page();
}

function the_previous_page_url() {
	global $dc9_news;
	echo $dc9_news->previous_page_url();
}

function the_next_page_url() {
	global $dc9_news;
	echo $dc9_news->next_page_url();
}

function set_home_news_query() {
	global $dc9_news;
	$dc9_news->set_home_news_query();
}


/*
 * Video base register
 */
require $classes_path . 'Video.php';

/*
 * Gallery base
 */
require $classes_path . 'Galerie.php';

/*
 * Load Gallery admin in admin
 */
if( is_admin() ) {
	require $classes_path . 'GalerieAdmin.php';
}

/*
 * Load plugin modifications
 */
require $classes_path . 'PluginModifications.php';

/*
 * Updates base
 */
require $classes_path . 'Updates.php';

/*
 * Updates for admin only
 */
if( is_admin() ) {
	require $classes_path . 'UpdatesAdmin.php';
}

/*
 * Video admin
 */
if( is_admin() ) {
	require $classes_path . 'VideoAdmin.php';
}


/*
 * Comments
 */
require $classes_path . 'Comments.php';

/*
 * Newsletter
 */
require $classes_path . 'Newsletter.php';
