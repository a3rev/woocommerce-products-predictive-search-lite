<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Get templates passing attributes and including the file.
 *
 * @access public
 * @param string $template_name
 * @param array $args (default: array())
 * @return void
 */
function wc_ps_get_template( $template_name, $args = array() ) {
	if ( $args && is_array( $args ) ) {
		extract( $args );
	}

	$template_file_path = wc_ps_get_template_file_path( $template_name );

	if ( ! file_exists( $template_file_path ) ) {
		_doing_it_wrong( __FUNCTION__, sprintf( '<code>%s</code> does not exist.', $template_file_path ), '1.0.0' );
		return;
	}

	// Allow 3rd party plugin filter template file from their plugin
	$template_file_path = apply_filters( 'wc_ps_get_template', $template_file_path, $template_name, $args );

	do_action( 'wc_ps_before_template_part', $template_name, $template_file_path, $args );

	include( $template_file_path );

	do_action( 'wc_ps_after_template_part', $template_name, $template_file_path, $args );
}

/**
 * wc_ps_get_template_file_path( $file )
 *
 * This is the load order:
 *
 *		yourtheme					/	woocommerce	/	$file
 *		yourtheme					/	$file
 *		WOOPS_TEMPLATE_PATH			/	$file
 *
 * @access public
 * @param $file string filename
 * @return PATH to the file
 */
function wc_ps_get_template_file_path( $file = '' ) {
	// If we're not looking for a file, do not proceed
	if ( empty( $file ) )
		return;

	// Look for file in stylesheet
	if ( file_exists( get_stylesheet_directory() . '/woocommerce/' . $file ) ) {
		$file_path = get_stylesheet_directory() . '/woocommerce/' . $file;

	// Look for file in stylesheet
	} elseif ( file_exists( get_stylesheet_directory() . '/' . $file ) ) {
		$file_path = get_stylesheet_directory() . '/' . $file;

	// Look for file in template
	} elseif ( file_exists( get_template_directory() . '/woocommerce/' . $file ) ) {
		$file_path = get_template_directory() . '/woocommerce/' . $file;

	// Look for file in template
	} elseif ( file_exists( get_template_directory() . '/' . $file ) ) {
		$file_path = get_template_directory() . '/' . $file;

	// Get default template
	} else {
		$file_path = WOOPS_TEMPLATE_PATH . '/' . $file;
	}

	// Return filtered result
	return apply_filters( 'wc_ps_get_template_file_path', $file_path, $file );
}

/**
 * wc_ps_search_form()
 *
 * @return void
 */
function wc_ps_search_form( $ps_id = '', $template = 'sidebar', $args = array(), $echo = false ) {

	$ps_id = str_replace( 'products_predictive_search-', '', $ps_id );
	if ( empty( $ps_id ) ) {
		$ps_id = rand( 100, 10000 );
	}

	ob_start();

	// Custom Style for Individual Predictive Search Widget
	$custom_style = '';
	if ( isset( $args['show_image'] ) && 0 == $args['show_image'] ) {
		$custom_style .= '
.ac_results_' . $ps_id . ' .rs_avatar {
	display: none !important;
}
.predictive_results.ac_results_' . $ps_id . ' .rs_content_popup {
	width: 100% !important;
}';
	}
	if ( isset( $args['show_desc'] ) && 0 == $args['show_desc'] ) {
		$custom_style .= '
.ac_results_' . $ps_id . ' .rs_description {
	display: none !important;
}';
	}

	if ( '' != trim( $custom_style ) ) {
		echo '<style>' . $custom_style . '</style>';
	}

	if ( 'header' == $template ) {
		wc_ps_search_form_header_tpl( $ps_id, $args );
	} else {
		wc_ps_search_form_sidebar_tpl( $ps_id, $args );
	}

	$search_form = ob_get_clean();

	if ( $echo ) {
		echo $search_form;
	} else {
		return $search_form;
	}
}

/**
 * wc_ps_search_form_sidebar_tpl()
 *
 * @return void
 */
function wc_ps_search_form_sidebar_tpl( $ps_id, $args = array() ) {
	global $wc_predictive_search_sidebar_template_settings;

	if ( ! is_array( $args ) ) {
		$args = array();
	}

	$args['popup_wide'] = $wc_predictive_search_sidebar_template_settings['popup_wide'];
	$args['cat_align'] = $wc_predictive_search_sidebar_template_settings['sidebar_category_dropdown_align'];
	$args['cat_max_wide'] = $wc_predictive_search_sidebar_template_settings['sidebar_category_dropdown_max_wide'];

	wc_ps_get_template( 'search-bar/predictive-search-form-sidebar.php',
		apply_filters( 'wc_ps_search_form_sidebar_tpl_args', array(
			'ps_id'              => $ps_id,
			'ps_widget_template' => 'sidebar',
			'ps_args'            => $args
		) )
	);
}

/**
 * wc_ps_search_form_header_tpl()
 *
 * @return void
 */
function wc_ps_search_form_header_tpl( $ps_id, $args = array() ) {
	global $wc_predictive_search_header_template_settings;

	if ( ! is_array( $args ) ) {
		$args = array();
	}

	$args['popup_wide'] = $wc_predictive_search_header_template_settings['popup_wide'];
	$args['cat_align'] = $wc_predictive_search_header_template_settings['header_category_dropdown_align'];
	$args['cat_max_wide'] = $wc_predictive_search_header_template_settings['header_category_dropdown_max_wide'];

	wc_ps_get_template( 'search-bar/predictive-search-form-header.php',
		apply_filters( 'wc_ps_search_form_header_tpl_args', array(
			'ps_id'              => $ps_id,
			'ps_widget_template' => 'header',
			'ps_args'            => $args
		) )
	);
}

/**
 * wc_ps_get_popup_item_tpl()
 *
 * @return void
 */
function wc_ps_get_popup_item_tpl() {

	wc_ps_get_template( 'popup/item.php',
		apply_filters( 'wc_ps_popup_item_tpl_args', array() )
	);
}

/**
 * wc_ps_get_popup_footer_sidebar_tpl()
 *
 * @return void
 */
function wc_ps_get_popup_footer_sidebar_tpl() {
	global $wc_predictive_search_sidebar_template_settings;

	wc_ps_get_template( 'popup/footer-sidebar.php',
		apply_filters( 'wc_ps_popup_footer_sidebar_tpl_args', array(
			'popup_seemore_text' => $wc_predictive_search_sidebar_template_settings['sidebar_popup_seemore_text']
		) )
	);
}

/**
 * wc_ps_get_popup_footer_header_tpl()
 *
 * @return void
 */
function wc_ps_get_popup_footer_header_tpl() {

	wc_ps_get_template( 'popup/footer-header.php',
		apply_filters( 'wc_ps_popup_footer_header_tpl_args', array(
			'popup_seemore_text' => ''
		) )
	);
}

/**
 * wc_ps_get_results_item_tpl()
 *
 * @return void
 */
function wc_ps_get_results_item_tpl() {
	wc_ps_get_template( 'results-page/item.php',
		apply_filters( 'wc_ps_results_item_tpl_args', array() )
	);
}

/**
 * wc_ps_get_results_header_tpl()
 *
 * @return void
 */
function wc_ps_get_results_header_tpl( $args = array() ) {
	if ( ! is_array( $args ) ) {
		$args = array();
	}

	wc_ps_get_template( 'results-page/header.php',
		apply_filters( 'wc_ps_results_header_tpl_args', $args )
	);
}

/**
 * wc_ps_get_results_footer_tpl()
 *
 * @return void
 */
function wc_ps_get_results_footer_tpl() {
	wc_ps_get_template( 'results-page/footer.php',
		apply_filters( 'wc_ps_results_footer_tpl_args', array() )
	);
}

/**
 * wc_ps_error_modal_tpl()
 *
 * @return void
 */
function wc_ps_error_modal_tpl( $args = array() ) {

	wc_ps_get_template( 'admin/error-log-modal.php',
		apply_filters( 'wc_ps_error_modal_tpl_args', $args )
	);
}

function wc_ps_get_product_categories() {
	global $wc_predictive_search_cache;
	$categories_list       = false;
	$append_transient_name = '';

	if ( $wc_predictive_search_cache->enable_cat_cache() ) {
		if ( class_exists('SitePress') ) {
			$current_lang = apply_filters( 'wpml_current_language', NULL );
			$append_transient_name = $current_lang;
		}

		$categories_list = $wc_predictive_search_cache->get_product_categories_dropdown_cache( $append_transient_name );

		if ( false === $categories_list ) {
			$language = trim( $append_transient_name );
			if ( '' != $language ) {
				$language = '_' . $language;
			}
			update_option( 'predictive_search_have_cat_cache' . $language, 'no' );
		}
	}

	return $categories_list;
}
