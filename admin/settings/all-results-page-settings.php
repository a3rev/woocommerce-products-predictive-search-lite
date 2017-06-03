<?php
/* "Copyright 2012 A3 Revolution Web Design" This software is distributed under the terms of GNU GENERAL PUBLIC LICENSE Version 3, 29 June 2007 */
// File Security Check
if ( ! defined( 'ABSPATH' ) ) exit;
?>
<?php
/*-----------------------------------------------------------------------------------
WC Predictive Search All Results Page Settings

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

class WC_PS_All_Results_Page_Settings extends WC_Predictive_Search_Admin_UI
{
	
	/**
	 * @var string
	 */
	private $parent_tab = 'all-results-page';
	
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
	public $form_key = 'wc_ps_all_results_pages_settings';
	
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
				'success_message'	=> __( 'All Results Pages successfully saved.', 'woocommerce-predictive-search' ),
				'error_message'		=> __( 'Error: All Results Pages can not save.', 'woocommerce-predictive-search' ),
				'reset_message'		=> __( 'All Results Pages successfully reseted.', 'woocommerce-predictive-search' ),
			);
		
		add_action( $this->plugin_name . '_set_default_settings' , array( $this, 'set_default_settings' ) );
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
			'name'				=> 'all-results-page',
			'label'				=> __( 'All Results Pages', 'woocommerce-predictive-search' ),
			'callback_function'	=> 'wc_ps_all_results_page_settings_form',
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
            	'name' 		=> __( 'Search results page settings', 'woocommerce-predictive-search' ),
                'type' 		=> 'heading',
                'id'		=> 'predictive_search_results_settings_box',
                'is_box'	=> true,
           	),
			array(  
				'name' 		=> __( 'Results', 'woocommerce-predictive-search' ),
				'desc' 		=> __('The number of results to show before endless scroll click to see more results.', 'woocommerce-predictive-search' ),
				'id' 		=> 'woocommerce_search_result_items',
				'type' 		=> 'text',
				'css' 		=> 'width:40px;',
				'default'	=> 10
			),
			array(  
				'name' 		=> __( 'Description character count', 'woocommerce-predictive-search' ),
				'desc' 		=> __('The number of characters from product descriptions that shows with each search result.', 'woocommerce-predictive-search' ),
				'id' 		=> 'woocommerce_search_text_lenght',
				'type' 		=> 'text',
				'css' 		=> 'width:40px;',
				'default'	=> 100
			),
			array(  
				'name' 		=> __( 'SKU', 'woocommerce-predictive-search' ),
				'desc' 		=> __('ON to show product SKU with search results', 'woocommerce-predictive-search' ),
				'id' 		=> 'woocommerce_search_sku_enable',
				'type' 		=> 'onoff_checkbox',
				'default'	=> 'yes',
				'checked_value'		=> 'yes',
				'unchecked_value'	=> 'no',
				'checked_label'		=> __( 'ON', 'woocommerce-predictive-search' ),
				'unchecked_label' 	=> __( 'OFF', 'woocommerce-predictive-search' ),
			),
			array(  
				'name' 		=> __( 'Price', 'woocommerce-predictive-search' ),
				'desc' 		=> __('ON to show product price with search results', 'woocommerce-predictive-search' ),
				'id' 		=> 'woocommerce_search_price_enable',
				'type' 		=> 'onoff_checkbox',
				'default'	=> 'yes',
				'checked_value'		=> 'yes',
				'unchecked_value'	=> 'no',
				'checked_label'		=> __( 'ON', 'woocommerce-predictive-search' ),
				'unchecked_label' 	=> __( 'OFF', 'woocommerce-predictive-search' ),
			),
			array(  
				'name' 		=> __( 'Add to cart', 'woocommerce-predictive-search' ),
				'desc' 		=> __('On to show Add to cart button with search results', 'woocommerce-predictive-search' ),
				'id' 		=> 'woocommerce_search_addtocart_enable',
				'type' 		=> 'onoff_checkbox',
				'default'	=> 'yes',
				'checked_value'		=> 'yes',
				'unchecked_value'	=> 'no',
				'checked_label'		=> __( 'ON', 'woocommerce-predictive-search' ),
				'unchecked_label' 	=> __( 'OFF', 'woocommerce-predictive-search' ),
			),
			array(  
				'name' 		=> __( 'Product Categories', 'woocommerce-predictive-search' ),
				'desc' 		=> __('On to show categories with search results', 'woocommerce-predictive-search' ),
				'id' 		=> 'woocommerce_search_categories_enable',
				'type' 		=> 'onoff_checkbox',
				'default'	=> 'yes',
				'checked_value'		=> 'yes',
				'unchecked_value'	=> 'no',
				'checked_label'		=> __( 'ON', 'woocommerce-predictive-search' ),
				'unchecked_label' 	=> __( 'OFF', 'woocommerce-predictive-search' ),
			),
			array(  
				'name' 		=> __( 'Product Tags', 'woocommerce-predictive-search' ),
				'desc' 		=> __('On to show tags with search results', 'woocommerce-predictive-search' ),
				'id' 		=> 'woocommerce_search_tags_enable',
				'type' 		=> 'onoff_checkbox',
				'default'	=> 'yes',
				'checked_value'		=> 'yes',
				'unchecked_value'	=> 'no',
				'checked_label'		=> __( 'ON', 'woocommerce-predictive-search' ),
				'unchecked_label' 	=> __( 'OFF', 'woocommerce-predictive-search' ),
			),
		
        ));
	}
	
}

global $wc_ps_all_results_page_settings;
$wc_ps_all_results_page_settings = new WC_PS_All_Results_Page_Settings();

/** 
 * wc_ps_all_results_page_settings_form()
 * Define the callback function to show subtab content
 */
function wc_ps_all_results_page_settings_form() {
	global $wc_ps_all_results_page_settings;
	$wc_ps_all_results_page_settings->settings_form();
}

?>