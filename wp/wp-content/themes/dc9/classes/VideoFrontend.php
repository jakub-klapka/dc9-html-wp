<?php

namespace DC9;


class VideoFrontend extends Pagination {

	private $current_term_id;

	private $children; //array with children terms

	private $children_was_tried = false; //some kind of caching

	private $thumb_path_rel_to_upload = 'yt_thumbnails';

	private $thumb_ext = 'jpg';

	private $yt_api_key = DC9_YT_API_KEY;

	private $thumb_size = array( 160, 90 );

	public function __construct() {

		/*
		 * Current term id, which will be useful in lot of functions
		 */
		$queried_object = get_queried_object();
		$this->current_term_id = $queried_object->term_id;

		/*
		 * Tell pagination who we are
		 */
		$this->pagination_for = 'video';

		$this->video_pagination_current_term_id = $this->current_term_id;

	}

	private function get_video_id() {
		global $dc9_video;
		$url = get_field('url');
		$parsed_url = $dc9_video->get_id_from_url( $url );
		if( $parsed_url[0] === true ) {
			return $parsed_url[1];
		} else {
			file_put_contents( get_template_directory() . '/logs/video.log', PHP_EOL . date(DATE_RSS) . ' ' . $parsed_url[1], FILE_APPEND );
			return false;
		}
	}

	public function the_video_id() {
		echo $this->get_video_id();
	}

	public function enqueue_video_gallery_styles() {
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_video_gallery_styles_callback' ) );
	}

	public function enqueue_video_gallery_styles_callback() {
		wp_enqueue_style( 'video-gallery' );
		wp_enqueue_script( 'video-gallery' );
	}

	public function generate_parent_anchors( $separator ) {

		$current_term_id = $this->current_term_id;

		$terms = get_terms( 'video-category', array(
			'fields' => 'id=>parent'
		) );

		$parents = $this->get_all_term_parents( $current_term_id, $terms );

		//add current term as last parent
		$parents[] = $current_term_id;

		foreach( $parents as $term_id ) {
			$term_obj = get_term( $term_id, 'video-category' );
			$term_name = $this->get_term_name( $term_obj );
			global $sitepress;
			$term_link = $sitepress->convert_url( get_term_link( $term_obj ) );
			$separator_to_output = ( $term_id == end( $parents ) ) ? '' : $separator;
			//output
			echo sprintf( '<a href="%s">%s</a>%s',
				$term_link, $term_name, $separator_to_output
			);
		}


	}

	private function get_term_name($term_obj, $lang = ICL_LANGUAGE_CODE)
	{
		if( $lang === 'cs' ) {
			return $term_obj->name;
		}

		$en_name = get_field( 'title_' . $lang, 'video-category_' . $term_obj->term_id );

		return ( !empty( $en_name ) ) ? $en_name : $term_obj->name;
	}

	private function get_all_term_parents( $current_id, $all_terms ) {

		$output = array();
		while( true ) {
			if( isset( $all_terms[$current_id] ) ) {
				if( $all_terms[$current_id] == 0 ) {
					//we've reached last one, and it's in array, just break
					break;
				}
				//current id cycle is in terms
				$output[] = $all_terms[$current_id];
				$current_id = $all_terms[$current_id]; //move to next parent
			} else {
				break; //for sure
			}
		}

		if( empty( $output ) ) {
			return false;
		}

		$output = array_reverse( $output ); //reverse order, to have top level term first

		return $output;

	}

	private function populate_children() { // :)

		if( $this->children_was_tried ){
			return; //we dont have to do that more than once
		}

		$children = get_terms( 'video-category', array(
			'parent' => $this->current_term_id
		) );

		//Chnage name if english is on
		if( ICL_LANGUAGE_CODE !== 'cs' ) {
			foreach( $children as $key => $child ){
				$en_name = get_field( 'title_' . ICL_LANGUAGE_CODE, 'video-category_' . $child->term_id );
				if( !empty( $en_name ) ){
					//english name is not empty
					$children[$key]->name = $en_name;
				}
			}
		}


		$this->children = $children;
		$this->children_was_tried = true;

	}

	public function has_children() {

		$this->populate_children();

		$output = ( empty( $this->children ) ) ? false : true;
		return $output;

	}

	public function get_children() {

		$this->populate_children();

		if( empty( $this->children ) ) {
			return false;
		}

		/*
		 * get_term_link for every children creates lots of db queries, we have to cache that
		 */
		$term_links = $this->get_term_links(); //will generate term links for at least what we have in this->children

		$output = array();

		foreach( $this->children as $child ) {
			$item = array(
				'name' => $child->name,
				'url' => $term_links[$child->term_id]
			);
			$output[] = $item;
		}

		return $output;

	}

	/*
	 * Caching url for term->ids
	 */
	private function get_term_links() {

		$term_links = get_transient( 'video-category_term_links_' . ICL_LANGUAGE_CODE );

		if( $term_links == false ) {
			//transient don't even exists
			$term_links = array();
			foreach( $this->children as $child ) {
				$term_links[$child->term_id] = get_term_link( $child );
				if( ICL_LANGUAGE_CODE !== 'cs' ) {
					//change url to english one
					global $sitepress;
					$term_links[$child->term_id] = $sitepress->convert_url( $term_links[$child->term_id] );
				}
			}
			//we have to set transient either way
			set_transient( 'video-category_term_links_' . ICL_LANGUAGE_CODE, $term_links, YEAR_IN_SECONDS );
		}

		//next we have to check for missing ones
		$we_have_to_save_transient = false;
		foreach( $this->children as $child ) {
			if( !isset( $term_links[$child->term_id] ) ) {
				//this one is missing in links
				$term_links[$child->term_id] = get_term_link( $child );

				if( ICL_LANGUAGE_CODE !== 'cs' ) {
					//change url to english one
					global $sitepress;
					$term_links[$child->term_id] = $sitepress->convert_url( $term_links[$child->term_id] );
				}

				$we_have_to_save_transient = true;
			}
		}

		if( $we_have_to_save_transient ) {
			set_transient( 'video-category_term_links_' . ICL_LANGUAGE_CODE, $term_links, YEAR_IN_SECONDS );
		}

		return $term_links;
	}


	/**
	 * Echo out video thumb url for current id in the loop
	 */
	public function the_video_thumb_url() {
		echo $this->get_video_thumb_url();
	}

	/**
	 * Will check, if thumb for current id in loop exists and if not, will generate new one
	 * @return string
	 */
	private function get_video_thumb_url() {
		$video_id = $this->get_video_id();
		if( $video_id === false ) {
			return false;
		}
		$wp_upload_dir = wp_upload_dir();

		//set paths and dirs
		$upload_dir = $wp_upload_dir['basedir'] . DIRECTORY_SEPARATOR . $this->thumb_path_rel_to_upload;
		$upload_url = $wp_upload_dir['baseurl'] . '/' . $this->thumb_path_rel_to_upload;
		$file_path = $upload_dir . DIRECTORY_SEPARATOR . $video_id . '.' . $this->thumb_ext;
		$temp_file_path = $upload_dir . DIRECTORY_SEPARATOR . $video_id . '_temp.' . $this->thumb_ext;
		$file_url = $upload_url . '/' . $video_id . '.' . $this->thumb_ext;

		if( file_exists( $file_path ) ) {
			//thumb exists
			return $file_url;
		} else {
			//get thumb
			return $this->get_nonexisting_thumb( $temp_file_path, $file_path, $file_url );
		}
	}

	/**
	 * Will handle download and save of new thumb for video with current id in loop
	 *
	 * @param $temp_file_path string system path for temporary image
	 * @param $file_path string system path for final image
	 * @param $file_url string url of new image
	 * @return mixed url of new image or false if failed
	 */
	private function get_nonexisting_thumb( $temp_file_path, $file_path, $file_url ) {
		$thumb_url = $this->fetch_thumb_url();

		if( $thumb_url === false) {
			return false;
		}

		$temp_image = fopen( $temp_file_path, 'wb' );

		//get image from fetched url
		$curl = curl_init( $thumb_url );
		curl_setopt( $curl, CURLOPT_FILE, $temp_image );
		curl_setopt( $curl, CURLOPT_HEADER, 0 );
		curl_setopt( $curl, CURLOPT_SSL_VERIFYPEER, false );
		curl_exec( $curl );
		curl_close( $curl );
		fclose( $temp_image );

		//resize image
		$wp_image = wp_get_image_editor( $temp_file_path );
		if( is_wp_error( $wp_image ) ) {
			file_put_contents( get_template_directory() . '/logs/video.log', PHP_EOL . date( DATE_RSS ) . 'Error in loading image for resize: ' . print_r( $wp_image, true ), FILE_APPEND );
			return false;
		}
		$wp_image->resize( $this->thumb_size[0], $this->thumb_size[1], true );
		$wp_image->save( $file_path );

		//delete temp file
		unlink( $temp_file_path );

		return $file_url;

	}

	/**
	 * Will ask google API for url of video thumb with current id in loop
	 *
	 * @return mixed url of thumb on YT server or false if API request fails
	 */
	private function fetch_thumb_url() {
		$video_id = $this->get_video_id();
		if( $video_id === false ) {
			return false;
		}

		$request_url = sprintf( 'https://www.googleapis.com/youtube/v3/videos?id=%s&part=snippet&key=%s&quotaUser=dc9',
			$video_id, $this->yt_api_key);


		//query google API
		$curl = curl_init();
		curl_setopt( $curl, CURLOPT_URL, $request_url );
		curl_setopt( $curl, CURLOPT_RETURNTRANSFER, true );
		curl_setopt( $curl, CURLOPT_SSL_VERIFYPEER, false );
		$response = curl_exec( $curl );
		curl_close( $curl );

		if( $response !== false ) {
			//request sucessful
			$data = json_decode( $response );
			if( !isset( $data->items[0]->snippet->thumbnails->medium->url ) ){
				file_put_contents( get_template_directory() . '/logs/video.log', PHP_EOL . date( DATE_RSS ) . ' YT API didn\'t return required data:' . print_r( $response, true ), FILE_APPEND );
				return false;
			}
			return $data->items[0]->snippet->thumbnails->medium->url;
		} else {
			//request fails
			file_put_contents( get_template_directory() . '/logs/video.log', PHP_EOL . date( DATE_RSS ) . ' Error in requesting YT API:' . print_r( $response, true ), FILE_APPEND );
			return false;
		}
	}

	public function get_title($lang = ICL_LANGUAGE_CODE)
	{
		global $post;
		if( $lang === 'cs' ){
			//on czech page - post title is title :)
			return $post->post_title;
		} else {
			//non-czech page - get title from field
			$foreign_title = get_field( 'title_' . $lang );
			if( !empty( $foreign_title ) ) {
				return $foreign_title;
			} else {
				//foreign title is not set - return at least czech
				return $post->post_title;
			}
		}
	}

	public function the_title()
	{
		echo $this->get_title();
	}

	public function the_content($lang = ICL_LANGUAGE_CODE)
	{
		the_field( 'description_' . $lang );
	}

}


$dc9_video_frontend = new VideoFrontend();
$template_loader_instance_name = 'dc9_video_frontend';