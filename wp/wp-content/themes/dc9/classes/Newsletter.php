<?php


namespace DC9;


use DC9\Models\Subscriber;

class Newsletter {


	public function __construct() {

		add_action( 'init', array( $this, 'add_rewrite_rule' ) );

		add_filter( 'template_include', array( $this, 'display_newsletter' ) );

		add_action( 'wp_loaded', array( $this, 'process_form' ) );

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




}

$dc9_newsletter = new Newsletter();