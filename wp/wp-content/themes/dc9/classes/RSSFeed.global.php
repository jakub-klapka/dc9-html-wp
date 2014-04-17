<?php

namespace DC9\Glob;


class RSSFeed {

	public function __construct()
	{

		/**
		 * Disable default feed rewrites
		 */
		add_filter( 'rewrite_rules_array', array( $this, 'disable_default_feed_rw' ) );

		/**
		 * add feed query var
		 */
		add_filter( 'query_vars', array( $this, 'add_feed_query_var' ) );

		/**
		 * Add rule to template redirect
		 */
		add_action( 'template_redirect', array( $this, 'template_redirect' ) );


	}

	public function disable_default_feed_rw($rules)
	{
		//disable all feeds
		foreach( $rules as $key => $rule ) {
			if( strpos( $key, 'feed' ) !== false ){
				unset( $rules[$key] );
			}
		}

		//add our feed on first place
		$new_rule = array( 'feed/?$' => 'index.php?dc9_feed=true' );
		$rules = array_merge( $new_rule, $rules );

		return $rules;
	}

	public function add_feed_query_var($query_vars)
	{
		$query_vars[] = 'dc9_feed';
		return $query_vars;
	}

	public function template_redirect()
	{
		if( !is_admin() && get_query_var( 'dc9_feed' ) ){
			add_filter( 'template_include', array( $this, 'include_custom_rss_template' ) );
		}
	}

	public function include_custom_rss_template($template)
	{
		return get_template_directory() . '/feed-rss2.php';
	}

}

$dc9_global_rssfeed = new RSSFeed();
