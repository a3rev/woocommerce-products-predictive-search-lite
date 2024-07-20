<?php
/**
 * The Template for Predictive Search plugin
 *
 * Override this template by copying it to yourtheme/woocommerce/results-page/header.php
 *
 */
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
?>

<?php if ( ! empty( $ps_search_list ) && count( $ps_search_list ) > 0 ) { ?>
	<p class="rs_result_heading">
		<?php esc_html_e( wc_ps_ict_t__( 'Viewing all', __('Viewing all', 'woocommerce-predictive-search' ) ) ); ?> 
		<strong><span class="ps_heading_search_in_name"><?php echo esc_html( $items_search_default[$ps_current_search_in]['name'] ); ?></span></strong> 
		<?php esc_html_e( wc_ps_ict_t__( 'Search Result Text', __('search results for your search query', 'woocommerce-predictive-search' ) ) ); ?> 
		<strong><?php echo esc_html( $search_keyword ); ?></strong>
	</p>
<?php } ?>

<?php
if ( ! empty( $ps_search_list ) && count( $ps_search_list ) > 1 ) {
	$line_vertical = '';
?>
	<div class="rs_result_others">
		<div class="rs_result_others_heading"><?php esc_html_e( wc_ps_ict_t__( 'Sort Text', __('Sort Search Results by', 'woocommerce-predictive-search' ) ) ); ?></div>
<?php
	foreach ( $ps_search_list as $search_other_item ) {
		if ( ! isset( $items_search_default[$search_other_item] ) ) continue;

		if ( $permalink_structure == '' ) {
			$parameters = '&rs='. urlencode($search_keyword) .'&search_in='. $search_other_item .'&cat_in='. $cat_in .'&search_other='. $search_other;
			$other_link_search = get_permalink( $woocommerce_search_page_id ) . $parameters;
			$data_href = '?page_id='. $woocommerce_search_page_id . $parameters;
?>
		<?php echo esc_html( $line_vertical ); ?>
		<span class="rs_result_other_item">
			<a class="ps_navigation ps_navigation<?php echo esc_attr( $search_other_item ); ?>" href="<?php echo esc_url( $other_link_search ); ?>" data-href="<?php echo esc_attr( $data_href ); ?>" alt=""><?php echo esc_html( $items_search_default[$search_other_item]['name'] ); ?></a>
		</span>
<?php
		} else {
			$parameters = 'keyword/'. urlencode($search_keyword) .'/search-in/'. $search_other_item .'/cat-in/'. $cat_in .'/search-other/'. $search_other;
			$other_link_search = rtrim( get_permalink( $woocommerce_search_page_id ), '/' ) .'/'. $parameters;
?>
		<?php echo esc_html( $line_vertical ); ?>
		<span class="rs_result_other_item">
			<a class="ps_navigation ps_navigation<?php echo esc_attr( $search_other_item ); ?>" href="<?php echo esc_url( $other_link_search ); ?>" data-href="<?php echo esc_attr( $parameters ); ?>" alt=""><?php echo esc_html( $items_search_default[$search_other_item]['name'] ); ?></a>
		</span>
<?php
		}
		$line_vertical = ' | ';
	}
?>
	</div>
<?php
}
?>