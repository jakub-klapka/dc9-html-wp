<?php


namespace DC9;


class KontaktFrontend {

	private $form_markup;

	public function __construct()
	{
		$this->modify_ajax_loader_url();

		add_action( 'wpcf7_form_elements', array( $this, 'add_placeholder_to_quiz' ) );

		$this->form_markup = get_field( 'which_form' );
	}

	public function has_form()
	{
		return ( empty( $this->form_markup ) ) ? false : true;
	}

	public function enqueue_scripts()
	{
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts_callback' ) );
		if ( function_exists( 'wpcf7_enqueue_scripts' ) ) {
			wpcf7_enqueue_scripts();
		}
	}

	public function enqueue_scripts_callback()
	{
		wp_enqueue_style( 'contact', get_template_directory_uri() . '/css/contact.css', array( 'layout' ), DC9_CSS_VERSION );
		wp_enqueue_script( 'contact', get_template_directory_uri() . '/js/contact.min.js', array( 'jquery', 'layout' ), DC9_CSS_VERSION, true );

	}

	public function return_form_markup()
	{
		return $this->form_markup;
	}

	private function modify_ajax_loader_url()
	{
		add_filter( 'wpcf7_ajax_loader', array( $this, 'modify_ajax_loader_url_callback' ) );
	}

	public function modify_ajax_loader_url_callback($url)
	{
		return get_template_directory_uri() . '/images/ajax-preloader.gif';
	}

	public function add_placeholder_to_quiz($form_markup)
	{
		$placeholder = __( '(vložte správné číslo)', 'Theme' );
		$form_markup = str_replace( 'id="quiz_add_placeholder"', 'id="quiz_add_placeholder" placeholder="' . $placeholder . '"', $form_markup );
		return $form_markup;
	}

}


$dc9_kontakt_frontend = new KontaktFrontend();
$template_loader_instance_name = 'dc9_kontakt_frontend';