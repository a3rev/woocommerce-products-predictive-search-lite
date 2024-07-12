<?php
/**
 * WC Predictive Search Hook Filter
 *
 * Hook anf Filter into woocommerce plugin
 *
 * Table Of Contents
 *
 * parse_shortcode_search_widget()
 * add_search_widget_icon()
 * add_search_widget_mce_popup()
 * parse_shortcode_search_result()
 * display_search()
 */

namespace A3Rev\WCPredictiveSearch;

class Shortcodes 
{
	public static function parse_shortcode_search_widget($attributes) {
		// Don't show content for shortcode on Dashboard, still support for admin ajax
		if ( is_admin() && ( ! defined( 'DOING_AJAX' ) || ! DOING_AJAX ) ) return;

		if ( ! is_array( $attributes ) ) {
			$attributes = array();
		}

		$attr = array_merge( array(
			'widget_template'  => 'sidebar',
			'show_catdropdown' => 1,
			'default_cat'      => '',
			'show_image'       => 1,
			'show_price'       => 1,
			'show_addtocart'   => 0,
			'show_desc'        => 1,
			'show_in_cat'      => 1,
			'character_max'    => 100,
			'style'            => '',
			'wrap'             => 'false',
			'search_box_text'  => '',
        ), $attributes );

        // WPCS: XSS ok.
		$widget_template  = esc_attr( $attr['widget_template'] );
		$show_catdropdown = intval( $attr['show_catdropdown'] );
		$default_cat      = esc_attr( $attr['default_cat'] );
		$show_image       = intval( $attr['show_image'] );
		$show_price       = intval( $attr['show_price'] );
		$show_addtocart   = intval( $attr['show_addtocart'] );
		$show_desc        = intval( $attr['show_desc'] );
		$show_in_cat      = intval( $attr['show_in_cat'] );
		$character_max    = intval( $attr['character_max'] );
		$style            = esc_attr( $attr['style'] );
		$wrap             = esc_attr( $attr['wrap'] );
		$search_box_text  = esc_attr( $attr['search_box_text'] );

		$text_lenght = $character_max;

		$break_div = '<div style="clear:both;"></div>';
		if ($wrap == 'true') $break_div = '';

		if ( trim($search_box_text) == '' ) {
			if ( class_exists('SitePress') ) {
				$current_lang = ICL_LANGUAGE_CODE;
				$search_box_texts = get_option('woocommerce_search_box_text', array() );
				if ( is_array($search_box_texts) && isset($search_box_texts[$current_lang]) ) $search_box_text = esc_attr( stripslashes( trim( $search_box_texts[$current_lang] ) ) );
				else $search_box_text = '';
			} else {
				$search_box_text = get_option('woocommerce_search_box_text', '' );
				if ( is_array($search_box_text) ) $search_box_text = '';
			}
		}

		$ps_id = rand(100, 10000);

		$row                  = 0;
		$search_list          = array();
		$number_items         = array();
		$items_search_default = Widgets::get_items_search();

		foreach ($items_search_default as $key => $data) {
			$item_key = $key.'_items';
			if ( isset($attr[$item_key]) ) {
				if ( $attr[$item_key] > 0 ) {
					$number_items[$key] = $attr[$item_key];
					$row += $attr[$item_key];
					$row++;
					$search_list[] = $key;
				}
			} elseif ( $data['number'] > 0 ) {
				$number_items[$key] = $data['number'];
				$row += $data['number'];
				$row++;
				$search_list[] = $key;
			}
		}

		$search_in = json_encode($number_items);
		$show_catdropdown = 0;

		$ps_args = array(
			'search_box_text'  => $search_box_text,
			'row'              => $row,
			'text_lenght'      => $text_lenght,
			'show_catdropdown' => $show_catdropdown,
			'default_cat'      => $default_cat,
			'widget_template'  => $widget_template,
			'show_image'       => $show_image,
			'show_price'       => $show_price,
			'show_addtocart'   => $show_addtocart,
			'show_desc'        => $show_desc,
			'show_in_cat'      => $show_in_cat,
			'search_in'        => $search_in,
			'search_list'      => $search_list,
		);
		$search_form = wc_ps_search_form( $ps_id, $widget_template, $ps_args, false );

		$search_form_html = '<div class="wc_ps_shortcode_container" style="max-width: 100%; '.$style.'">' . $search_form . '</div>' . $break_div;

		return $search_form_html;
	}

	public static function add_search_widget_icon($context){
		$image_btn = WOOPS_IMAGES_URL . "/ps_icon.png";
		$out = '<a href="#TB_inline?width=670&height=500&modal=false&inlineId=woo_search_widget_shortcode" class="thickbox" title="'.__('Insert WooCommerce Predictive Search Shortcode', 'woocommerce-predictive-search' ).'"><img class="search_widget_shortcode_icon" src="'.$image_btn.'" alt="'.__('Insert WooCommerce Predictive Search Shortcode', 'woocommerce-predictive-search' ).'" /></a>';
		return $context . $out;
	}
	
	//Action target that displays the popup to insert a form to a post/page
	public static function add_search_widget_mce_popup(){
		$disabled_cat_dropdown = false;
		$product_categories = false;

		$items_search_default = Widgets::get_items_search();
		?>
		<script type="text/javascript">
			jQuery(document).ready(function(){
				jQuery('#woo_search_show_catdropdown').on('click', function(){
					if ( jQuery(this).is(':checked') ) {
						jQuery('.woo_search_set_default_cat_container').show();
					} else {
						jQuery('.woo_search_set_default_cat_container').hide();
					}
				});
			});

			function woo_search_widget_add_shortcode(){
				var number_items = '';
				<?php foreach ($items_search_default as $key => $data) {?>
				var woo_search_<?php echo $key ?>_items = '<?php echo $key ?>_items="' + jQuery("#woo_search_<?php echo $key ?>_items").val() + '" ';
				number_items += woo_search_<?php echo $key ?>_items;
				<?php } ?>
				var woo_search_widget_template = jQuery("#woo_search_widget_template").val();
				var woo_search_set_default_cat = jQuery('#woo_search_set_default_cat').val();
				var woo_search_show_catdropdown = 0;
				if ( jQuery('#woo_search_show_catdropdown').is(":checked") ) {
					woo_search_show_catdropdown = 1;
				} else {
					woo_search_set_default_cat = '';
				}
				var woo_search_show_image = 0;
				if ( jQuery('#woo_search_show_image').is(":checked") ) {
					woo_search_show_image = 1;
				}
				var woo_search_show_price = 0;
				if ( jQuery('#woo_search_show_price').is(":checked") ) {
					woo_search_show_price = 1;
				}
				var woo_search_show_addtocart = 0;
				if ( jQuery('#woo_search_show_addtocart').is(":checked") ) {
					woo_search_show_addtocart = 1;
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
				win.send_to_editor('[woocommerce_search_widget ' + number_items + ' widget_template="'+woo_search_widget_template+'" show_catdropdown="'+woo_search_show_catdropdown+'" default_cat="'+woo_search_set_default_cat+'" show_image="'+woo_search_show_image+'" show_price="'+woo_search_show_price+'" show_addtocart="'+woo_search_show_addtocart+'" show_desc="'+woo_search_show_desc+'" show_in_cat="'+woo_search_show_in_cat+'" character_max="'+woo_search_text_lenght+'" style="'+woo_search_style+'" '+wrap+' search_box_text="'+woo_search_box_text+'" ]');
			}
			
			
		</script>
		<style type="text/css">
		#TB_ajaxContent{width:auto !important;}
		#TB_ajaxContent p {
			padding:2px 0;	
		}
		.field_content {
			padding:0 40px;
		}
		.field_content label{
			width:150px;
			float:left;
			text-align:left;
		}
		.a3-view-docs-button {
			background-color: #FFFFE0 !important;
			border: 1px solid #E6DB55 !important;
			border-radius: 3px;
			-webkit-border-radius: 3px;
			-moz-border-radius: 3px;
			color: #21759B !important;
			outline: 0 none;
			text-shadow:none !important;
			font-weight:normal !important;
			font-family: sans-serif;
			font-size: 12px;
			text-decoration: none;
			padding: 3px 8px;
			position: relative;
			margin-left: 4px;
			white-space:nowrap;
		}
		.a3-view-docs-button:hover {
			color: #D54E21 !important;
		}
		@media screen and ( max-width: 782px ) {
			#woo_search_box_text {
				width:100% !important;	
			}
		}
		@media screen and ( max-width: 480px ) {
			.a3_ps_exclude_item {
				float:none !important;
				display:block;
			}
		}
		</style>
		<div id="woo_search_widget_shortcode" style="display:none;">
		  <div style="height: 98%; overflow: auto;">
			<h3><?php _e('Customize the Predictive Search Shortcode', 'woocommerce-predictive-search' ); ?> <a class="add-new-h2 a3-view-docs-button" target="_blank" href="<?php echo WOO_PREDICTIVE_SEARCH_DOCS_URI; ?>#section-16" ><?php _e('View Docs', 'woocommerce-predictive-search' ); ?></a></h3>
			<div style="clear:both"></div>
			<div class="field_content">
                <?php foreach ($items_search_default as $key => $data) { ?>
                <p><label for="woo_search_<?php echo $key ?>_items"><?php echo $data['name']; ?>:</label> <input style="width:100px;" size="10" id="woo_search_<?php echo $key ?>_items" name="woo_search_<?php echo $key ?>_items" type="text" value="<?php echo $data['number'] ?>" /> <span class="description"><?php _e('Number of', 'woocommerce-predictive-search' ); echo ' '.$data['name'].' '; _e('results to show in dropdown', 'woocommerce-predictive-search' ); ?></span></p> 
                <?php } ?>
                <p><label for="woo_search_widget_template"><?php _e('Select Template', 'woocommerce-predictive-search' ); ?>:</label> <select style="width:100px" id="woo_search_widget_template" name="woo_search_widget_template"><option value="sidebar" selected="selected"><?php _e('Widget', 'woocommerce-predictive-search' ); ?></option><option value="header"><?php _e('Header', 'woocommerce-predictive-search' ); ?></option></select></p>
                <p>
                	<label for="woo_search_show_catdropdown"><?php _e('Category Dropdown', 'woocommerce-predictive-search' ); ?>:</label> <input <?php echo ( $disabled_cat_dropdown ) ? 'disabled="disabled"' : ''; ?> type="checkbox" checked="checked" id="woo_search_show_catdropdown" name="woo_search_show_catdropdown" value="1" /> <span class="description"><?php _e('Search in Product Category Feature', 'woocommerce-predictive-search' ); ?></span>
                	<?php if ( $disabled_cat_dropdown ) { ?>
                	<br>
            		<label>&nbsp;</label><span><?php echo sprintf( __( 'Activate and build <a href="%s" target="_blank">Category Cache</a> to activate this feature', 'woocommerce-predictive-search' ), admin_url( 'admin.php?page=woo-predictive-search&tab=search-box-settings&box_open=predictive_search_category_cache_box#predictive_search_category_cache_box', 'relative' ) ); ?></span>
            		<?php } ?>
            	</p>

            	
            	<p class="woo_search_set_default_cat_container" style="<?php if ( $disabled_cat_dropdown || false === $product_categories ) { ?>display: none;<?php } ?>">
            		<label for="woo_search_set_default_cat"><?php _e('Default Category', 'woocommerce-predictive-search' ); ?>:</label> 
            		<select style="width:100px" id="woo_search_set_default_cat" name="woo_search_set_default_cat">
            			<option value="" selected="selected"><?php _e('All', 'woocommerce-predictive-search' ); ?></option>
            		<?php if ( $product_categories ) { ?>
						<?php foreach ( $product_categories as $category_data ) { ?>
						<option value="<?php echo esc_attr( $category_data['slug'] ); ?>"><?php echo esc_html( $category_data['name'] ); ?></option>
						<?php } ?>
            		<?php } ?>
            		</select> 
            		<span class="description"><?php _e('Set category as default selected category for Category Dropdown', 'woocommerce-predictive-search' ); ?></span>
            	</p>

                <p><label for="woo_search_show_image"><?php _e('Image', 'woocommerce-predictive-search' ); ?>:</label> <input type="checkbox" checked="checked" id="woo_search_show_image" name="woo_search_show_image" value="1" /> <span class="description"><?php _e('Show Results Images', 'woocommerce-predictive-search' ); ?></span></p>
                <p><label for="woo_search_show_price"><?php _e('Price', 'woocommerce-predictive-search' ); ?>:</label> <input type="checkbox" checked="checked" id="woo_search_show_price" name="woo_search_show_price" value="1" /> <span class="description"><?php _e('Product Results - Show Prices', 'woocommerce-predictive-search' ); ?></span></p>
                <p><label for="woo_search_show_addtocart"><?php _e('Add to cart', 'woocommerce-predictive-search' ); ?>:</label> <input type="checkbox" id="woo_search_show_addtocart" name="woo_search_show_addtocart" value="1" /> <span class="description"><?php _e('Show Results Add to cart button', 'woocommerce-predictive-search' ); ?></span></p>
            	<p><label for="woo_search_show_desc"><?php _e('Description', 'woocommerce-predictive-search' ); ?>:</label> <input type="checkbox" checked="checked" id="woo_search_show_desc" name="woo_search_show_desc" value="1" /> <span class="description"><?php _e('Show Results Description', 'woocommerce-predictive-search' ); ?></span></p>
            	<p><label for="woo_search_text_lenght"><?php _e('Characters Count', 'woocommerce-predictive-search' ); ?>:</label> <input style="width:100px;" size="10" id="woo_search_text_lenght" name="woo_search_text_lenght" type="text" value="100" /> <span class="description"><?php _e('Number of results description characters', 'woocommerce-predictive-search' ); ?></span></p>
            	<p><label for="woo_search_show_in_cat"><?php _e('Product Categories', 'woocommerce-predictive-search' ); ?>:</label> <input type="checkbox" checked="checked" id="woo_search_show_in_cat" name="woo_search_show_in_cat" value="1" /> <span class="description"><?php _e('Product Results - Show Categories', 'woocommerce-predictive-search' ); ?></span></p>
                <p><label for="woo_search_align"><?php _e('Alignment', 'woocommerce-predictive-search' ); ?>:</label> <select style="width:100px" id="woo_search_align" name="woo_search_align"><option value="none" selected="selected"><?php _e('None', 'woocommerce-predictive-search' ); ?></option><option value="left-wrap"><?php _e('Left - wrap', 'woocommerce-predictive-search' ); ?></option><option value="left"><?php _e('Left - no wrap', 'woocommerce-predictive-search' ); ?></option><option value="center"><?php _e('Center', 'woocommerce-predictive-search' ); ?></option><option value="right-wrap"><?php _e('Right - wrap', 'woocommerce-predictive-search' ); ?></option><option value="right"><?php _e('Right - no wrap', 'woocommerce-predictive-search' ); ?></option></select> <span class="description"><?php _e('Horizontal aliginment of search box', 'woocommerce-predictive-search' ); ?></span></p>
                <p><label for="woo_search_width"><?php _e('Search box width', 'woocommerce-predictive-search' ); ?>:</label> <input style="width:100px;" size="10" id="woo_search_width" name="woo_search_width" type="text" value="200" />px</p>
                <p><label for="woo_search_box_text"><?php _e('Search box text message', 'woocommerce-predictive-search' ); ?>:</label> <input style="width:300px;" size="10" id="woo_search_box_text" name="woo_search_box_text" type="text" value="" /></p>
                <p><label for="woo_search_padding"><strong><?php _e('Padding', 'woocommerce-predictive-search' ); ?></strong>:</label><br /> 
				<label for="woo_search_padding_top" style="width:auto; float:none"><?php _e('Above', 'woocommerce-predictive-search' ); ?>:</label><input style="width:50px;" size="10" id="woo_search_padding_top" name="woo_search_padding_top" type="text" value="10" />px &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                <label for="woo_search_padding_bottom" style="width:auto; float:none"><?php _e('Below', 'woocommerce-predictive-search' ); ?>:</label> <input style="width:50px;" size="10" id="woo_search_padding_bottom" name="woo_search_padding_bottom" type="text" value="10" />px &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                <label for="woo_search_padding_left" style="width:auto; float:none"><?php _e('Left', 'woocommerce-predictive-search' ); ?>:</label> <input style="width:50px;" size="10" id="woo_search_padding_left" name="woo_search_padding_left" type="text" value="0" />px &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                <label for="woo_search_padding_right" style="width:auto; float:none"><?php _e('Right', 'woocommerce-predictive-search' ); ?>:</label> <input style="width:50px;" size="10" id="woo_search_padding_right" name="woo_search_padding_right" type="text" value="0" />px
                </p>
			</div>
            <p><input type="button" class="button-primary" value="<?php _e('Insert Shortcode', 'woocommerce-predictive-search' ); ?>" onclick="woo_search_widget_add_shortcode();"/>&nbsp;&nbsp;&nbsp;
            <a class="button" style="" href="#" onclick="tb_remove(); return false;"><?php _e('Cancel', 'woocommerce-predictive-search' ); ?></a>
			</p>
            <div style="clear:both;"></div>
		  </div>
          <div style="clear:both;"></div>
		</div>
<?php
	}
	
	public static function parse_shortcode_search_result($attributes) {
		// Don't show content for shortcode on Dashboard, still support for admin ajax
		if ( is_admin() && ( ! defined( 'DOING_AJAX' ) || ! DOING_AJAX ) ) return;

		if ( wcps_current_theme_is_fse_theme() ) {
			global $wc_ps_hook_backbone;
			$wc_ps_hook_backbone->include_result_shortcode_script();
		}

		$search_results = '';
		global $woocommerce_search_page_id;
		global $wp_query;

		$search_keyword = '';
		if (isset($wp_query->query_vars['keyword'])) $search_keyword = urldecode( sanitize_text_field( wp_unslash( $wp_query->query_vars['keyword'] ) ) );
		else if (isset($_REQUEST['rs']) && trim($_REQUEST['rs']) != '') $search_keyword = sanitize_text_field( wp_unslash( $_REQUEST['rs'] ) );

		$search_results .= self::display_search();
    	return $search_results;	
    }
						
	public static function display_search() {
		global $wp_query;
		global $wpdb;
		global $woocommerce_search_page_id;
	
		$items_search_default = Widgets::get_items_search();
		$search_keyword = '';
		$search_in = 'product';
		$search_other = '';
		$cat_in = 'all';
		
		if (isset($wp_query->query_vars['keyword'])) $search_keyword = urldecode( sanitize_text_field( wp_unslash( $wp_query->query_vars['keyword'] ) ) );
		else if (isset($_REQUEST['rs']) && trim($_REQUEST['rs']) != '') $search_keyword = sanitize_text_field( wp_unslash( $_REQUEST['rs'] ) );
		
		if (isset($wp_query->query_vars['cat-in'])) $cat_in = urldecode( sanitize_text_field( wp_unslash( $wp_query->query_vars['cat-in'] ) ) );
		else if (isset($_REQUEST['cat_in']) && trim($_REQUEST['cat_in']) != '') $cat_in = sanitize_text_field( wp_unslash( $_REQUEST['cat_in'] ) );
		
		if (isset($wp_query->query_vars['search-in'])) $search_in = urldecode( sanitize_text_field( wp_unslash( $wp_query->query_vars['search-in'] ) ) );
		else if (isset($_REQUEST['search_in']) && trim($_REQUEST['search_in']) != '') $search_in = sanitize_text_field( wp_unslash( $_REQUEST['search_in'] ) );
		
		if (isset($wp_query->query_vars['search-other'])) $search_other = urldecode( sanitize_text_field( wp_unslash( $wp_query->query_vars['search-other'] ) ) );
		else if (isset($_REQUEST['search_other']) && trim($_REQUEST['search_other']) != '') $search_other = sanitize_text_field( wp_unslash( $_REQUEST['search_other'] ) );
		
		$permalink_structure = get_option( 'permalink_structure' );

		if ( $search_keyword != '' && $search_in != '' ) {

			global $ps_search_list, $ps_current_search_in;

			ob_start();
		?>
		<div id="ps_results_container" class="woocommerce">
			<style type="text/css">
				.rs_result_heading{margin:15px 0;}
				.ajax-wait{display: none; position: absolute; width: 100%; height: 100%; top: 0px; left: 0px; background:url("<?php echo WOOPS_IMAGES_URL; ?>/ajax-loader.gif") no-repeat center center #EDEFF4; opacity: 1;text-align:center;}
				.ajax-wait img{margin-top:14px;}
				.p_data,.r_data,.q_data{display:none;}
				.rs_date{color:#777;font-size:small;}
				.rs_result_row{width:100%;float:left;margin:0px 0 10px;padding :0px 0 10px; 6px;border-bottom:1px solid #c2c2c2;}
				.rs_result_row:hover{opacity:1;}
				.rs_rs_avatar{width:64px;margin-right:10px;overflow: hidden;float:left; text-align:center;}
				.rs_rs_avatar img{width:100%;height:auto; padding:0 !important; margin:0 !important; border: none !important;}
				.rs_rs_name{margin-left:0px;}
				.rs_content{margin-left:74px;}
				.ps_more_result{display:none;width:240px;text-align:center;position:fixed;bottom:50%;left:50%;margin-left:-125px;background-color: black;opacity: .75;color: white;padding: 10px;border-radius:10px;-webkit-border-radius: 10px;-moz-border-radius: 10px}
				.rs_rs_price .oldprice{text-decoration:line-through; font-size:80%;}
				.rs_result_others { margin-bottom:20px; }
				.rs_result_others_heading {font-weight:bold;} 
				.ps_navigation_activated { font-weight:bold;}
			</style>
		
		<?php
			$tmp_args = array(
				'items_search_default'       => $items_search_default,
				'ps_search_list'             => $ps_search_list,
				'ps_current_search_in'       => $ps_current_search_in,
				'permalink_structure'        => $permalink_structure,
				'woocommerce_search_page_id' => $woocommerce_search_page_id,
				'search_keyword'             => $search_keyword,
				'cat_in'                     => $cat_in,
				'search_in'                  => $search_in,
				'search_other'               => $search_other,
			);
			wc_ps_get_results_header_tpl( $tmp_args );
		?>

        	<div id="ps_list_items_container">
            </div>
            <div style="clear:both"></div>
            <div class="ps_more_result" id="ps_more_result_popup">
                <img src="<?php echo WOOPS_IMAGES_URL; ?>/more-results-loader.gif" />
                <div><em><?php wc_ps_ict_t_e( 'Loading Text', __('Loading More Results...', 'woocommerce-predictive-search' ) ); ?></em></div>
            </div>
            <div class="ps_more_result" id="ps_no_more_result_popup"><em><?php wc_ps_ict_t_e( 'No More Result Text', __('No More Results to Show', 'woocommerce-predictive-search' ) ); ?></em></div>
            <div class="ps_more_result" id="ps_fetching_result_popup">
                <img src="<?php echo WOOPS_IMAGES_URL; ?>/more-results-loader.gif" />
                <div><em><?php wc_ps_ict_t_e( 'Fetching Text', __('Fetching search results...', 'woocommerce-predictive-search' ) ); ?></em></div>
            </div>
            <div class="ps_more_result" id="ps_no_result_popup"><em><?php wc_ps_ict_t_e( 'No Fetching Result Text', __('No Results to Show', 'woocommerce-predictive-search' ) ); ?></em></div>
            <div id="ps_footer_container">
            </div>
		</div>
        <script type="text/javascript">
		(function($) {
		$(function(){
			wc_ps_app.start();
		});
		})(jQuery);
		</script>
		<?php
			
			$output = ob_get_clean();
			
			return $output;
        }
	}	
}
