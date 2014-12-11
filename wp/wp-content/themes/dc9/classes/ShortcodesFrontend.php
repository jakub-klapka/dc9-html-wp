<?php

namespace DC9;


class ShortcodesFrontend {

	public function __construct()
	{

		/**
		 * Fix shortcodes
		 */
		add_action( 'the_content', array( $this, 'cleanup_shortcode_fix' ) );
		
		/**
		 * shortcodes
		 */
		add_shortcode( 'code', array( $this, 'code_shortcode' ) );
		add_shortcode( 'youtube', array( $this, 'youtube_shortcode' ) );

	}

	public function cleanup_shortcode_fix($content){
		$array = array('<p>['=>'[',']</p>'=>']',']<br />'=>']',']<br>'=>']');
		$content = strtr($content, $array);return $content;
	}
	

	public function code_shortcode($atts, $content = null)
	{
		$atts = shortcode_atts( array(
			'title' => ''
		), $atts );

		$output = '';
		if( !empty( $atts['title'] ) ){
			$output .= sprintf( '<div class="code_heading">%s</div>', wp_kses($atts['title'], array('b', 'i', 'strong', 'em')) );
		}

		$output .= sprintf( '<pre><code>%s</code></pre>', $content );

		return $output;
	}

	public function youtube_shortcode($atts, $content = null)
	{
		$atts = shortcode_atts( array( 'url' => '' ), $atts );
		$video_id = preg_match( "/^(?:http(?:s)?:\/\/)?(?:www\.)?(?:youtu\.be\/|youtube\.com\/(?:(?:watch)?\?(?:.*&)?v(?:i)?=|(?:embed|v|vi|user)\/))([^\?&\"'>]+)/", $atts['url'], $matches );

		$output = '';
		if( isset( $matches[1] ) ) {
			$output .= sprintf( '<iframe width="670" height="377" src="//www.youtube.com/embed/%s" frameborder="0" allowfullscreen></iframe>',
				$matches[1] );
		}
		return $output;
	}

}

$dc9_shortcodes_frontend = new ShortcodesFrontend();