<?php

namespace DC9;


class Pagination {

	protected $pagination_for;

	protected $video_pagination_current_term_id;

	public function get_pagination_links() {
		global $wp_query;
		$current_page = ( $wp_query->query_vars['paged'] == 0 ) ? 1 : $wp_query->query_vars['paged'] ;
		if( is_post_type_archive( 'updates' ) ) {
			if( ICL_LANGUAGE_CODE == 'cs' ) { //WPML bughack
				$base = get_post_type_archive_link( 'updates' );
			} else {
				$base = get_bloginfo( 'url' ) . 'updates/';
			}
		} else {
			switch( $this->pagination_for ) {
				case( 'novinky' ):
					if( ICL_LANGUAGE_CODE == 'cs' ) { //WPML bughack
						$base = get_post_type_archive_link( 'novinky' );
					} else {
						$base = get_bloginfo( 'url' ) . 'news/';
					}
				break;
				case( 'video' ):
					$base = get_term_link( $this->video_pagination_current_term_id, 'video-category' );
				break;
			}
		}

		$links = paginate_links( array(
			'base'         => $base . '%_%',
			'format'       => 'page/%#%',
			'total'        => $wp_query->max_num_pages,
			'current'      => $current_page,
			'show_all'     => False,
			'end_size'     => 1,
			'mid_size'     => 3,
			'prev_next'    => false,
			'type'         => 'array'
		) );
		return $links;
	}

	public function is_paginated() {
		global $wp_query;
		if( $wp_query->max_num_pages > 1 ) {
			return true;
		} else {
			return false;
		}
	}

	public function is_first_page() {
		global $wp_query;
		$is_first_page = ( $wp_query->query_vars['paged'] == 0 ) ? true : false;
		return $is_first_page;
	}

	public function is_last_page() {
		global $wp_query;
		$is_last_page = ( $wp_query->query_vars['paged'] == $wp_query->max_num_pages ) ? true : false;
		return $is_last_page;
	}

	public function previous_page_url() {
		$anchor = get_previous_posts_link();
		preg_match( '/href="(.+)"/', $anchor, $matches );
		return $matches[1];
	}

	public function next_page_url() {
		$anchor = get_next_posts_link();
		preg_match( '/href="(.+)"/', $anchor, $matches );
		return $matches[1];
	}

} 