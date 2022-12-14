<?php
/**
 * The Template for Predictive Search plugin
 *
 * Override this template by copying it to yourtheme/woocommerce/predictive-search-form-sidebar.php
 *
 */
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

global $woocommerce_search_page_id;

$search_results_page = str_replace( array( 'http:', 'https:' ), '', get_permalink( $woocommerce_search_page_id ) );
?>

<?php do_action( 'wc_ps_search_form_before' ); ?>

<div class="wc_ps_bar <?php echo ( 'yes' == $ps_args['search_icon_mobile'] ? 'search_icon_only' : '' ); ?>">

	<div class="wc_ps_mobile_icon sidebar_temp" data-ps-id="<?php echo $ps_id; ?>" aria-label="<?php _e( 'Open Search', 'woocommerce-predictive-search' ); ?>">
		<div style="display: inline-flex; justify-content: center; align-items: center;">
			<svg viewBox="0 0 24 24" height="25" width="25" fill="none" stroke="currentColor" xmlns="http://www.w3.org/2000/svg" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="display: inline-block; vertical-align: middle;"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
		</div>
	</div>
	<div style="clear:both;"></div>

	<div class="wc_ps_container wc_ps_sidebar_container <?php echo is_rtl() ? 'rtl' : ''; ?>" id="wc_ps_container_<?php echo $ps_id; ?>">
		<form
			class="wc_ps_form"
			id="wc_ps_form_<?php echo $ps_id; ?>"
			autocomplete="off"
			action="<?php echo esc_url( $search_results_page ); ?>"
			method="get"

			data-ps-id="<?php echo $ps_id; ?>"
			data-ps-cat_align="<?php echo $ps_args['cat_align']; ?>"
			data-ps-cat_max_wide="<?php echo $ps_args['cat_max_wide']; ?>"
			data-ps-popup_wide="<?php echo $ps_args['popup_wide']; ?>"
			data-ps-widget_template="<?php echo $ps_widget_template; ?>"
		>

			<input type="hidden" class="wc_ps_category_selector" name="cat_in" value="" >

			<div class="wc_ps_nav_right">
				<div class="wc_ps_nav_submit">
					<div class="wc_ps_nav_submit_icon">
						<svg viewBox="0 0 24 24" height="16" width="16" fill="none" stroke="currentColor" xmlns="http://www.w3.org/2000/svg" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="display: inline-block; vertical-align: middle;"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
					</div>
					<input data-ps-id="<?php echo $ps_id; ?>" class="wc_ps_nav_submit_bt" type="button" value="<?php echo __( 'Go', 'woocommerce-predictive-search' ); ?>">
				</div>
			</div>

			<div class="wc_ps_nav_fill">
				<div class="wc_ps_nav_field">
					<input type="text" name="rs" class="wc_ps_search_keyword" id="wc_ps_search_keyword_<?php echo $ps_id; ?>"
						aria-label="<?php _e( 'Keyword Search', 'woocommerce-predictive-search' ); ?>"
						onblur="if( this.value == '' ){ this.value = '<?php echo esc_js( $ps_args['search_box_text'] ); ?>'; }"
						onfocus="if( this.value == '<?php echo esc_js( $ps_args['search_box_text'] ); ?>' ){ this.value = ''; }"
						value="<?php echo esc_attr( $ps_args['search_box_text'] ); ?>"
						data-ps-id="<?php echo $ps_id; ?>"
						data-ps-default_text="<?php echo esc_attr( $ps_args['search_box_text'] ); ?>"
						data-ps-row="<?php echo esc_attr( $ps_args['row'] ); ?>"
						data-ps-text_lenght="<?php echo esc_attr( $ps_args['text_lenght'] ); ?>"

						<?php if ( class_exists('SitePress') ) { ?>
						data-ps-lang="<?php echo ICL_LANGUAGE_CODE; ?>"
						<?php } ?>

						<?php if ( $ps_args['search_in'] != '' ) { ?>
						data-ps-popup_search_in="<?php echo esc_attr( $ps_args['search_in'] ); ?>"
						<?php } ?>

						<?php if ( count( $ps_args['search_list'] ) > 0 ) { ?>
						data-ps-search_in="<?php echo esc_attr( $ps_args['search_list'][0] ); ?>"
						data-ps-search_other="<?php echo esc_attr( implode( ',', $ps_args['search_list'] ) ); ?>"
						<?php } ?>

						data-ps-show_price="<?php echo $ps_args['show_price']; ?>"
						data-ps-show_in_cat="<?php echo $ps_args['show_in_cat']; ?>"
					/>
					<svg aria-hidden="true" viewBox="0 0 512 512" class="wc_ps_searching_icon" style="display: none;" aria-label="<?php _e( 'Searching', 'woocommerce-predictive-search' ); ?>">
						<path d="M288 39.056v16.659c0 10.804 7.281 20.159 17.686 23.066C383.204 100.434 440 171.518 440 256c0 101.689-82.295 184-184 184-101.689 0-184-82.295-184-184 0-84.47 56.786-155.564 134.312-177.219C216.719 75.874 224 66.517 224 55.712V39.064c0-15.709-14.834-27.153-30.046-23.234C86.603 43.482 7.394 141.206 8.003 257.332c.72 137.052 111.477 246.956 248.531 246.667C393.255 503.711 504 392.788 504 256c0-115.633-79.14-212.779-186.211-240.236C302.678 11.889 288 23.456 288 39.056z"></path>
					</svg>
				</div>
			</div>

		<?php if ( '' == get_option('permalink_structure') ) { ?>
			<input type="hidden" name="page_id" value="<?php echo esc_attr( $woocommerce_search_page_id ); ?>"  />

			<?php if ( class_exists('SitePress') ) { ?>
				<input type="hidden" name="lang" value="<?php echo ICL_LANGUAGE_CODE; ?>"  />
			<?php } ?>

		<?php } ?>

		<?php if ( count( $ps_args['search_list'] ) > 0 ) { ?>
			<input type="hidden" name="search_in" value="<?php echo esc_attr( $ps_args['search_list'][0] ); ?>"  />
			<input type="hidden" name="search_other" value="<?php echo esc_attr( implode( ',', $ps_args['search_list'] ) ); ?>"  />
		<?php } ?>

			<?php do_action( 'wc_ps_search_form_inside' ); ?>
		</form>
	</div>
	<div style="clear:both;"></div>

</div>

<?php do_action( 'wc_ps_search_form_after' ); ?>

