<?php
/**
 * WooCommerce Predictive Search Hook Filter
 *
 * Hook anf Filter into woocommerce plugin
 *
 * Table Of Contents
 *
 * plugins_loaded()
 * a3_wp_admin()
 * yellow_message_dontshow()
 * yellow_message_dismiss()
 * plugin_extra_links()
 */

namespace A3Rev\WCPredictiveSearch;

class Hook_Filter
{

	public static function plugins_loaded() {
		global $woocommerce_search_page_id;
		global $predictive_search_mode;

		$woocommerce_search_page_id = Functions::get_page_id_from_shortcode( 'woocommerce_search', 'woocommerce_search_page_id');

		$predictive_search_mode = get_option( 'predictive_search_mode', 'strict' );
	}

	public static function a3_wp_admin() {
		wp_enqueue_style( 'a3rev-wp-admin-style', WOOPS_CSS_URL . '/a3_wp_admin.css' );
	}

	public static function yellow_message_dontshow() {
		check_ajax_referer( 'wc_ps_yellow_message_dontshow', 'security' );
		$option_name   = sanitize_text_field( $_REQUEST['option_name'] );
		update_option( $option_name, 1 );
		die();
	}

	public static function yellow_message_dismiss() {
		check_ajax_referer( 'wc_ps_yellow_message_dismiss', 'security' );
		$session_name   = $_REQUEST['session_name'];
		if ( !isset($_SESSION) ) { @session_start(); }
		$_SESSION[$session_name] = 1 ;
		die();
	}

	public static function plugin_extra_links($links, $plugin_name) {
		global ${WOOPS_PREFIX.'admin_init'};

		if ( $plugin_name != WOOPS_NAME) {
			return $links;
		}
		$links[] = '<a href="'.WOO_PREDICTIVE_SEARCH_DOCS_URI.'" target="_blank">'.__('Documentation', 'woocommerce-predictive-search' ).'</a>';
		$links[] = '<a href="'.${WOOPS_PREFIX.'admin_init'}->support_url.'" target="_blank">'.__('Support', 'woocommerce-predictive-search' ).'</a>';
		return $links;
	}

	public static function settings_plugin_links($actions) {
		$actions = array_merge( array( 'settings' => '<a href="admin.php?page=woo-predictive-search">' . __( 'Settings', 'woocommerce-predictive-search' ) . '</a>' ), $actions );

		return $actions;
	}

	public static function plugin_extension_box( $boxes = array() ) {
		global ${WOOPS_PREFIX.'admin_init'};

		$support_box = '<a href="'.${WOOPS_PREFIX.'admin_init'}->support_url.'" target="_blank" alt="'.__('Go to Support Forum', 'woocommerce-predictive-search' ).'"><img src="'.WOOPS_IMAGES_URL.'/go-to-support-forum.png" /></a>';

		$boxes[] = array(
			'content' => $support_box,
			'css' => 'border: none; padding: 0; background: none;'
		);

		$review_box = '<div style="margin-bottom: 5px; font-size: 12px;"><strong>' . __('Is this plugin is just what you needed? If so', 'woocommerce-predictive-search' ) . '</strong></div>';
        $review_box .= '<a href="https://wordpress.org/support/view/plugin-reviews/woocommerce-predictive-search#postform" target="_blank" alt="'.__('Submit Review for Plugin on WordPress', 'woocommerce-predictive-search' ).'"><img src="'.WOOPS_IMAGES_URL.'/a-5-star-rating-would-be-appreciated.png" /></a>';

        $boxes[] = array(
            'content' => $review_box,
            'css' => 'border: none; padding: 0; background: none;'
        );

		$pro_box = '<a href="'.${WOOPS_PREFIX.'admin_init'}->pro_plugin_page_url.'" target="_blank" alt="'.__('WooCommerce Predictive Search Pro', 'woocommerce-predictive-search' ).'"><img src="'.WOOPS_IMAGES_URL.'/pro-version.jpg" /></a>';

		$boxes[] = array(
			'content' => $pro_box,
			'css' => 'border: none; padding: 0; background: none;'
		);

		$free_woocommerce_box = '<a href="https://profiles.wordpress.org/a3rev/#content-plugins" target="_blank" alt="'.__('Free WooCommerce Plugins', 'woocommerce-predictive-search' ).'"><img src="'.WOOPS_IMAGES_URL.'/free-woocommerce-plugins.jpg" /></a>';

		$boxes[] = array(
			'content' => $free_woocommerce_box,
			'css' => 'border: none; padding: 0; background: none;'
		);

		$free_wordpress_box = '<a href="https://profiles.wordpress.org/a3rev/#content-plugins" target="_blank" alt="'.__('Free WordPress Plugins', 'woocommerce-predictive-search' ).'"><img src="'.WOOPS_IMAGES_URL.'/free-wordpress-plugins.png" /></a>';

		$boxes[] = array(
			'content' => $free_wordpress_box,
			'css' => 'border: none; padding: 0; background: none;'
		);

		$connect_box = '<div style="margin-bottom: 5px;">' . __('Connect with us via','woocommerce-predictive-search' ) . '</div>';
		$connect_box .= '<a href="https://www.facebook.com/a3rev" target="_blank" alt="'.__('a3rev Facebook', 'woocommerce-predictive-search' ).'" style="margin-right: 5px;"><img src="'.WOOPS_IMAGES_URL.'/follow-facebook.png" /></a> ';
		$connect_box .= '<a href="https://twitter.com/a3rev" target="_blank" alt="'.__('a3rev Twitter', 'woocommerce-predictive-search' ).'"><img src="'.WOOPS_IMAGES_URL.'/follow-twitter.png" /></a>';

		$boxes[] = array(
			'content' => $connect_box,
			'css' => 'border-color: #3a5795;'
		);

		return $boxes;
	}
}
