<?php
/* "Copyright 2012 A3 Revolution Web Design" This software is distributed under the terms of GNU GENERAL PUBLIC LICENSE Version 3, 29 June 2007 */
// File Security Check
if ( ! defined( 'ABSPATH' ) ) exit;
?>
<?php
/*-----------------------------------------------------------------------------------
WC Predictive Search Global Settings

TABLE OF CONTENTS

- var parent_tab
- var subtab_data
- var option_name
- var form_key
- var position
- var form_fields
- var form_messages

- __construct()
- subtab_init()
- set_default_settings()
- get_settings()
- subtab_data()
- add_subtab()
- settings_form()
- init_form_fields()

-----------------------------------------------------------------------------------*/

class WC_Predictive_Search_Global_Settings extends WC_Predictive_Search_Admin_UI
{

	/**
	 * @var string
	 */
	private $parent_tab = 'global-settings';

	/**
	 * @var array
	 */
	private $subtab_data;

	/**
	 * @var string
	 * You must change to correct option name that you are working
	 */
	public $option_name = '';

	/**
	 * @var string
	 * You must change to correct form key that you are working
	 */
	public $form_key = 'wc_predictive_search_global_settings';

	/**
	 * @var string
	 * You can change the order show of this sub tab in list sub tabs
	 */
	private $position = 1;

	/**
	 * @var array
	 */
	public $form_fields = array();

	/**
	 * @var array
	 */
	public $form_messages = array();

	/*-----------------------------------------------------------------------------------*/
	/* __construct() */
	/* Settings Constructor */
	/*-----------------------------------------------------------------------------------*/
	public function __construct() {

		$this->init_form_fields();
		$this->subtab_init();

		$this->form_messages = array(
				'success_message'	=> __( 'Global Settings successfully saved.', 'woocommerce-predictive-search' ),
				'error_message'		=> __( 'Error: Global Settings can not save.', 'woocommerce-predictive-search' ),
				'reset_message'		=> __( 'Global Settings successfully reseted.', 'woocommerce-predictive-search' ),
			);

		add_action( $this->plugin_name . '-' . $this->form_key . '_settings_end', array( $this, 'include_script' ) );

		add_action( $this->plugin_name . '_set_default_settings' , array( $this, 'set_default_settings' ) );

		add_action( $this->plugin_name . '-' . $this->form_key . '_settings_init' , array( $this, 'after_save_settings' ) );
		//add_action( $this->plugin_name . '_get_all_settings' , array( $this, 'get_settings' ) );
	}

	/*-----------------------------------------------------------------------------------*/
	/* subtab_init() */
	/* Sub Tab Init */
	/*-----------------------------------------------------------------------------------*/
	public function subtab_init() {

		add_filter( $this->plugin_name . '-' . $this->parent_tab . '_settings_subtabs_array', array( $this, 'add_subtab' ), $this->position );

	}

	/*-----------------------------------------------------------------------------------*/
	/* set_default_settings()
	/* Set default settings with function called from Admin Interface */
	/*-----------------------------------------------------------------------------------*/
	public function set_default_settings() {
		global $wc_predictive_search_admin_interface;

		$wc_predictive_search_admin_interface->reset_settings( $this->form_fields, $this->option_name, false );
	}

	/*-----------------------------------------------------------------------------------*/
	/* after_save_settings()
	/* Process when clean on deletion option is un selected */
	/*-----------------------------------------------------------------------------------*/
	public function after_save_settings() {
		if ( ( isset( $_POST['bt_save_settings'] ) || isset( $_POST['bt_reset_settings'] ) ) && get_option( $this->plugin_name . '_clean_on_deletion' ) == 'no' )  {
			$uninstallable_plugins = (array) get_option('uninstall_plugins');
			unset($uninstallable_plugins[$this->plugin_path]);
			update_option('uninstall_plugins', $uninstallable_plugins);
		}

		if ( isset( $_POST['bt_save_settings'] ) ) {
			flush_rewrite_rules();
		} elseif ( 1 == get_option( 'wc_predictive_search_just_confirm', 0 ) ) {
			delete_option( 'wc_predictive_search_just_confirm' );
			flush_rewrite_rules();
		}

		if ( ( isset( $_POST['bt_save_settings'] ) || isset( $_POST['bt_reset_settings'] ) ) )  {
			global $wc_ps_exclude_data;
			$wc_ps_exclude_data->empty_table();

			delete_option( 'woocommerce_search_exclude_products' );
			delete_option( 'woocommerce_search_exclude_posts' );
			delete_option( 'woocommerce_search_exclude_pages' );
		}
		if ( isset( $_POST['bt_save_settings'] ) )  {
			global $wc_ps_exclude_data;
			if ( isset( $_POST['woocommerce_search_exclude_products'] ) && count( $_POST['woocommerce_search_exclude_products'] ) > 0 ) {
				foreach ( $_POST['woocommerce_search_exclude_products'] as $item_id ) {
					$wc_ps_exclude_data->insert_item( $item_id, 'product' );
				}
			}
			if ( isset( $_POST['woocommerce_search_exclude_posts'] ) && count( $_POST['woocommerce_search_exclude_posts'] ) > 0 ) {
				foreach ( $_POST['woocommerce_search_exclude_posts'] as $item_id ) {
					$wc_ps_exclude_data->insert_item( $item_id, 'post' );
				}
			}
			if ( isset( $_POST['woocommerce_search_exclude_pages'] ) && count( $_POST['woocommerce_search_exclude_pages'] ) > 0 ) {
				foreach ( $_POST['woocommerce_search_exclude_pages'] as $item_id ) {
					$wc_ps_exclude_data->insert_item( $item_id, 'page' );
				}
			}
		}
	}

	/*-----------------------------------------------------------------------------------*/
	/* get_settings()
	/* Get settings with function called from Admin Interface */
	/*-----------------------------------------------------------------------------------*/
	public function get_settings() {
		global $wc_predictive_search_admin_interface;

		$wc_predictive_search_admin_interface->get_settings( $this->form_fields, $this->option_name );
	}

	/**
	 * subtab_data()
	 * Get SubTab Data
	 * =============================================
	 * array (
	 *		'name'				=> 'my_subtab_name'				: (required) Enter your subtab name that you want to set for this subtab
	 *		'label'				=> 'My SubTab Name'				: (required) Enter the subtab label
	 * 		'callback_function'	=> 'my_callback_function'		: (required) The callback function is called to show content of this subtab
	 * )
	 *
	 */
	public function subtab_data() {

		$subtab_data = array(
			'name'				=> 'global-settings',
			'label'				=> __( 'Settings', 'woocommerce-predictive-search' ),
			'callback_function'	=> 'wc_predictive_search_global_settings_form',
		);

		if ( $this->subtab_data ) return $this->subtab_data;
		return $this->subtab_data = $subtab_data;

	}

	/*-----------------------------------------------------------------------------------*/
	/* add_subtab() */
	/* Add Subtab to Admin Init
	/*-----------------------------------------------------------------------------------*/
	public function add_subtab( $subtabs_array ) {

		if ( ! is_array( $subtabs_array ) ) $subtabs_array = array();
		$subtabs_array[] = $this->subtab_data();

		return $subtabs_array;
	}

	/*-----------------------------------------------------------------------------------*/
	/* settings_form() */
	/* Call the form from Admin Interface
	/*-----------------------------------------------------------------------------------*/
	public function settings_form() {
		global $wc_predictive_search_admin_interface;

		$output = '';
		$output .= $wc_predictive_search_admin_interface->admin_forms( $this->form_fields, $this->form_key, $this->option_name, $this->form_messages );

		return $output;
	}

	/*-----------------------------------------------------------------------------------*/
	/* init_form_fields() */
	/* Init all fields of this form */
	/*-----------------------------------------------------------------------------------*/
	public function init_form_fields() {

		global $wpdb;
		$all_products     = array();
		$all_posts        = array();
		$all_pages        = array();

		$products_excluded     = array();
		$posts_excluded        = array();
		$pages_excluded        = array();
		
		if ( is_admin() && in_array (basename($_SERVER['PHP_SELF']), array('admin.php') ) && isset( $_GET['page'] ) && $_GET['page'] == 'woo-predictive-search' && ( ! isset( $_GET['tab'] ) || $_GET['tab'] == 'global-settings' ) ) {

			if ( isset( $_POST['bt_save_settings'] ) )  {
				if ( isset( $_POST['woocommerce_search_exclude_products'] ) ) {
					$products_excluded     = $_POST['woocommerce_search_exclude_products'];
				}
				$posts_excluded = array();
				if ( isset( $_POST['woocommerce_search_exclude_posts'] ) ) {
					$posts_excluded        = $_POST['woocommerce_search_exclude_posts'];
				}
				$pages_excluded = array();
				if ( isset( $_POST['woocommerce_search_exclude_pages'] ) ) {
					$pages_excluded        = $_POST['woocommerce_search_exclude_pages'];
				}
			} else {
				$products_excluded     = $wpdb->get_col( $wpdb->prepare( "SELECT object_id FROM {$wpdb->prefix}ps_exclude WHERE object_type = %s ", 'product' ) );
				$posts_excluded        = $wpdb->get_col( $wpdb->prepare( "SELECT object_id FROM {$wpdb->prefix}ps_exclude WHERE object_type = %s ", 'post' ) );
				$pages_excluded        = $wpdb->get_col( $wpdb->prepare( "SELECT object_id FROM {$wpdb->prefix}ps_exclude WHERE object_type = %s ", 'page' ) );
			}

			if ( ! empty( $products_excluded ) ) {
				$results = $wpdb->get_results("SELECT post_id, post_title FROM ".$wpdb->prefix."ps_posts WHERE post_type='product' AND post_id IN (" . implode(',', $products_excluded ) . ") ORDER BY post_title ASC");
				if ($results) {
					foreach($results as $item_data) {
						$all_products[$item_data->post_id] = $item_data->post_title;
					}
				}
			}


			if ( ! empty( $posts_excluded ) ) {
				$results = $wpdb->get_results("SELECT post_id, post_title FROM ".$wpdb->prefix."ps_posts WHERE post_type='post' AND post_id IN (" . implode(',', $posts_excluded ) . ") ORDER BY post_title ASC");
				if ($results) {
					foreach($results as $item_data) {
						$all_posts[$item_data->post_id] = $item_data->post_title;
					}
				}
			}

			if ( ! empty( $pages_excluded ) ) {
				$results = $wpdb->get_results("SELECT post_id, post_title FROM ".$wpdb->prefix."ps_posts WHERE post_type='page' AND post_id IN (" . implode(',', $pages_excluded ) . ") ORDER BY post_title ASC");
				if ($results) {
					foreach($results as $item_data) {
						$all_pages[$item_data->post_id] = $item_data->post_title;
					}
				}
			}

		}

  		// Define settings
     	$this->form_fields = apply_filters( $this->option_name . '_settings_fields', array(

     		array(
            	'name' 		=> __( 'Plugin Framework Global Settings', 'woocommerce-predictive-search' ),
            	'id'		=> 'plugin_framework_global_box',
                'type' 		=> 'heading',
                'first_open'=> true,
                'is_box'	=> true,
           	),

           	array(
           		'name'		=> __( 'Customize Admin Setting Box Display', 'woocommerce-predictive-search' ),
           		'desc'		=> __( 'By default each admin panel will open with all Setting Boxes in the CLOSED position.', 'woocommerce-predictive-search' ),
                'type' 		=> 'heading',
           	),
           	array(
				'type' 		=> 'onoff_toggle_box',
			),
			array(
           		'name'		=> __( 'Google Fonts', 'woocommerce-predictive-search' ),
           		'desc'		=> __( 'By Default Google Fonts are pulled from a static JSON file in this plugin. This file is updated but does not have the latest font releases from Google.', 'woocommerce-predictive-search' ),
                'type' 		=> 'heading',
           	),
           	array(
                'type' 		=> 'google_api_key',
           	),
           	array(
            	'name' 		=> __( 'House Keeping', 'woocommerce-predictive-search' ),
                'type' 		=> 'heading',
            ),
			array(
				'name' 		=> __( 'Clean Up On Deletion', 'woocommerce-predictive-search' ),
				'desc' 		=> __( 'On deletion (not deactivate) the plugin will completely remove all tables and data it created, leaving no trace it was ever here.', 'woocommerce-predictive-search' ),
				'id' 		=> $this->plugin_name . '_clean_on_deletion',
				'type' 		=> 'onoff_checkbox',
				'default'	=> 'no',
				'separate_option'	=> true,
				'free_version'		=> true,
				'checked_value'		=> 'yes',
				'unchecked_value'	=> 'no',
				'checked_label'		=> __( 'ON', 'woocommerce-predictive-search' ),
				'unchecked_label' 	=> __( 'OFF', 'woocommerce-predictive-search' ),
			),

     		array(
            	'name' 		=> __( 'Search Results No-Cache', 'woocommerce-predictive-search' ),
            	'desc'		=> __( 'While testing different setting and the results in search box dropdown you need to switch ON Results No-Cache On. Search box dropdown results are cached in local store for frontend users for faster delivery on repeat searches. Be sure to turn this OFF when you are finished testing.', 'woocommerce-predictive-search' ),
                'type' 		=> 'heading',
                'id'		=> 'predictive_search_nocache_box',
				'is_box'	=> true,
           	),
			array(
				'name' 		=> __( 'Results No-Cache', 'woocommerce-predictive-search' ),
				'id' 		=> 'woocommerce_search_is_debug',
				'type' 		=> 'onoff_checkbox',
				'default'	=> 'yes',
				'checked_value'		=> 'yes',
				'unchecked_value'	=> 'no',
				'checked_label'		=> __( 'ON', 'woocommerce-predictive-search' ),
				'unchecked_label' 	=> __( 'OFF', 'woocommerce-predictive-search' ),
			),

			array(
            	'name' 		=> __( 'Predictive Search Mode', 'woocommerce-predictive-search' ),
            	'desc'		=> __( '<strong>IMPORTANT!</strong> Remember to turn ON the No-Cache option so that you see the difference between the 2 search modes when testing.', 'woocommerce-predictive-search' ),
                'type' 		=> 'heading',
                'id'		=> 'predictive_search_mode_box',
				'is_box'	=> true,
           	),
           	array(
				'name' 		=> __( 'Search Mode', 'woocommerce-predictive-search' ),
				'desc'		=> '</span><span class="description predictive_search_mode_strict">' . __( "STRICT MODE will return exact match results. Example if user types 'out' the results will include all items that have 'out' at the start of a word such as 'outside', 'outsized' etc. This gives 100% relevant results every time but can lead to a lot of 'Nothing Found' results depending on how customers search your site.", 'woocommerce-predictive-search' ) . '</span>'
				. '<span class="description predictive_search_mode_broad">' . __( "BROAD MODE just like Strict mode will return results that have the search term at the start but will also search within a word. Example if user types 'out' all items that have 'out' at the start will be returned plus all that have 'out' within a word such as 'fadeout', 'about' etc. Results are not as accurate as STRICT MODE but there will be less 'Nothing Found' results.", 'woocommerce-predictive-search' ) . '</span><span>',
				'class'		=> 'predictive_search_mode',
				'id' 		=> 'predictive_search_mode',
				'type' 		=> 'switcher_checkbox',
				'default'	=> 'broad',
				'checked_value'		=> 'strict',
				'unchecked_value'	=> 'broad',
				'checked_label'		=> __( 'STRICT', 'woocommerce-predictive-search' ),
				'unchecked_label' 	=> __( 'BROAD', 'woocommerce-predictive-search' ),
			),

      		array(
            	'name' 		=> __('Search Page Configuration', 'woocommerce-predictive-search' ),
                'type' 		=> 'heading',
                'desc' 		=> ( class_exists('SitePress') ) ? __('Predictive Search has detected the WPML plugin. On install a search page was auto created for each language in use. Please use the WPML String Translations plugin to make translation for plugin text for each page. If adding another language after installing Predictive Search you have to manually create a search page for it.', 'woocommerce-predictive-search' ) : __('A search results page needs to be selected so that WooCommerce Predictive Search knows where to show search results. This page should have been created upon installation of the plugin, if not you need to create it.', 'woocommerce-predictive-search' ),
           		'id'		=> 'predictive_search_page_configuration_box',
           		'is_box'	=> true,
           	),
			array(
				'name' 		=> __( 'Search Page', 'woocommerce-predictive-search' ),
				'desc' 		=> __('Page contents:', 'woocommerce-predictive-search' ).' [woocommerce_search]',
				'id' 		=> 'woocommerce_search_page_id',
				'type' 		=> 'single_select_page',
			),

			array(
            	'name' 		=> __( 'Special Characters', 'woocommerce-predictive-search' ),
				'desc'		=> __( 'Select any special characters that are used on this site. Selecting a character will mean that results will be returned when user search input includes or excludes the special character. <strong>IMPORTANT!</strong> Do not turn this feature on unless needed. If ON - only select actual characters used in Product Titles, SKU, Category Names etc - each special character selected creates 1 extra query per search object, per product, post or page.', 'woocommerce-predictive-search' ),
                'type' 		=> 'heading',
                'id'		=> 'predictive_search_special_characters_box',
                'is_box'	=> true,
           	),
			array(
				'name' 		=> __( 'Special Character Function', 'woocommerce-predictive-search' ),
				'class'		=> 'woocommerce_search_remove_special_character',
				'id' 		=> 'woocommerce_search_remove_special_character',
				'type' 		=> 'onoff_checkbox',
				'default'	=> 'no',
				'checked_value'		=> 'yes',
				'unchecked_value'	=> 'no',
				'checked_label'		=> __( 'ON', 'woocommerce-predictive-search' ),
				'unchecked_label' 	=> __( 'OFF', 'woocommerce-predictive-search' ),
			),

			array(
                'type' 		=> 'heading',
				'class'		=> 'woocommerce_search_remove_special_character_container',
           	),
           	array(
				'name' 		=> __( 'Character Syntax', 'woocommerce-predictive-search' ),
				'id' 		=> 'woocommerce_search_replace_special_character',
				'type' 		=> 'onoff_radio',
				'default'	=> 'remove',
				'onoff_options' => array(
					array(
						'val' 				=> 'ignore',
						'text' 				=> __( 'IGNORE. ON to ignore or skip over special characters in the string.', 'woocommerce-predictive-search' ),
						'checked_label'		=> __( 'ON', 'woocommerce-predictive-search' ),
						'unchecked_label' 	=> __( 'OFF', 'woocommerce-predictive-search' ),
					),
					array(
						'val' 				=> 'remove',
						'text' 				=> __( 'REMOVE. ON to remove or see special characters as a space.', 'woocommerce-predictive-search' ).' <span class="description">('.__( 'recommended', 'woocommerce-predictive-search' ).')</span>' ,
						'checked_label'		=> __( 'ON', 'woocommerce-predictive-search' ),
						'unchecked_label' 	=> __( 'OFF', 'woocommerce-predictive-search' ),
					),
					array(
						'val' 				=> 'both',
						'text' 				=> __( 'BOTH. On to use ignore and remove for special characters.', 'woocommerce-predictive-search' ),
						'checked_label'		=> __( 'ON', 'woocommerce-predictive-search' ),
						'unchecked_label' 	=> __( 'OFF', 'woocommerce-predictive-search' ),
					),
				),
			),

			array(
				'name' 		=> __( "Select Characters", 'woocommerce-predictive-search' ),
				'id' 		=> 'woocommerce_search_special_characters',
				'type' 		=> 'multiselect',
				'css'		=> 'width:600px; min-height:80px;',
				'options'	=> WC_Predictive_Search_Functions::special_characters_list(),
			),

			array(
            	'name' 		=> __( 'Exclude From Predictive Search', 'woocommerce-predictive-search' ),
                'type' 		=> 'heading',
                'id'		=> 'predictive_search_exclude_box',
                'is_box'	=> true,
           	),
           	array(
				'name'      => __( 'Exclude Out Of Stock', 'woocommerce-predictive-search' ),
				'desc'		=> __( 'ON to exclude out of stock products from search results', 'woocommerce-predictive-search' ),
				'class'		=> 'woocommerce_search_exclude_out_stock',
				'id' 		=> 'woocommerce_search_exclude_out_stock',
				'type' 		=> 'onoff_checkbox',
				'default'	=> 'yes',
				'checked_value'		=> 'yes',
				'unchecked_value'	=> 'no',
				'checked_label'		=> __( 'ON', 'woocommerce-predictive-search' ),
				'unchecked_label' 	=> __( 'OFF', 'woocommerce-predictive-search' ),
			),
			array(  
				'name' 		=> __( 'Exclude Products', 'woocommerce-predictive-search' ),
				'id' 		=> 'woocommerce_search_exclude_products',
				'type' 		=> 'multiselect',
				'placeholder' => __( 'Search Products', 'woocommerce-predictive-search' ),
				'css'		=> 'width:600px; min-height:80px;',
				'options'	=> $all_products,
				'default'	=> $products_excluded,
				'options_url' => admin_url( 'admin-ajax.php?action=wc_ps_get_exclude_options&type=product&keyword=', 'relative' ),
			),
			array(  
				'name' 		=> __( 'Exclude Posts', 'woocommerce-predictive-search' ),
				'id' 		=> 'woocommerce_search_exclude_posts',
				'type' 		=> 'multiselect',
				'placeholder' => __( 'Search Posts', 'woocommerce-predictive-search' ),
				'css'		=> 'width:600px; min-height:80px;',
				'options'	=> $all_posts,
				'default'	=> $posts_excluded,
				'options_url' => admin_url( 'admin-ajax.php?action=wc_ps_get_exclude_options&type=post&keyword=', 'relative' ),
			),
			array(  
				'name' 		=> __( 'Exclude Pages', 'woocommerce-predictive-search' ),
				'id' 		=> 'woocommerce_search_exclude_pages',
				'type' 		=> 'multiselect',
				'placeholder' => __( 'Search Pages', 'woocommerce-predictive-search' ),
				'css'		=> 'width:600px; min-height:80px;',
				'options'	=> $all_pages,
				'default'	=> $pages_excluded,
				'options_url' => admin_url( 'admin-ajax.php?action=wc_ps_get_exclude_options&type=page&keyword=', 'relative' ),
			),

        ));
	}

	public function include_script() {
	?>
<script>
(function($) {

	$(document).ready(function() {

		if ( $("input.woocommerce_search_focus_enable:checked").val() != 'yes') {
			$('.woocommerce_search_focus_plugin_container').css( {'visibility': 'hidden', 'height' : '0px', 'overflow' : 'hidden', 'margin-bottom' : '0px' } );
		}

		if ( $("input.woocommerce_search_remove_special_character:checked").val() != 'yes') {
			$('.woocommerce_search_remove_special_character_container').css( {'visibility': 'hidden', 'height' : '0px', 'overflow' : 'hidden', 'margin-bottom' : '0px' } );
		}

		if ( $("input.predictive_search_mode:checked").val() != 'strict') {
			$('.predictive_search_mode_strict').hide();
		} else {
			$('.predictive_search_mode_broad').hide();
		}

		$(document).on( "a3rev-ui-onoff_checkbox-switch", '.woocommerce_search_focus_enable', function( event, value, status ) {
			$('.woocommerce_search_focus_plugin_container').attr('style','display:none;');
			if ( status == 'true' ) {
				$(".woocommerce_search_focus_plugin_container").slideDown();
			} else {
				$(".woocommerce_search_focus_plugin_container").slideUp();
			}
		});

		$(document).on( "a3rev-ui-onoff_checkbox-switch", '.woocommerce_search_remove_special_character', function( event, value, status ) {
			$('.woocommerce_search_remove_special_character_container').attr('style','display:none;');
			if ( status == 'true' ) {
				$(".woocommerce_search_remove_special_character_container").slideDown();
			} else {
				$(".woocommerce_search_remove_special_character_container").slideUp();
			}
		});

		$(document).on( "a3rev-ui-onoff_checkbox-switch", '.predictive_search_mode', function( event, value, status ) {
			if ( status == 'true' ) {
				$(".predictive_search_mode_strict").attr('style','display: inline;');
				$(".predictive_search_mode_broad").attr('style','display: none;');
			} else {
				$(".predictive_search_mode_strict").attr('style','display: none;');
				$(".predictive_search_mode_broad").attr('style','display: inline;');
			}
		});

	});

})(jQuery);
</script>
    <?php
	}
}

global $wc_predictive_search_global_settings;
$wc_predictive_search_global_settings = new WC_Predictive_Search_Global_Settings();

/**
 * wc_predictive_search_global_settings_form()
 * Define the callback function to show subtab content
 */
function wc_predictive_search_global_settings_form() {
	global $wc_predictive_search_global_settings;
	$wc_predictive_search_global_settings->settings_form();
}

?>