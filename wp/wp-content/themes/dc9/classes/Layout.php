<?php


namespace DC9;


/**
 * Class for functions loaded on every pageload
 *
 * @package DC9
 */
class Layout {



	/**
	 * Fire off all functions loaded on every pageload
	 */
	public function __construct() {

		/**
		 * CSS, JS cachebusting
		 */
		define( 'DC9_CSS_VERSION', '8' );

		/**
		 * Load scripts and styles
		 */
		add_action( 'wp_enqueue_scripts', array( $this, 'registerStylesScripts' ) );

		/**
		 * Load Theme textdomain
		 */
		add_action( 'after_setup_theme', array( $this, 'loadThemeTextdomain' ) );

		/**
		 * Remove unnesecary page supports
		 */
		add_action( 'init', array( $this, 'removePageSupport' ) );

		/**
		 * Register main nav menu
		 */
		register_nav_menu( 'main_menu', 'Hlavní menu' );


		/*
		 * Nav menu transient
		 */
		//check on every save post
		add_action( 'save_post', array( $this, 'checkMainMenuTransient' ) );

		/**
		 * New user settings
		 */
		add_action('user_register', array( $this, 'set_user_metaboxes' ) );

	}



	/**
	 * Register all scripts and styles on page, to be used on specific pages
	 * @return void
	 */
	public function registerStylesScripts()
	{
		/*
		 * Global styles and scripts
		 */
		wp_register_style( 'fancybox', get_template_directory_uri() . '/js/fancybox/jquery.fancybox.css', false, DC9_CSS_VERSION );
		wp_register_style( 'layout', get_template_directory_uri() . '/css/layout.css', array( 'fancybox' ), DC9_CSS_VERSION );

		wp_deregister_script( 'jquery' );
		wp_register_script( 'modernizr', get_template_directory_uri() . '/js/modernizr.js', false, DC9_CSS_VERSION );
		wp_register_script( 'jquery', get_template_directory_uri() . '/js/jquery-1.10.2.min.js', false, DC9_CSS_VERSION );
		wp_register_script( 'fancybox', get_template_directory_uri() . '/js/fancybox/jquery.fancybox.pack.js', array( 'jquery' ), DC9_CSS_VERSION );
		wp_register_script( 'layout', get_template_directory_uri() . '/js/layout.min.js', array( 'modernizr', 'jquery', 'fancybox' ), DC9_CSS_VERSION );

		/*
		 * Enqueue global styles
		 */
		wp_enqueue_style( 'layout' );
		wp_enqueue_script( 'layout' );

		/*
		 * Home + news archive
		 */
		wp_register_style( 'home', get_template_directory_uri() . '/css/home.css', array( 'layout' ), DC9_CSS_VERSION );

		/*
		 * Novinky - single
		 */
		wp_register_style( 'single-novinky', get_template_directory_uri() . '/css/single-novinky.css', array( 'layout' ), DC9_CSS_VERSION );

		/*
		 * Video gallery
		 */
		wp_register_style( 'video-gallery', get_template_directory_uri() . '/css/video_gallery.css', array( 'layout' ), DC9_CSS_VERSION );
		wp_register_script( 'video-gallery', get_template_directory_uri() . '/js/video_gallery.min.js', array( 'layout'), DC9_CSS_VERSION );

		/*
		 * PE2 for gallery
		 */
		wp_register_style( 'pe2-custom', get_template_directory_uri() . '/css/pe2-display.css', array( 'layout' ), DC9_CSS_VERSION );

		/*
		 * Gallery archive
		 */
		wp_register_style( 'gallery-archive', get_template_directory_uri() . '/css/gallery-archive.css', array( 'layout' ), DC9_CSS_VERSION );

	}


	/**
	 * Load l10n textdomain for Theme
	 * @return void
	 */
	public function loadThemeTextdomain() {
		load_theme_textdomain( 'Theme' );
	}

	/**
	 * Generate and output language switcher in head section
	 * @return void
	 */
	public function generateLangSwitcher() {

		$languages = icl_get_languages( 'skip_missing=0&orderby=custom' );

		if( get_post_type() === 'video' && is_archive() !== true ) {
			//Video CPT is not multilangual - wpml is not able to generate wn url from ID
			$cs_link = get_permalink();

			global $sitepress;
			//bind en link to languages from wpml
			$languages['en']['url'] = $sitepress->convert_url( $cs_link, 'en' );
		}

		if( is_tax( 'video-category' ) ) {
			$cs_link = get_term_link( get_queried_object() );

			global $sitepress;
			$languages['en']['url'] = $sitepress->convert_url( $cs_link, 'en' );
			$languages['cs']['url'] = $cs_link;
		}

		foreach( $languages as $language ):
			$active = ( $language['language_code'] == ICL_LANGUAGE_CODE ) ? true : false;
			$lang_separator = ( $language != end( $languages ) ) ? '&nbsp;/&nbsp;' : '';

			if( $active ) {
				echo sprintf( '<span class="active" itemprop="name">%s</span>%s',
					$language['native_name'], $lang_separator
				);
			} else {
				echo sprintf( '<a href="%s" itemprop="url name">%s</a>%s',
					$language['url'], $language['native_name'], $lang_separator
				);
			}
		endforeach;

	}

	/**
	 * Removes author and custom fields support from Page post type
	 */
	public function removePageSupport(){
		remove_post_type_support( 'page', 'author' );
		remove_post_type_support( 'page', 'custom-fields' );
	}

	/**
	 * Echo out nav menu with custom walker
	 */
	public function generateMainMenu() {
		global $post;
		if( empty( $post ) ) {
			//we are on home or something like that
			$id = 0;
		} else {
			$id = get_the_ID();
		}
		$transient_name = 'nav_menu_markup_' . $id . '_' . ICL_LANGUAGE_CODE;
		//$menu_markup = get_transient( $transient_name );
		$menu_markup = false;
		if( $menu_markup === false ) {
			//not in cache
			$args = array(
				'theme_location'  => 'main_menu',
				'menu'            => 'main_menu',
				'container'       => false,
				'menu_class'      => false,
				'menu_id'         => false,
				'echo'            => false,
				'fallback_cb'     => 'wp_page_menu',
				'items_wrap'      => '<ul>%3$s</ul>',
				'depth'           => 0,
				'walker'          => new CustomMenuWalker
			);
			$menu_markup = wp_nav_menu( $args );
			//set_transient( $transient_name, $menu_markup, YEAR_IN_SECONDS );
		}
		echo $menu_markup;

		//TODO: transients with proper id assigning for video terms
	}

	/**
	 * Check if saved post is main menu -> delete transient
	 *
	 * Called on save post action
	 *
	 * @param $post_id
	 */
	public function checkMainMenuTransient( $post_id ) {
		$post_type = get_post_type( $post_id );
		if( $post_type == 'nav_menu_item' ){
			//delete transient for all ids

			global $wpdb;
			$query = $wpdb->prepare("
				DELETE FROM $wpdb->options
				WHERE ( option_name LIKE %s OR option_name LIKE %s)",
				'%_transient_timeout_nav_menu_markup_%', '%_transient_nav_menu_markup_%');
			$wpdb->query( $query );

		}
	}


	public function enqueue_home_styles() {
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_home_styles_callback' ) );
	}

	public function enqueue_home_styles_callback() {
		wp_enqueue_style( 'home' );
	}

	public function get_url_from_anchor( $markup ) {
		if( empty( $markup ) ) {
			return false;
		}
		$output = array();
		//URL matching
		preg_match( '/href="(.+?)"/', $markup, $matches );
		if( isset( $matches[1] ) ) {
			$output['url'] = $matches[1];
		} else {
			return false;
		}

		//Name matching
		preg_match( '/<a.+?>(.+)<\/a/', $markup, $matches );
		if( isset( $matches[1] ) ) {
			$output['name'] = $matches[1];
		} else {
			return false;
		}

		return $output;
	}

	public function enqueue_novinky_single_style() {
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_novinky_single_style_callback' ) );
	}

	public function enqueue_novinky_single_style_callback() {
		wp_enqueue_style( 'single-novinky' );
	}

	function set_user_metaboxes($user_id=NULL) {

		$defaults = array(
			'closedpostboxes_page' => 'a:0:{}',
			'metaboxhidden_page' => 'a:5:{i:0;s:6:"acf_84";i:1;s:7:"acf_118";i:2;s:6:"acf_16";i:3;s:7:"acf_201";i:4;s:7:"slugdiv";}',
			'manageuploadcolumnshidden' => 'a:4:{i:0;s:6:"author";i:1;s:8:"comments";i:2;s:0:"";i:3;s:0:"";}',
			'closedpostboxes_theme_options' => 'a:0:{}',
			'metaboxhidden_theme_options' => 'a:5:{i:0;s:6:"acf_84";i:1;s:7:"acf_118";i:2;s:7:"acf_201";i:3;s:12:"revisionsdiv";i:4;s:7:"slugdiv";}',
			'managenav-menuscolumnshidden' => '"a:4:{i:0;s:11:"link-target";i:1;s:11:"css-classes";i:2;s:3:"xfn";i:3;s:11:"description";}',
			'metaboxhidden_nav-menus' => 'a:6:{i:0;s:8:"add-post";i:1;s:11:"add-novinky";i:2;s:9:"add-video";i:3;s:11:"add-updates";i:4;s:12:"add-category";i:5;s:12:"add-post_tag";}',
			'closedpostboxes_nav-menus' => 'a:0:{}',
			'meta-box-order_dashboard' => 'a:4:{s:6:"normal";s:57:"dashboard_right_now,ZigDashNote,custom_dashboard_activity";s:4:"side";s:24:"google-analytics-summary";s:7:"column3";s:0:"";s:7:"column4";s:0:"";}'
		);


		// So this can be used without hooking into user_register
		if ( ! $user_id)
			$user_id = get_current_user_id();

		foreach( $defaults as $key => $value ){

			// Set the default order if it has not been set yet
			if ( ! get_user_meta( $user_id, $key, true) ) {
				$meta_value = unserialize( $value );
				update_user_meta( $user_id, $key, $meta_value );
			}
		}

	}

}

class CustomMenuWalker extends \Walker_Nav_Menu {
	/**
	 * Starts the list before the elements are added.
	 *
	 * @see Walker::start_lvl()
	 *
	 * @since 3.0.0
	 *
	 * @param string $output Passed by reference. Used to append additional content.
	 * @param int    $depth  Depth of menu item. Used for padding.
	 * @param array  $args   An array of arguments. @see wp_nav_menu()
	 */
	function start_lvl( &$output, $depth = 0, $args = array() ) {
		$indent = str_repeat("\t", $depth);
		$output .= "\n$indent<ul itemscope itemtype=\"http://schema.org/SiteNavigationElement\">\n";
	}

	/**
	 * Start the element output.
	 *
	 * @see Walker::start_el()
	 *
	 * @since 3.0.0
	 *
	 * @param string $output Passed by reference. Used to append additional content.
	 * @param object $item   Menu item data object.
	 * @param int    $depth  Depth of menu item. Used for padding.
	 * @param array  $args   An array of arguments. @see wp_nav_menu()
	 * @param int    $id     Current item ID.
	 */
	function start_el( &$output, $item, $depth = 0, $args = array(), $id = 0 ) {
		$indent = ( $depth ) ? str_repeat( "\t", $depth ) : '';

		$classes = empty( $item->classes ) ? array() : (array) $item->classes;

		$classes_for_li = array();

		$has_submenu = ( in_array( 'menu-item-has-children', $classes ) ) ? true : false;
		$is_video_parent = ( in_array( 'video_archive', $classes ) ) ? true : false;
		$is_current_page_video_term = is_tax( 'video-category' );
		$should_stay_active = ( $is_video_parent === true && $is_current_page_video_term === true ) ? true : false; //If any video term is displayed, the video archive link in menu shloud be active

		/**
		 * Active when top level item is active, or some of children, but not in second level
		 */
		if( ( $item->current || $item->current_item_parent || $should_stay_active ) && $depth == 0 ) {
			$classes_for_li[] = 'active';
		}
		if( $has_submenu ) {
			$classes_for_li[] = 'has_submenu';
		}

		if( !empty( $classes_for_li ) ){
			$class_names = ' class="' . join( ' ', $classes_for_li ) . '"';
		} else {
			$class_names = '';
		}

		$output .= $indent . '<li' . $class_names .'>';

		$atts = array();
		$atts['title']  = ! empty( $item->attr_title ) ? $item->attr_title : '';
		$atts['target'] = ! empty( $item->target )     ? $item->target     : '';
		$atts['rel']    = ! empty( $item->xfn )        ? $item->xfn        : '';
		$atts['href']   = ! empty( $item->url )        ? $item->url        : '';

		/**
		 * Filter the HTML attributes applied to a menu item's <a>.
		 *
		 * @since 3.6.0
		 *
		 * @param array $atts {
		 *     The HTML attributes applied to the menu item's <a>, empty strings are ignored.
		 *
		 *     @type string $title  The title attribute.
		 *     @type string $target The target attribute.
		 *     @type string $rel    The rel attribute.
		 *     @type string $href   The href attribute.
		 * }
		 * @param object $item The current menu item.
		 * @param array  $args An array of arguments. @see wp_nav_menu()
		 */
		$atts = apply_filters( 'nav_menu_link_attributes', $atts, $item, $args );

		$attributes = '';
		foreach ( $atts as $attr => $value ) {
			if ( ! empty( $value ) ) {
				$value = ( 'href' === $attr ) ? esc_url( $value ) : esc_attr( $value );
				$attributes .= ' ' . $attr . '="' . $value . '"';
			}
		}

		$item_output = $args->before;
		if( $has_submenu ) {
			$item_output .= '<span'. $attributes .'>';
		} else {
			$item_output .= '<a itemprop="name url"'. $attributes .'>';
		}
		/** This filter is documented in wp-includes/post-template.php */
		$item_output .= $args->link_before . apply_filters( 'the_title', $item->title, $item->ID ) . $args->link_after;
		if( $has_submenu ) {
			$item_output .= '</span>';
		} else {
			$item_output .= '</a>';
		}
		$item_output .= $args->after;

		/**
		 * Filter a menu item's starting output.
		 *
		 * The menu item's starting output only includes $args->before, the opening <a>,
		 * the menu item's title, the closing </a>, and $args->after. Currently, there is
		 * no filter for modifying the opening and closing <li> for a menu item.
		 *
		 * @since 3.0.0
		 *
		 * @param string $item_output The menu item's starting HTML output.
		 * @param object $item        Menu item data object.
		 * @param int    $depth       Depth of menu item. Used for padding.
		 * @param array  $args        An array of arguments. @see wp_nav_menu()
		 */
		$output .= apply_filters( 'walker_nav_menu_start_el', $item_output, $item, $depth, $args );
	}

}

?>
