<?php
/* "Copyright 2012 A3 Revolution Web Design" This software is distributed under the terms of GNU GENERAL PUBLIC LICENSE Version 3, 29 June 2007 */
// File Security Check
if ( ! defined( 'ABSPATH' ) ) exit;
?>
<?php
/*-----------------------------------------------------------------------------------
WC Predictive Search Input Box Settings

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

class WC_Predictive_Search_Input_Box_Settings extends WC_Predictive_Search_Admin_UI
{
	
	/**
	 * @var string
	 */
	private $parent_tab = 'search-box-settings';
	
	/**
	 * @var array
	 */
	private $subtab_data;
	
	/**
	 * @var string
	 * You must change to correct option name that you are working
	 */
	public $option_name = 'wc_predictive_search_input_box_settings';
	
	/**
	 * @var string
	 * You must change to correct form key that you are working
	 */
	public $form_key = 'wc_predictive_search_input_box_settings';
	
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
				'success_message'	=> __( 'Search Box Settings successfully saved.', 'woocommerce-predictive-search' ),
				'error_message'		=> __( 'Error: Search Box Settings can not save.', 'woocommerce-predictive-search' ),
				'reset_message'		=> __( 'Search Box Settings successfully reseted.', 'woocommerce-predictive-search' ),
			);
		
		add_action( $this->plugin_name . '-' . $this->form_key . '_settings_end', array( $this, 'include_script' ) );

		add_action( $this->plugin_name . '_set_default_settings' , array( $this, 'set_default_settings' ) );

		add_action( $this->plugin_name . '_settings_' . 'predictive_search_searchbox_text' . '_start', array( $this, 'predictive_search_searchbox_text' ) );

		add_action( $this->plugin_name . '-' . $this->form_key . '_settings_init' , array( $this, 'after_save_settings' ) );

		add_action( $this->plugin_name . '_get_all_settings' , array( $this, 'get_settings' ) );
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

		if ( isset( $_REQUEST['woocommerce_search_box_text']) ) {
			update_option('woocommerce_search_box_text',  $_REQUEST['woocommerce_search_box_text'] );
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
			'name'				=> 'search-box-settings',
			'label'				=> __( 'Search Box', 'woocommerce-predictive-search' ),
			'callback_function'	=> 'wc_predictive_search_input_box_settings_form',
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

  		// Define settings			
     	$this->form_fields = apply_filters( $this->option_name . '_settings_fields', array(

     		array(
            	'name' 		=> __( 'Global Search Box Text', 'woocommerce-predictive-search' ),
                'type' 		=> 'heading',
				'id'		=> 'predictive_search_searchbox_text',
				'is_box'	=> true,
           	),

     		array(
            	'name' 		=> __( 'Dropdown Results Animation', 'woocommerce-predictive-search' ),
            	'desc'		=> '<img class="rwd_image_maps" src="'.WOOPS_IMAGES_URL.'/premium-results-animation.png" usemap="#resultsAnimationMap" style="width: auto; max-width: 100%;" border="0" />
<map name="resultsAnimationMap" id="resultsAnimationMap">
	<area shape="rect" coords="0,400,360,460" href="'.$this->pro_plugin_page_url.'" target="_blank" />
</map>',
                'type' 		=> 'heading',
                'id'		=> 'predictive_search_animiation_box',
                'is_box'	=> true,
           	),

           	array(
            	'name' 		=> __( 'Search In Category Feature', 'woocommerce-predictive-search' ),
            	'desc'		=> '<img class="rwd_image_maps" src="'.WOOPS_IMAGES_URL.'/premium-search-category-cache.png" usemap="#searchInCategoryMap" style="width: auto; max-width: 100%;" border="0" />
<map name="searchInCategoryMap" id="searchInCategoryMap">
	<area shape="rect" coords="410,160,930,230" href="'.$this->pro_plugin_page_url.'" target="_blank" />
</map>',
            	'id'		=> 'predictive_search_category_cache_box',
                'type' 		=> 'heading',
				'is_box'	=> true,
           	),
        ));
	}

	function predictive_search_searchbox_text() {
		if ( class_exists('SitePress') ) {
			$woocommerce_search_box_text = get_option('woocommerce_search_box_text', array() );
			if ( !is_array( $woocommerce_search_box_text) ) $woocommerce_search_box_text = array();

			global $sitepress;
			$active_languages = $sitepress->get_active_languages();
			if ( is_array($active_languages)  && count($active_languages) > 0 ) {
	?>
    		<tr valign="top" class="">
				<td class="forminp" colspan="2">
                <?php _e("Enter the translated search box text for each language for WPML to show it correct on the front end.", 'woocommerce-predictive-search' ); ?>
				</td>
			</tr>
    <?php
				foreach ( $active_languages as $language ) {
	?>
    		<tr valign="top" class="">
				<th class="titledesc" scope="row"><label for="woocommerce_search_box_text_<?php echo $language['code']; ?>"><?php _e('Text to Show', 'woocommerce-predictive-search' );?> (<?php echo $language['display_name']; ?>)</label></th>
				<td class="forminp">
                	<input type="text" class="" value="<?php if (isset($woocommerce_search_box_text[$language['code']]) ) esc_attr_e( stripslashes( $woocommerce_search_box_text[$language['code']] ) ); ?>" style="min-width:300px;" id="woocommerce_search_box_text_<?php echo $language['code']; ?>" name="woocommerce_search_box_text[<?php echo $language['code']; ?>]" /> <span class="description"><?php _e('&lt;empty&gt; shows nothing', 'woocommerce-predictive-search' ); ?></span>
				</td>
			</tr>
    <?php
				}
			}

		} else {
			$woocommerce_search_box_text = get_option('woocommerce_search_box_text', '' );
			if ( is_array( $woocommerce_search_box_text) ) $woocommerce_search_box_text = '';
	?>
            <tr valign="top" class="">
				<th class="titledesc" scope="row"><label for="woocommerce_search_box_text"><?php _e('Text to Show', 'woocommerce-predictive-search' );?></label></th>
				<td class="forminp">
                	<input type="text" class="" value="<?php esc_attr_e( stripslashes( $woocommerce_search_box_text ) ); ?>" style="min-width:300px;" id="woocommerce_search_box_text" name="woocommerce_search_box_text" /> <span class="description"><?php _e('&lt;empty&gt; shows nothing', 'woocommerce-predictive-search' ); ?></span>
				</td>
			</tr>
    <?php }
	}

	public function include_script() {
		wp_enqueue_script( 'jquery-rwd-image-maps' );
	?>
    <?php
	}
}

global $wc_predictive_search_input_box_settings_panel;
$wc_predictive_search_input_box_settings_panel = new WC_Predictive_Search_Input_Box_Settings();

/** 
 * wc_predictive_search_performance_settings_form()
 * Define the callback function to show subtab content
 */
function wc_predictive_search_input_box_settings_form() {
	global $wc_predictive_search_input_box_settings_panel;
	$wc_predictive_search_input_box_settings_panel->settings_form();
}

?>