<?php

namespace DC9;


class Admin {

	public function __construct()
	{
		
		/**
		 * Add tinymce css
		 */
		add_action( 'init', array( $this, 'add_tinymce_style' ) );

		/**
		 * Tinymce buttons
		 */
		if ( get_user_option('rich_editing') == 'true' ) {
			add_filter( 'mce_external_plugins', array( $this, 'add_tinymce_plugin' ) );
			add_filter( 'mce_buttons', array( $this, 'register_tinymce_buttons' ) );
		}

		/*
		 * Add submenu placeholder meta box do menu editor
		 */
		add_action( 'admin_init', array( $this, 'addMenuPlaceholderMetaBox' ) );
		add_action( 'admin_init', array( $this, 'add_gallery_archive_metabox_to_navmenu' ) );

		/**
		 * Dashboard modifs
		 */
		add_action('dashboard_glance_items', array( $this, 'add_custom_post_counts' ) );
		add_action('admin_head', array( $this, 'dashboard_my_custom_fonts' ) );

		/**
		 * IWP dash
		 */
		global $iwp_mmb_core;
		remove_action( 'rightnow_end', array( $iwp_mmb_core, 'add_right_now_info' ) );

		/**
		 * Delete cache on save term
		 */
		add_action( 'edit_term', array( $this, 'delete_cache_on_save_post' ) );
		add_action( 'create_term', array( $this, 'delete_cache_on_save_post' ) );
		add_action( 'delete_term', array( $this, 'delete_cache_on_save_post' ) );

		/**
		 * Check for admin notices for new updates
		 */
		add_action( 'admin_notices', array( $this, 'check_for_admin_notices' ) );

	}

	public function add_tinymce_style()
	{
		add_editor_style( 'css/editor-style.css' );
	}

	public function register_tinymce_buttons( $buttons ) {
		foreach( $buttons as $key => $button ) {
			if( $button === 'wp_adv' ) {
				unset( $buttons[$key] );
			}
		}
		array_push( $buttons, 'dc9_code', 'dc9_youtube', 'wp_adv' );
		return $buttons;

	}

	public function add_tinymce_plugin( $plugin_array ) {
		$plugin_array['dc9_shortcodes'] = get_template_directory_uri() . '/tinymce_plugins/editor-buttons.js';
		return $plugin_array;
	}

	/**
	 * Add meta box to nav menus editor
	 *
	 * called by admin_init action
	 */
	public function addMenuPlaceholderMetaBox() {
		add_meta_box(
			'nav_menu_submenu_placeholder',
			__('Zástupce pro podmenu'),
			array( $this, 'menuPlaceholderMetaBoxCallback'),
			'nav-menus',
			'side',
			'low'
		);
	}

	/**
	 * Callback for nav menu placeholder metabox
	 */
	public function menuPlaceholderMetaBoxCallback() {
		?>
		<div id="placeholder" class="posttypediv">
			<div id="tabs-panel-wishlist-login" class="tabs-panel tabs-panel-active">
				<ul id ="wishlist-login-checklist" class="categorychecklist form-no-clear">
					<li>
						<label class="menu-item-title">
							<input type="checkbox" class="menu-item-checkbox" checked="checked" name="menu-item[-1][menu-item-object-id]" value="-1">Vložit zástupce
						</label>
						<input type="hidden" class="menu-item-type" name="menu-item[-1][menu-item-type]" value="custom">
						<input type="hidden" class="menu-item-title" name="menu-item[-1][menu-item-title]" value="Zástupce pro submenu">
						<input type="hidden" class="menu-item-url" name="menu-item[-1][menu-item-url]" value="">
						<input type="hidden" class="menu-item-classes" name="menu-item[-1][menu-item-classes]" value="submenu_placeholder">
					</li>
				</ul>
			</div>
			<p class="button-controls">
                <span class="add-to-menu">
                    <input type="submit" class="button-secondary submit-add-to-menu right" value="Přidat do menu" name="add-post-type-menu-item" id="submit-placeholder">
                    <span class="spinner"></span>
                </span>
			</p>
		</div>
	<?php
	}

	/**
	 * Add meta box for gallery archive
	 *
	 * called by admin_init action
	 */
	public function add_gallery_archive_metabox_to_navmenu() {
		add_meta_box(
			'nav_menu_gallery_archive',
			__('Odkaz na výpis galerií'),
			array( $this, 'add_gallery_archive_metabox_to_navmenu_callback'),
			'nav-menus',
			'side',
			'low'
		);
	}

	/**
	 * Callback for nav menu placeholder metabox
	 */
	public function add_gallery_archive_metabox_to_navmenu_callback() {
		$link = ( ICL_LANGUAGE_CODE === 'en' ) ? get_bloginfo('url') . '/en/gallery/' : get_post_type_archive_link( 'galerie' );
		?>
		<div id="gallery_archive" class="posttypediv">
			<div id="tabs-panel-wishlist-login" class="tabs-panel tabs-panel-active">
				<ul id ="wishlist-login-checklist" class="categorychecklist form-no-clear">
					<li>
						<label class="menu-item-title">
							<input type="checkbox" class="menu-item-checkbox" checked="checked" name="menu-item[-1][menu-item-object-id]" value="-1">Vložit odkaz na výpis galerií
						</label>
						<input type="hidden" class="menu-item-type" name="menu-item[-1][menu-item-type]" value="custom">
						<input type="hidden" class="menu-item-title" name="menu-item[-1][menu-item-title]" value="Galerie">
						<input type="hidden" class="menu-item-url" name="menu-item[-1][menu-item-url]" value="<?php echo $link; ?>">
					</li>
				</ul>
			</div>
			<p class="button-controls">
                <span class="add-to-menu">
                    <input type="submit" class="button-secondary submit-add-to-menu right" value="Přidat do menu" name="add-post-type-menu-item" id="submit-gallery_archive">
                    <span class="spinner"></span>
                </span>
			</p>
		</div>
	<?php
	}

	public function add_custom_post_counts() {
		$post_types = array('novinky', 'video', 'galerie', 'updates'); // array of custom post types to add to 'At A Glance' widget
		foreach ($post_types as $pt) :
			$pt_info = get_post_type_object($pt); // get a specific CPT's details
			$num_posts = wp_count_posts($pt); // retrieve number of posts associated with this CPT
			$num = number_format_i18n($num_posts->publish); // number of published posts for this CPT
			$text = _n( $pt_info->labels->singular_name, $pt_info->labels->name, intval($num_posts->publish) ); // singular/plural text label for CPT
			echo '<li class="page-count '.$pt_info->name.'-count"><a href="edit.php?post_type='.$pt.'">'.$num.' '.$text.'</a></li>';
		endforeach;

	}

	function dashboard_my_custom_fonts() {
		global $current_screen;
		if( $current_screen->id !== 'dashboard' ){
			return;
		}

		echo '<style>
			.page-count.novinky-count a:before {content:\'\f103\' !important;}
			.page-count.video-count a:before {content:\'\f236\' !important;}
			.page-count.galerie-count a:before {content:\'\f233\' !important;}
			.page-count.updates-count a:before {content:\'\f109\' !important;}
		  </style>';
	}

	public function delete_cache_on_save_post( $post_id = null )
	{
		if( function_exists('wp_cache_clear_cache') ){
			wp_cache_clear_cache();
		}
	}

	public function check_for_admin_notices()
	{
		$notices = get_option( 'dc9_admin_deffered_notices' );
		if( !empty( $notices ) ) {
			foreach( $notices as $notice ) {
				echo $notice;
			}
			delete_option( 'dc9_admin_deffered_notices' );
		}
	}

}

$dc9_admin = new Admin();
