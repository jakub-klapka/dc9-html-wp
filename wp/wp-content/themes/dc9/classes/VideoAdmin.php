<?php


namespace DC9;


class VideoAdmin {

	private $cpt_slug;

	private $taxonomy_slug;

	public function __construct() {

		/*
		 * Set cpt slug from global
		 */
		global $dc9_video;
		$this->cpt_slug = $dc9_video->cpt_slug;
		$this->taxonomy_slug = $dc9_video->taxonomy_slug;

		/*
		 * Add meta regenerate checkbox to publish page
		 */
		add_action( 'post_submitbox_misc_actions', array( $this, 'create_updates_checkbox' ) );

		/*
		 * Check for metadata update on post save
		 */
		add_action( 'save_post', array( $this, 'check_for_update_generate' ) );


	}

	public function create_updates_checkbox() {

		global $post;
		$checked = ( $this->is_update() ) ? '' : ' checked="checked"';
		if( isset( $post->post_type ) && $post->post_type == $this->cpt_slug ) {
			?>
			<div class="misc-pub-section">
				<label><input type="checkbox" name="dc9_generate_update"<?php echo $checked; ?> /> Vygenerovat update při uložení</label>
			</div>
		<?php

		}

	}

	public function check_for_update_generate( $post_id ) {

		$post_type = get_post_type( $post_id );

		if( $post_type !== $this->cpt_slug || wp_is_post_revision( $post_id ) ) {
			return;
		}

		if( isset( $_POST['dc9_generate_update'] ) ) {
			//we want to generate update

			$this->generate_update();
		}

	}

	private function generate_update() {

		global $sitepress;
		$languages = $sitepress->get_active_languages();

		foreach( $languages as $lang ) {
			$final_string = $this->get_update_string( $lang['code'] );

			$post = array(
				'post_content'   => $final_string,
				'post_title'     => wp_kses( $final_string, array() ),
				'post_status'    => 'publish',
				'post_type'      => 'updates'
			);

			$var_name = 'post_id_' . $lang['code'];
			$$var_name = wp_insert_post( $post );

			//generate admin notice
			$notices = get_option( 'dc9_admin_deffered_notices', array() );
			$notices[] = <<<EOT
<div class="updated">
	<p><strong>Byla vygenerována novinka v tomto znění: {$final_string}</strong></p>
</div>
EOT;
			update_option( 'dc9_admin_deffered_notices', $notices );

		}

		//WPML binding:
		//insert czech first and get trid
		/** @var int $post_id_cs */
		$sitepress->set_element_language_details( $post_id_cs , 'post_updates', false, 'cs' );

		$new_group_trid = $sitepress->get_element_trid($post_id_cs, 'post_updates');

		//connect with english
		/** @var int $post_id_en */
		$sitepress->set_element_language_details( $post_id_en , 'post_updates', $new_group_trid, 'en', 'cs' );



	}

	private function get_update_string($language)
	{
		$strings = array(
			'cs' => array(
				'video_updated' => 'Aktualizováno video %s v kategorii %s',
				'video_updated_no_cat' => 'Aktualizováno video <a href="%s">%s</a>',
				'new_video' => 'Přidáno video %s do kategorie %s',
				'new_video_no_cat' => 'Přidáno video <a href="%s">%s</a>'

			),
			'en' => array(
				'video_updated' => 'Video %s updated in category %s',
				'video_updated_no_cat' => 'Video <a href="%s">%s</a> updated',
				'new_video' => 'New video %s in category %s',
				'new_video_no_cat' => 'New video <a href="%s">%s</a>'
			)
		);

		global $post;
		/** @var array $video_category array of arrays with name and url */
		$video_category = $this->get_video_category($language);

		//Get post title from field, in nonczech lang
		$post_title = '';
		if( $language === 'cs' ) {
			$post_title = $post->post_title;
		} else {
			$post_title = get_field( 'title_' . $language, $post->ID );
		}
		if( empty( $post_title ) ) {
			$post_title = $post->post_title; //If we dont have title in field, use at least czech one
		}


		//Generate string based on used category or not
		if( $video_category == false ) {
			//no category specified
			if( $this->is_update() ) {
				//is update, not new video
				$final_string = sprintf( $strings[$language]['video_updated_no_cat'],
					get_permalink( $post->ID ), $post_title );
			} else {
				//New video
				$final_string = sprintf( $strings[$language]['new_video_no_cat'],
					get_permalink( $post->ID ), $post_title );
			}
		} else {
			//we have specified category

			//generate category tree
			$category_markup = '';
			for( $i = 0; $i < count( $video_category ); $i++ ) { //array - 0index, count - 1index
				$category_markup .= sprintf( '<a href="%s">%s</a>',
					$video_category[$i]['url'], $video_category[$i]['name'] );
				if( !( ( $i + 1 ) == count( $video_category ) ) ) {
					//if not last - append divider
					$category_markup .= '/';
				}
			}

			if( $this->is_update() ) {
				//is update, not new video
				$final_string = sprintf( $strings[$language]['video_updated'],
					$post_title, $category_markup );
			} else {
				//New video
				$final_string = sprintf( $strings[$language]['new_video'],
					$post_title, $category_markup );
			}
		}

		return $final_string;
	}

	private function is_update() {
		global $post;
		$update = ( $post->post_status == 'publish' ) ? true : false;
		return $update;
	}

	private function get_video_category($language){

		global $post;
		$terms = get_the_terms( $post->ID, $this->taxonomy_slug );
		if( empty( $terms ) ) {
			return false;
		}

		//if there is more terms selected use only one of them to generate cat tree
		$last_term = end( $terms );

		//generate category tree (parents etc.)
		$term_tree = array(
			$last_term
		);

		//set first term as first parent
		$parent = $last_term;
		while( $parent->parent != 0 ) {
			//cycle thourgh parents while there are some
			$parent = get_term_by( 'id', $parent->parent, $this->taxonomy_slug );
			array_unshift( $term_tree, $parent );
		}


		//if lang is not cs, we have to compensate for this
		if( $language !== 'cs' ){
			$final_output = array();
			foreach( $term_tree as $term ) {
				$foreign_name = get_field( 'title_' . $language, 'video-category_' . $term->term_id );
				$output['name'] = ( !empty( $foreign_name ) ) ? $foreign_name : $term->name;
				global $sitepress;
				$output['url'] = $sitepress->convert_url( get_term_link( $term ), $language );
				$final_output[] = $output;
			}

			return $final_output;
		}

		$final_output = array();
		foreach ( $term_tree as $term ) {
			$output['name'] = $term->name;
			$output['url'] = get_term_link($term);
			$final_output[] = $output;
		}

		return $final_output;

	}


}

$dc9_video_admin = new VideoAdmin();