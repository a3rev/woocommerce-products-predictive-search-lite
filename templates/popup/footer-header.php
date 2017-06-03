<?php
/**
 * The Template for Predictive Search plugin
 *
 * Override this template by copying it to yourtheme/woocommerce/popup/footer-header.php
 *
 */
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
?>
<script type="text/template" id="wc_psearch_footerHeaderTpl"><div rel="more_result" class="more_result">
		<span><?php echo sprintf( wc_ps_ict_t__( 'More result Text - Header', $popup_seemore_text ), '{{= title }}' ); ?></span>
		{{ if ( description != null && description != '' ) { }}{{= description }}{{ } }}
</div></script>