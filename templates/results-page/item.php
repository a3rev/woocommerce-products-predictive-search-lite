<?php
/**
 * The Template for Predictive Search plugin
 *
 * Override this template by copying it to yourtheme/woocommerce/results-page/item.php
 *
 */
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
?>
<script type="text/template" id="wc_psearch_result_itemTpl">{{ if ( image_url != null && image_url != '' ) { }}<span class="rs_rs_avatar"><a href="{{= url }}" aria-label="{{= title }}"><img src="{{= image_url }}" /></a></span>{{ } }}
	<div class="rs_content {{ if ( image_url == null || image_url == '' ) { }}no_image{{ } }}">
		<a href="{{= url }}"><span class="rs_rs_name">{{= title }}</span></a>
		{{ if ( sku != null && sku != '' ) { }}<div class="rs_rs_sku"><?php echo esc_js( wc_ps_ict_t__( 'SKU', __('SKU', 'woocommerce-predictive-search' ) ) ); ?>: {{= sku }}</div>{{ } }}
		{{ if ( price != null && price != '' ) { }}<div class="rs_rs_price"><?php echo esc_js( wc_ps_ict_t__( 'Price', __('Price', 'woocommerce-predictive-search' ) ) ); ?>: {{= price }}</div>{{ } }}
		{{ if ( stock != null && stock != '' ) { }}<span class="rs_rs_stock">{{= stock }}</span>{{ } }}
		{{ if ( addtocart != null && addtocart != '' ) { }}<div class="rs_rs_addtocart">{{= addtocart }}</div>{{ } }}
		{{ if ( description != null && description != '' ) { }}<div class="rs_rs_description">{{= description }}</div>{{ } }}
		{{ if ( categories.length > 0 ) { }}
			<div class="rs_rs_cat posted_in">
				<?php echo esc_js( wc_ps_ict_t__( 'Category', __('Category', 'woocommerce-predictive-search' ) ) ); ?>:
				{{ var number_cat = 0; }}
				{{ _.each( categories, function( cat_data ) { number_cat++; }}
					{{ if ( number_cat > 1 ) { }}, {{ } }}<a href="{{= cat_data.url }}">{{= cat_data.name }}</a>
				{{ }); }}
			</div>
		{{ } }}
		{{ if ( tags.length > 0 ) { }}
			<div class="rs_rs_tag tagged_as">
				<?php echo esc_js( wc_ps_ict_t__( 'Tags', __('Tags', 'woocommerce-predictive-search' ) ) ); ?>:
				{{ var number_tag = 0; }}
				{{ _.each( tags, function( tag_data ) { number_tag++; }}
					{{ if ( number_tag > 1 ) { }}, {{ } }}<a href="{{= tag_data.url }}">{{= tag_data.name }}</a>
				{{ }); }}
			</div>
		{{ } }}
	</div>
</script>