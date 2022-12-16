<?php
/* "Copyright 2012 A3 Revolution Web Design" This software is distributed under the terms of GNU GENERAL PUBLIC LICENSE Version 3, 29 June 2007 */

namespace A3Rev\WCPredictiveSearch\FrameWork\Settings {

use A3Rev\WCPredictiveSearch\FrameWork;

// File Security Check
if ( ! defined( 'ABSPATH' ) ) exit;

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

class Global_Panel extends FrameWork\Admin_UI
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

		add_action( $this->plugin_name . '-' . $this->form_key . '_settings_end', array( $this, 'include_modal_script' ) );

		add_action( $this->plugin_name . '_set_default_settings' , array( $this, 'set_default_settings' ) );

		add_action( $this->plugin_name . '-' . $this->form_key . '_settings_init' , array( $this, 'after_save_settings' ) );
		//add_action( $this->plugin_name . '_get_all_settings' , array( $this, 'get_settings' ) );

		add_action( $this->plugin_name . '_settings_' . 'predictive_search_shortcode_box' . '_start', array( $this, 'predictive_search_shortcode_box' ) );
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
		$GLOBALS[$this->plugin_prefix.'admin_interface']->reset_settings( $this->form_fields, $this->option_name, false );
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
					$wc_ps_exclude_data->insert_item( absint( $item_id ), 'product' );
				}
			}
			if ( isset( $_POST['woocommerce_search_exclude_posts'] ) && count( $_POST['woocommerce_search_exclude_posts'] ) > 0 ) {
				foreach ( $_POST['woocommerce_search_exclude_posts'] as $item_id ) {
					$wc_ps_exclude_data->insert_item( absint( $item_id ), 'post' );
				}
			}
			if ( isset( $_POST['woocommerce_search_exclude_pages'] ) && count( $_POST['woocommerce_search_exclude_pages'] ) > 0 ) {
				foreach ( $_POST['woocommerce_search_exclude_pages'] as $item_id ) {
					$wc_ps_exclude_data->insert_item( absint( $item_id ), 'page' );
				}
			}
		}
	}

	/*-----------------------------------------------------------------------------------*/
	/* get_settings()
	/* Get settings with function called from Admin Interface */
	/*-----------------------------------------------------------------------------------*/
	public function get_settings() {
		$GLOBALS[$this->plugin_prefix.'admin_interface']->get_settings( $this->form_fields, $this->option_name );
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
		$output = '';
		$output .= $GLOBALS[$this->plugin_prefix.'admin_interface']->admin_forms( $this->form_fields, $this->form_key, $this->option_name, $this->form_messages );

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
		
		if ( is_admin() && in_array (basename($_SERVER['PHP_SELF']), array('admin.php') ) && isset( $_GET['page'] ) && sanitize_key( $_GET['page'] ) == 'woo-predictive-search' && ( ! isset( $_GET['tab'] ) || sanitize_key( $_GET['tab'] ) == 'global-settings' ) ) {

			if ( isset( $_POST['bt_save_settings'] ) )  {
				if ( isset( $_POST['woocommerce_search_exclude_products'] ) && is_array( $_POST['woocommerce_search_exclude_products'] ) ) {
					$products_excluded     = array_map( 'absint', $_POST['woocommerce_search_exclude_products'] );
				}
				$posts_excluded = array();
				if ( isset( $_POST['woocommerce_search_exclude_posts'] ) && is_array( $_POST['woocommerce_search_exclude_posts'] ) ) {
					$posts_excluded        = array_map( 'absint', $_POST['woocommerce_search_exclude_posts'] );
				}
				$pages_excluded = array();
				if ( isset( $_POST['woocommerce_search_exclude_pages'] ) && is_array( $_POST['woocommerce_search_exclude_pages'] ) ) {
					$pages_excluded        = array_map( 'absint', $_POST['woocommerce_search_exclude_pages'] );
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
				'options'	=> \A3Rev\WCPredictiveSearch\Functions::special_characters_list(),
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

			array(
            	'name' 		=> __( 'Shortcode', 'woocommerce-predictive-search' ),
                'type' 		=> 'heading',
                'id'		=> 'predictive_search_shortcode_box',
                'is_box'	=> true,
           	),

        ));
	}

	public function predictive_search_shortcode_box() {
	?>
		<tr valign="top" class="">
			<td class="forminp" colspan="2">
            	<?php _e( 'You can add the Predictive Search Box by shortcode anywhere in a theme or page builder that accepts shortcodes.', 'woocommerce-predictive-search' ); ?>
            	<br />
            	<div style="text-align: center; margin-top: 20px;"><a data-toggle="modal" href="#ps_generate_shortcode-modal" class="button button-primary"><?php _e( 'Create Shortcode', 'woocommerce-predictive-search' ); ?></a></div>
			</td>
		</tr>
	<?php
	}

	public function include_modal_script() {
		$disabled_cat_dropdown = false;
		$product_categories = false;

		$items_search_default = \A3Rev\WCPredictiveSearch\Widgets::get_items_search();
	?>
		<script type="text/javascript">

			function woo_search_widget_add_shortcode(){
				var number_items = '';
				<?php foreach ($items_search_default as $key => $data) {?>
				var woo_search_<?php echo $key ?>_items = '<?php echo $key ?>_items="' + jQuery("#woo_search_<?php echo $key ?>_items").val() + '" ';
				number_items += woo_search_<?php echo $key ?>_items;
				<?php } ?>
				var woo_search_widget_template = jQuery("#woo_search_widget_template").val();
				var woo_search_show_image = 0;
				if ( jQuery('#woo_search_show_image').is(":checked") ) {
					woo_search_show_image = 1;
				}
				var woo_search_show_price = 0;
				if ( jQuery('#woo_search_show_price').is(":checked") ) {
					woo_search_show_price = 1;
				}
				var woo_search_show_desc = 0;
				if ( jQuery('#woo_search_show_desc').is(":checked") ) {
					woo_search_show_desc = 1;
				}
				var woo_search_show_in_cat = 0;
				if ( jQuery('#woo_search_show_in_cat').is(":checked") ) {
					woo_search_show_in_cat = 1;
				}
				var woo_search_text_lenght = jQuery("#woo_search_text_lenght").val();
				var woo_search_align = jQuery("#woo_search_align").val();
				var woo_search_width = jQuery("#woo_search_width").val();
				var woo_search_padding_top = jQuery("#woo_search_padding_top").val();
				var woo_search_padding_bottom = jQuery("#woo_search_padding_bottom").val();
				var woo_search_padding_left = jQuery("#woo_search_padding_left").val();
				var woo_search_padding_right = jQuery("#woo_search_padding_right").val();
				var woo_search_box_text = jQuery("#woo_search_box_text").val();
				var woo_search_style = '';
				var wrap = '';
				if (woo_search_align == 'center') woo_search_style += 'float:none;margin:auto;display:table;';
				else if (woo_search_align == 'left-wrap') woo_search_style += 'float:left;';
				else if (woo_search_align == 'right-wrap') woo_search_style += 'float:right;';
				else woo_search_style += 'float:'+woo_search_align+';';
				
				if(woo_search_align == 'left-wrap' || woo_search_align == 'right-wrap') wrap = 'wrap="true"';
				
				if (parseInt(woo_search_width) > 0) woo_search_style += 'width:'+parseInt(woo_search_width)+'px;';
				if (parseInt(woo_search_padding_top) >= 0) woo_search_style += 'padding-top:'+parseInt(woo_search_padding_top)+'px;';
				if (parseInt(woo_search_padding_bottom) >= 0) woo_search_style += 'padding-bottom:'+parseInt(woo_search_padding_bottom)+'px;';
				if (parseInt(woo_search_padding_left) >= 0) woo_search_style += 'padding-left:'+parseInt(woo_search_padding_left)+'px;';
				if (parseInt(woo_search_padding_right) >= 0) woo_search_style += 'padding-right:'+parseInt(woo_search_padding_right)+'px;';
				var win = window.dialogArguments || opener || parent || top;
				var shortcode_output = '[woocommerce_search_widget ' + number_items + ' widget_template="'+woo_search_widget_template+'" show_image="'+woo_search_show_image+'" show_price="'+woo_search_show_price+'" show_desc="'+woo_search_show_desc+'" show_in_cat="'+woo_search_show_in_cat+'" character_max="'+woo_search_text_lenght+'" style="'+woo_search_style+'" '+wrap+' search_box_text="'+woo_search_box_text+'" ]';

				jQuery(".shortcode_container").html( shortcode_output );
			}
		</script>
		<style type="text/css">
			.field_content {
				padding:0 40px;
			}
			.field_content label{
				width:150px;
				float:left;
				text-align:left;
			}
			.field_content p {
				clear: both;
			}
			.shortcode_container {
				background: rgba(0, 0, 0, 0.07);
			    color: #fc2323;
			    padding: 30px 20px;
			    margin-top: 20px;
			}
			body.mobile.modal-open #wpwrap {
				position:  inherit;
			}
			@media screen and ( max-width: 782px ) {
				#woo_search_box_text {
					width:100% !important;	
				}
				label[for="woo_search_padding"] {
					width: 100%;
				}
			}
		</style>

    	<div class="modal fade wc-ps-modal" id="ps_generate_shortcode-modal" tabindex="-1" role="dialog" aria-hidden="true" style="display: none;">
			<div class="modal-dialog modal-lg" role="document">
				<div class="modal-content">
					<div class="modal-header">
						<h5 class="modal-title"><?php echo __( 'Generate Shortcode', 'woocommerce-predictive-search' ); ?></h5>
					</div>
					<div class="modal-body m-3">
						<div class="field_content">
			                <?php foreach ($items_search_default as $key => $data) { ?>
			                <p><label for="woo_search_<?php echo $key ?>_items"><?php echo $data['name']; ?>:</label> <input style="width:100px;" size="10" id="woo_search_<?php echo $key ?>_items" name="woo_search_<?php echo $key ?>_items" type="text" value="<?php echo $data['number'] ?>" /> <span class="description"><?php _e('Number of', 'woocommerce-predictive-search' ); echo ' '.$data['name'].' '; _e('results to show in dropdown', 'woocommerce-predictive-search' ); ?></span></p> 
			                <?php } ?>
			                <p><label for="woo_search_widget_template"><?php _e('Select Template', 'woocommerce-predictive-search' ); ?>:</label> <select style="width:100px" id="woo_search_widget_template" name="woo_search_widget_template"><option value="sidebar" selected="selected"><?php _e('Widget', 'woocommerce-predictive-search' ); ?></option><option value="header"><?php _e('Header', 'woocommerce-predictive-search' ); ?></option></select></p>

			                <p><label for="woo_search_show_image"><?php _e('Image', 'woocommerce-predictive-search' ); ?>:</label> <input type="checkbox" checked="checked" id="woo_search_show_image" name="woo_search_show_image" value="1" /> <span class="description"><?php _e('Show Results Images', 'woocommerce-predictive-search' ); ?></span></p>
			                <p><label for="woo_search_show_price"><?php _e('Price', 'woocommerce-predictive-search' ); ?>:</label> <input type="checkbox" checked="checked" id="woo_search_show_price" name="woo_search_show_price" value="1" /> <span class="description"><?php _e('Product Results - Show Prices', 'woocommerce-predictive-search' ); ?></span></p>
			            	<p><label for="woo_search_show_desc"><?php _e('Description', 'woocommerce-predictive-search' ); ?>:</label> <input type="checkbox" checked="checked" id="woo_search_show_desc" name="woo_search_show_desc" value="1" /> <span class="description"><?php _e('Show Results Description', 'woocommerce-predictive-search' ); ?></span></p>
			            	<p><label for="woo_search_text_lenght"><?php _e('Characters Count', 'woocommerce-predictive-search' ); ?>:</label> <input style="width:100px;" size="10" id="woo_search_text_lenght" name="woo_search_text_lenght" type="text" value="100" /> <span class="description"><?php _e('Number of results description characters', 'woocommerce-predictive-search' ); ?></span></p>
			            	<p><label for="woo_search_show_in_cat"><?php _e('Product Categories', 'woocommerce-predictive-search' ); ?>:</label> <input type="checkbox" checked="checked" id="woo_search_show_in_cat" name="woo_search_show_in_cat" value="1" /> <span class="description"><?php _e('Product Results - Show Categories', 'woocommerce-predictive-search' ); ?></span></p>
			                <p><label for="woo_search_align"><?php _e('Alignment', 'woocommerce-predictive-search' ); ?>:</label> <select style="width:100px" id="woo_search_align" name="woo_search_align"><option value="none" selected="selected"><?php _e('None', 'woocommerce-predictive-search' ); ?></option><option value="left-wrap"><?php _e('Left - wrap', 'woocommerce-predictive-search' ); ?></option><option value="left"><?php _e('Left - no wrap', 'woocommerce-predictive-search' ); ?></option><option value="center"><?php _e('Center', 'woocommerce-predictive-search' ); ?></option><option value="right-wrap"><?php _e('Right - wrap', 'woocommerce-predictive-search' ); ?></option><option value="right"><?php _e('Right - no wrap', 'woocommerce-predictive-search' ); ?></option></select> <span class="description"><?php _e('Horizontal aliginment of search box', 'woocommerce-predictive-search' ); ?></span></p>
			                <p><label for="woo_search_width"><?php _e('Search box width', 'woocommerce-predictive-search' ); ?>:</label> <input style="width:100px;" size="10" id="woo_search_width" name="woo_search_width" type="text" value="200" />px</p>
			                <p><label for="woo_search_box_text"><?php _e('Search box text message', 'woocommerce-predictive-search' ); ?>:</label> <input style="width:300px;" size="10" id="woo_search_box_text" name="woo_search_box_text" type="text" value="" /></p>
			                <p><label for="woo_search_padding"><strong><?php _e('Padding', 'woocommerce-predictive-search' ); ?></strong>:</label>
							<label for="woo_search_padding_top" style="width:auto; float:none"><?php _e('Above', 'woocommerce-predictive-search' ); ?>:</label><input style="width:50px;" size="10" id="woo_search_padding_top" name="woo_search_padding_top" type="text" value="10" />px &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			                <label for="woo_search_padding_bottom" style="width:auto; float:none"><?php _e('Below', 'woocommerce-predictive-search' ); ?>:</label> <input style="width:50px;" size="10" id="woo_search_padding_bottom" name="woo_search_padding_bottom" type="text" value="10" />px &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			                <label for="woo_search_padding_left" style="width:auto; float:none"><?php _e('Left', 'woocommerce-predictive-search' ); ?>:</label> <input style="width:50px;" size="10" id="woo_search_padding_left" name="woo_search_padding_left" type="text" value="0" />px &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			                <label for="woo_search_padding_right" style="width:auto; float:none"><?php _e('Right', 'woocommerce-predictive-search' ); ?>:</label> <input style="width:50px;" size="10" id="woo_search_padding_right" name="woo_search_padding_right" type="text" value="0" />px
			                </p>
						</div>
						<div class="shortcode_container"></div>
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-primary" onclick="woo_search_widget_add_shortcode();"><?php echo __( 'Get Shortcode', 'woocommerce-predictive-search' ); ?></button>
						<button type="button" class="btn btn-secondary" data-dismiss="modal"><?php echo __( 'Close', 'woocommerce-predictive-search' ); ?></button>
					</div>
				</div>
			</div>
		</div>
	<?php
		if ( ! wp_script_is( 'bootstrap-modal', 'registered' ) 
			&& ! wp_script_is( 'bootstrap-modal', 'enqueued' ) ) {
			$GLOBALS[$this->plugin_prefix.'admin_interface']->register_modal_scripts();
		}

		wp_enqueue_style( 'bootstrap-modal' );

		// Don't include modal script if bootstrap is loaded by theme or plugins
		if ( wp_script_is( 'bootstrap', 'registered' ) 
			|| wp_script_is( 'bootstrap', 'enqueued' ) ) {
			
			wp_enqueue_script( 'bootstrap' );
			
			return;
		}

		wp_enqueue_script( 'bootstrap-modal' );
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

}

// global code
namespace {

/**
 * wc_predictive_search_global_settings_form()
 * Define the callback function to show subtab content
 */
function wc_predictive_search_global_settings_form() {
	global $wc_predictive_search_global_settings;
	$wc_predictive_search_global_settings->settings_form();
}

}
