<?php
/**
 * The Template for Predictive Search plugin
 *
 * Override this template by copying it to yourtheme/woocommerce/results-page/footer.php
 *
 */
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
?>
<script type="text/template" id="wc_psearch_result_footerTpl"><div style="clear:both"></div>
	{{ if ( next_page_number > 1 ) { }}
	<div id="ps_more_check"></div>
	{{ } else if ( total_items == 0 && first_load ) { }}
	<p style="text-align:center"><?php wc_ps_ict_t_e( 'No Result Text', __('Nothing Found! Please refine your search and try again.', 'woocommerce-predictive-search' ) ); ?></p>
	{{ } }}
</script>