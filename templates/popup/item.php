<?php
/**
 * The Template for Predictive Search plugin
 *
 * Override this template by copying it to yourtheme/woocommerce/popup/item.php
 *
 */
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
?>
<script type="text/template" id="wc_psearch_itemTpl"><div class="ajax_search_content">
	<div class="result_row">
		<span class="rs_avatar"><a href="{{= url }}"><img src="{{= image_url }}" /></a></span>
		<div class="rs_content_popup">
			<a href="{{= url }}">
				<span class="rs_name">{{= title }}</span>
				{{ if ( type == 'p_sku' ) { }}<span class="rs_sku"><?php wc_ps_ict_t_e( 'SKU', __('SKU', 'woocommerce-predictive-search' ) ); ?>: <strong>{{= sku }}</strong></span>{{ } }}
				{{ if ( price != null && price != '' ) { }}<span class="rs_price"><?php wc_ps_ict_t_e( 'Price', __('Price', 'woocommerce-predictive-search' ) ); ?>: {{= price }}</span>{{ } }}
				{{ if ( stock != null && stock != '' ) { }}<span class="rs_stock">{{= stock }}</span>{{ } }}
				{{ if ( description != null && description != '' ) { }}<span class="rs_description">{{= description }}</span>{{ } }}
			</a>
			{{ if ( categories.length > 0 ) { }}
				<span class="rs_cat posted_in">
					<?php wc_ps_ict_t_e( 'Category', __('Category', 'woocommerce-predictive-search' ) ); ?>: 
					{{ var number_cat = 0; }}
					{{ _.each( categories, function( cat_data ) { number_cat++; }}
						{{ if ( number_cat > 1 ) { }}, {{ } }}<a class="rs_cat_link" href="{{= cat_data.url }}">{{= cat_data.name }}</a>
					{{ }); }}
				</span>
			{{ } }}
		</div>
	</div>
</div></script>