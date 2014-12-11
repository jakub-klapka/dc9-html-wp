<?php

namespace DC9;


class GalerieFrontend {

	//OPTIONS:
	private $phototile_width = '660px';

	private $gallery_ids = array();

	private $current_gallery_number = 1;

	public function __construct() {

		/*
		 * populate gallery ids
		 */
		$this->populate_gallery_ids();

		/*
		 * Remove default PE2 styles and enqueue ours
		 */
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_styles' ) );				add_filter( 'wpseo_canonical', array( $this, 'check_for_gallery_canonical_url' ) );
	}

	public function enqueue_styles() {
		wp_enqueue_style( 'pe2-custom' );
	}


	private function populate_gallery_ids() {

		$link = get_field( 'gallery_link' );

		global $dc9_galerie;
		$ids = $dc9_galerie->extract_gallery_ids( $link );

		if( $ids !== false ) {
			$this->gallery_ids['user'] = $ids['user'];
			$this->gallery_ids['album'] = $ids['album'];
		}

	}

	public function generate_gallery() {
		$shortcode = sprintf('[pe2-gallery album="http://picasaweb.google.com/data/feed/base/user/%s/albumid/%s?alt=rss&hl=en_US&kind=photo" pe2_gal_format="phototile" pe2_phototile="%s" ]',
			$this->gallery_ids['user'], $this->gallery_ids['album'], $this->phototile_width );
		echo do_shortcode( $shortcode );
	}

	public function enqueue_archive_style() {
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_archive_style_callback' ) );
	}

	public function enqueue_archive_style_callback() {
		wp_enqueue_style( 'gallery-archive' );
	}

	public function the_featured_image() {
		echo $this->get_featured_image();
	}

	private function get_featured_image() {
		$img = get_field( 'gallery_featured_image' );
		return $img['sizes']['gallery_featured'];
	}

	public function the_gallery_number() {
		echo $this->current_gallery_number;
		$this->current_gallery_number++;
	}

	public function the_modified_date() {
		$date = date_create( get_field( 'gallery_last_modified' ) );
		echo date_format( $date, get_option('date_format') );
	}		public function check_for_gallery_canonical_url( $url ) {				if( strpos( $url, 'en/galerie/' ) !== false ) {			return str_replace( 'en/galerie/', 'en/gallery/', $url );		}				return $url;			}

}

$dc9_galerie_frontend = new GalerieFrontend();
$template_loader_instance_name = 'dc9_galerie_frontend';