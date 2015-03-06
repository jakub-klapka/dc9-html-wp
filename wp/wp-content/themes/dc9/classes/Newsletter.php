<?php


namespace DC9;


use DC9\Models\MCCampaign;
use DC9\Models\Subscriber;

class Newsletter {


	public function __construct() {

		add_action( 'init', array( $this, 'add_rewrite_rule' ) );

		add_filter( 'template_include', array( $this, 'display_newsletter' ) );

		add_action( 'wp_loaded', array( $this, 'process_form' ) );

		add_action( 'save_post', array( $this, 'send_newsletter' ) );

	}

	public function add_rewrite_rule() {

		add_rewrite_rule( 'newsletter/?$', 'index.php?newsletter=1', 'top' );
		add_rewrite_tag( '%newsletter%', '([^&]+)' );

	}

	public function display_newsletter( $template ) {

		if ( get_query_var( 'newsletter' ) == true ) {
			return locate_template( array( 'newsletter.php' ) );
		}

		return $template;
	}

	public function process_form() {
		if( !isset( $_POST[ 'newsletter_submit' ] ) ) return;

		if( !wp_verify_nonce( $_POST['_wpnonce'], 'newsletter' ) ) wp_die( __( 'Neoprávněný přístup', 'Theme' ) );

		global $dc9_newsletter_data;

		if( !isset( $_POST[ 'email' ] ) || !is_email( $_POST[ 'email' ] ) ) {
			$dc9_newsletter_data[ 'error' ] = __( 'Musíte zadat platný e-mail', 'Theme' );
			return;
		}

		//process mail
		include_once( 'models/Subscriber.php' );

		$subscriber = new Subscriber();
		$result = $subscriber->addNewSubscriber( sanitize_email( $_POST[ 'email' ] ) );

		if( $result === true ) {
			$dc9_newsletter_data[ 'success' ] = __( 'Váš e-mail byl zaregistrován, nyní prosím zkontrolujte svou e-mail schránku.', 'Theme' );
		} else {
			$dc9_newsletter_data[ 'error' ] = __( 'Při vaší registraci nastala tato chyba:', 'Theme' ) . ' ' . $result;
		}

	}


	public function send_newsletter( $post_id ) {
		if( wp_is_post_revision( $post_id ) || wp_is_post_autosave( $post_id ) ) return;

		if( get_post_type( $post_id ) !== 'novinky' ) return;

		$post = get_post( $post_id );

		if( $post->post_status === 'publish'
		    && get_post_meta( $post_id, 'newsletter_sent', true ) != true
			&& (int)date_format( date_create( $post->post_date ), 'U' ) > 1425587176 ){ //Only news after 5.3.2015

			//Disable any more sending on this news first
			update_post_meta( $post_id, 'newsletter_sent', true );


			//Generate excerpt
			if( has_excerpt( $post_id ) ) {
				$excerpt = $post->post_excerpt;
			} else {
				$post_content = apply_filters( 'the_content', $post->post_content );
				$has_more_tag = preg_match( '/<!--more(.*?)?-->/', $post_content, $matches );

				if ( $has_more_tag ) {
					$parts = explode( $matches[ 0 ], $post_content );
					$excerpt = $parts[ 0 ];
				} else {
					$excerpt = $post_content;
				}
			}


			//Generate and send campaign
			require_once( 'models/MCCampaign.php' );
			$subject = ( icl_get_current_language() === 'en' ) ? '[DC-9.eu] New post added: ' : '[DC-9.eu] Přidána novinka: ';
			$campaign = new MCCampaign( array(
				'mail_subject' => $subject . get_the_title( $post ),
				'title' => get_the_title( $post ),
				'url' => get_permalink( $post ),
				'content' => $excerpt
			) );

			$campaign->send();


		}


	}


}

$dc9_newsletter = new Newsletter();