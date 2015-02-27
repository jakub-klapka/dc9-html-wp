<?php

namespace DC9;


class Comments {

	public function __construct() {

		/*
		 * Comments
		 */
		add_action( 'after_setup_theme', array( $this, 'add_html5_support' ) );

	}

	public function add_html5_support() {
		add_theme_support( 'html5', array( 'comment-form', 'comment-list' ) );
	}

}

$dc9_comments = new Comments();