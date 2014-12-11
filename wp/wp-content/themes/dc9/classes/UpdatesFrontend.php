<?php

namespace DC9;

class UpdatesFrontend extends Pagination {

	public function __construct()
	{
		/*
		 * Tell pagination who we are
		 */
		$this->pagination_for = 'updates';				add_filter( 'wpseo_canonical', array( $this, 'check_for_updates_canonical_url' ) );		add_filter( 'wpseo_prev_rel_link', array( $this, 'check_for_updates_canonical_url' ) );		add_filter( 'wpseo_next_rel_link', array( $this, 'check_for_updates_canonical_url' ) );
	}

	public function setup_updates() {
		$query = new \WP_Query( array(
			'post_type' => 'updates',
			'posts_per_page' => dc9_get_option( 'how_many_updates' )
		) );

		return $query;
	}

	public function setup_postdata( $updates_query ) {
		global $post;
		$post = $updates_query->post;
		setup_postdata( $post );
	}

	/**
	 * Echo content without wpautop
	 */
	public function the_content() {
		echo get_the_content();
	}

	public function enqueue_archive_style()
	{
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_archive_style_callback' ) );
	}

	public function enqueue_archive_style_callback()
	{
		wp_enqueue_style( 'updates-archive', get_template_directory_uri() . '/css/archive-updates.css', array( 'layout' ), DC9_CSS_VERSION );
	}

	public function the_updates_archive_link()
	{
		if( ICL_LANGUAGE_CODE == 'cs' ) {
			echo get_post_type_archive_link( 'updates' );
		} else {
			echo get_bloginfo( 'url' ) . 'updates';
		}
	}			public function check_for_updates_canonical_url( $url ) {				if( strpos( $url, 'en/aktualizace/' ) !== false ) {			return str_replace( 'en/aktualizace/', 'en/updates/', $url );		}				return $url;			}

}

$dc9_updates_frontend = new UpdatesFrontend();
$template_loader_instance_name = 'dc9_updates_frontend';
