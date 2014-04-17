<?php

namespace DC9;


class GalerieAdmin {

	public function __construct() {

		/*
		 * Create admin page and create menu item
		 */
		add_action( 'admin_menu', array( $this, 'add_menu_page' ) );

		/*
		 * Add meta regenerate checkbox to publish page
		 */
		add_action( 'post_submitbox_misc_actions', array( $this, 'add_checkbox_to_submit_box' ) );

		/*
		 * Add meta regenerate checkbox for update generation
		 */
		add_action( 'post_submitbox_misc_actions', array( $this, 'add_checkbox_generate_update' ) );


		/*
		 * Check for metadata update on post save
		 */
		add_action( 'save_post', array( $this, 'check_for_meta_update' ) );

	}

	public function add_menu_page() {
		add_submenu_page( 'edit.php?post_type=galerie', 'Obnova Metadat z Galerií', 'Obnova Metadat', 'edit_galleries', 'gallery_meta_update', array( $this, 'generate_admin_page' ) );
	}

	public function generate_admin_page() {
		?>
		<div class="wrap">
			<h2>Obnova metadat z galerií</h2>
			<?php $this->refresh_meta_for_all(); ?>
		</div>
		<?php
	}

	private function refresh_meta_for_all() {

		$galleries = new \WP_Query(array(
			'post_type' => 'galerie',
			'nopaging' => true
		));

		//add also other languages posts
		$this->append_other_language_posts( $galleries->posts );

		foreach( $galleries->posts as $gallery ) {

			echo '<br/>';
			echo $this->refresh_meta_for_post( $gallery, true );

		}

	}

	/**
	 * Because WP Query returns posts in current language only, this will append posts in inactive langs based on posts in $posts
	 * @param $posts array with WP_Post objects
	 */
	private function append_other_language_posts( &$posts ) {
		$languages = icl_get_languages( 'skip_missing=0' );

		foreach( $languages as $lang ) {

			if( $lang['active'] == 0 ) {
				//we have to append posts in this lang

				$posts_to_add = array();

				foreach( $posts as $single_post ) {

					$translated_object_id = icl_object_id( $single_post->ID, 'galerie', false, $lang['language_code'] );
					if( $translated_object_id != null ) {
						//There is translation for this post

						$posts_to_add[] = get_post( $translated_object_id );

					}
				}

				$posts = array_merge( $posts, $posts_to_add );
			}
		}
	}

	/**
	 * Refreshes metadata for specified post and return string with result
	 * @param $gallery object WP_Post object on which refresh metadata - have to be post type galerie
	 * @param $force_generate_update bool if we should generate updates regardless of presence of dc9_generate_update checkbox
	 * @return string with text result
	 */
	private function refresh_meta_for_post( $gallery, $force_generate_update = false ) {

		$return = '';

		if( !current_user_can( 'edit_galleries' ) ) {
			return 'Nedostatečná oprávnění';
		}

		global $dc9_galerie;

		$link = get_field( 'gallery_link', $gallery->ID );
		$ids = $dc9_galerie->extract_gallery_ids( $link );

		$parameters_from_api = $this->get_album_parameters_from_api( $ids['user'], $ids['album'] );

		$modified_time = date_create( $parameters_from_api['last_updated'] );

		if( $modified_time === false ) {
			$return .= 'Nastala chyba při parsování odpovědi z googlu pro post ID: ' . $gallery->ID;
			return $return;
		}

		$formated_time = date_format( $modified_time, 'Ymd' );

		$original_time = get_post_meta( $gallery->ID, 'gallery_last_modified', true );

		$original_photo_count = get_post_meta( $gallery->ID, 'number_of_photos', true );

		if( $original_time == $formated_time && $parameters_from_api['number_of_photos'] == $original_photo_count ) {
			$return .= 'V galerii ' . $gallery->post_title . ' se nic nezměnilo.';
		} else {

			update_post_meta( $gallery->ID, 'gallery_last_modified', $formated_time );
			update_post_meta( $gallery->ID, 'number_of_photos', $parameters_from_api['number_of_photos'] );

			$return .= 'Úspěšně obnoveny metadata pro galerii ' . $gallery->post_title . '<br>';
			$return .= '&nbsp;&nbsp;(počet fotek: ' . $parameters_from_api['number_of_photos'] . ', původně: ' . $original_photo_count . ')';

			if( isset( $_POST['dc9_generate_update'] ) || $force_generate_update == true ) {

				$this->generate_update( $original_photo_count, $parameters_from_api['number_of_photos'], $gallery );

			}

		}

		return $return;

	}


	/**
	 * @param $user_id ID of user in google picasa
	 * @param $album_id ID of album - needed in conjunction woth user id
	 * @return array parameters of album
	 */
	private function get_album_parameters_from_api( $user_id, $album_id ) {

		$fetch_url = sprintf( 'https://picasaweb.google.com/data/feed/api/user/%s/albumid/%s',
			$user_id, $album_id );

		$ch = curl_init();
		curl_setopt( $ch, CURLOPT_HEADER, false );
		curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
		curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, false );
		curl_setopt( $ch, CURLOPT_URL, $fetch_url );
		$response = curl_exec( $ch );
		curl_close( $ch );

		if( $response === false ) {
			echo 'Nastala chyba při kontaktování API';
			return;
		}

		return $this->extract_parameters_from_response( $response );

	}

	/**
	 * @param $response string XML input to parse
	 * @return array with parameters from xml
	 *      last_updated - last updated time of whole album
	 *      number_of_photos
	 */
	private function extract_parameters_from_response( $response ) {

		$xml = new \SimpleXMLElement( $response );
		$output = array();
		$output['last_updated'] = $xml->updated;
		$output['number_of_photos'] = count( $xml->entry );
		return $output;

	}

	public function add_checkbox_to_submit_box() {

		global $post;
		if( $post->post_type == 'galerie' ) {

			?>
			<div class="misc-pub-section">
				<label><input type="checkbox" name="dc9_refresh_metadata" checked="checked" /> Obnovit metadata při uložení</label>
			</div>
			<?php

		}

	}

	public function check_for_meta_update( $post_id ) {

		global $post_type;

		if( $post_type !== 'galerie' || wp_is_post_revision( $post_id ) ) {
			return;
		}

		if( isset( $_POST['dc9_refresh_metadata'] ) ) {
			//user checked metadata refresh
			global $post;
			$this->refresh_meta_for_post( $post );

		}

	}

	public function add_checkbox_generate_update() {
		global $post;
		if( $post->post_type == 'galerie' ) {

			?>
			<div class="misc-pub-section">
				<label><input type="checkbox" name="dc9_generate_update" checked="checked" /> Vygenerovat update pokud se něco změnilo</label>
			</div>
		<?php

		}
	}

	/**
	 * Generate update based on num of photos and wp post object, which is affected
	 * @param $original_photo_count int how many photos was there
	 * @param $new_photo_count int
	 * @param $wp_post object WP_Post object which is related to news
	 */
	private function generate_update( $original_photo_count, $new_photo_count, $wp_post )	{

		$post_lang = $this->get_post_language( $wp_post->ID );

		$strings = array(
			'cs' => array(
				'update_only' => 'Aktualizována galerie <a href="%s">%s</a>',
				'photos_added' => 'Přidáno %s %s do galerie <a href="%s">%s</a>'
			),
			'en' => array(
				'update_only' => 'Updated gallery <a href="%s">%s</a>',
				'photos_added' => 'Added %s %s into <a href="%s">%s</a>'
			)
		);

		//generate content of post
		if( $original_photo_count >= $new_photo_count ) {
			//something changed, but still same number of photos

			$permalink = get_permalink( $wp_post->ID );
			$update_content = sprintf( $strings[$post_lang]['update_only'],
				$permalink, $wp_post->post_title );

		} else {
			//number of photos increased

			$permalink = get_permalink( $wp_post->ID );
			$new_photos = $new_photo_count - $original_photo_count;
			$photos_text = ( $new_photos > 1 ) ? __('fotek') : __('fotka');
			$update_content = sprintf( $strings[$post_lang]['photos_added'],
				$new_photos, $photos_text, $permalink, $wp_post->post_title );
		}

		//insert post into db
		$new_page_id = wp_insert_post( array(
			'post_content' => $update_content,
			'post_title' => sanitize_text_field( $update_content ),
			'post_status' => 'publish',
			'post_type' => 'updates'
		), true );

		//insert translation
		global $sitepress;
		$sitepress->set_element_language_details( $new_page_id, 'post_updates', false, $post_lang );

	}

	private function get_post_language( $post_id ) {

		global $wpdb;

		$query = $wpdb->prepare('SELECT language_code FROM ' . $wpdb->prefix . 'icl_translations WHERE element_id=%d AND element_type=%s', $post_id, 'post_galerie');
		$query_exec = $wpdb->get_row($query);

		return $query_exec->language_code;

	}

}

$dc9_galerie_admin = new GalerieAdmin();