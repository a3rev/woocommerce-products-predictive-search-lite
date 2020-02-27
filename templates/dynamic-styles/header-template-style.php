<?php
global ${WOOPS_PREFIX.'admin_interface'}, ${WOOPS_PREFIX.'fonts_face'};

// Search Bar Header Template
global $wc_predictive_search_header_template_settings;
extract( $wc_predictive_search_header_template_settings );

?>
@header_container_wide : ~"calc( <?php echo $header_search_box_wide; ?>% - <?php echo $header_search_box_margin_right; ?>px - <?php echo $header_search_box_margin_left; ?>px - <?php echo ( (int) $header_search_box_border['width'] * 2 ); ?>px )";
@header_container_mobile_wide : ~"calc( 100% - <?php echo $header_search_box_mobile_margin_right; ?>px - <?php echo $header_search_box_mobile_margin_left; ?>px - <?php echo ( (int) $header_search_box_border['width'] * 2 ); ?>px )";
@header_container_height: <?php echo $header_search_box_height; ?>px;
@header_container_margin: <?php echo $header_search_box_margin_top; ?>px <?php echo $header_search_box_margin_right; ?>px <?php echo $header_search_box_margin_bottom; ?>px <?php echo $header_search_box_margin_left; ?>px;
@header_container_mobile_margin: <?php echo $header_search_box_mobile_margin_top; ?>px <?php echo $header_search_box_mobile_margin_right; ?>px <?php echo $header_search_box_mobile_margin_bottom; ?>px <?php echo $header_search_box_mobile_margin_left; ?>px;
@header_container_border_focus_color: <?php echo $header_search_box_border_color_focus; ?>;
.header_container_align() {
<?php if ( 'center' === $header_search_box_align ) { ?>
	margin: 0 auto;
<?php } else { ?>
	float: <?php echo $header_search_box_align; ?>;
<?php } ?>
}
.header_container_border() {
	<?php echo ${WOOPS_PREFIX.'admin_interface'}->generate_border_css( $header_search_box_border ); ?>
}
.header_container_shadow() {
	<?php echo ${WOOPS_PREFIX.'admin_interface'}->generate_shadow_css( $header_search_box_shadow ); ?>
}

/* Header Category Dropdown Variables */
@header_cat_align: <?php echo $header_category_dropdown_align; ?>;
@header_cat_bg_color : <?php echo $header_category_dropdown_bg_color; ?>;
@header_cat_down_icon_size: <?php echo $header_category_dropdown_icon_size; ?>px;
@header_cat_down_icon_color: <?php echo $header_category_dropdown_icon_color; ?>;
.header_cat_label_font() {
	<?php echo ${WOOPS_PREFIX.'fonts_face'}->generate_font_css( $header_category_dropdown_font ); ?>
}
.header_cat_side_border() {
<?php if ( 'left' === $header_category_dropdown_align ) { ?>
	border-left: none;
	<?php echo str_replace( 'border:', 'border-right:', ${WOOPS_PREFIX.'admin_interface'}->generate_border_style_css( $header_category_dropdown_side_border ) ) ; ?>
<?php } else { ?>
	border-right: none;
	<?php echo str_replace( 'border:', 'border-left:', ${WOOPS_PREFIX.'admin_interface'}->generate_border_style_css( $header_category_dropdown_side_border ) ) ; ?>
<?php } ?>
}
.header_cat_selector_dir() {
	<?php if ( 'right' === $header_category_dropdown_align ) { ?>
	direction: rtl;
	<?php } ?>
}
.header_cat_selector_option_dir() {
	<?php if ( 'right' === $header_category_dropdown_align ) { ?>
	direction: ltr;
	text-align: left;
	<?php } ?>
}

/* Header Search Icon Variables */
@header_search_icon_size: <?php echo $header_search_icon_size; ?>px;
@header_search_icon_color: <?php echo $header_search_icon_color; ?>;
@header_search_icon_hover_color: <?php echo $header_search_icon_hover_color; ?>;
@header_search_icon_bg_color: <?php echo $header_search_icon_bg_color; ?>;
@header_search_icon_bg_hover_color: <?php echo $header_search_icon_bg_hover_color; ?>;
.header_search_icon_side_border() {
<?php if ( 'left' === $header_category_dropdown_align ) { ?>
	border-right: none;
	<?php echo str_replace( 'border:', 'border-left:', ${WOOPS_PREFIX.'admin_interface'}->generate_border_style_css( $header_search_icon_side_border ) ) ; ?>
<?php } else { ?>
	border-left: none;
	<?php echo str_replace( 'border:', 'border-right:', ${WOOPS_PREFIX.'admin_interface'}->generate_border_style_css( $header_search_icon_side_border ) ) ; ?>
<?php } ?>
}

/* Header Search Input Variables */
@header_input_padding: <?php echo $header_input_padding_tb; ?>px <?php echo $header_input_padding_lr; ?>px !important;
.header_input_bg_color {
	<?php echo ${WOOPS_PREFIX.'admin_interface'}->generate_background_color_css( $header_input_bg_color ); ?>
}
.header_input_font() {
	<?php echo ${WOOPS_PREFIX.'fonts_face'}->generate_font_css( $header_input_font ); ?>
}
@header_loading_icon_size: <?php echo $header_loading_icon_size; ?>px;
@header_loading_icon_color: <?php echo $header_loading_icon_color; ?>;
@header_loading_icon_top_postition: ~"calc( <?php echo $header_search_box_height; ?>px/2 - <?php echo $header_loading_icon_size; ?>px/2 )";

/* Header Close Icon Variables */
@header_close_icon_size: <?php echo $header_close_icon_size; ?>px;
@header_close_icon_color: <?php echo $header_close_icon_color; ?>;
@header_close_icon_margin: <?php echo $header_close_icon_margin_top; ?>px <?php echo $header_close_icon_margin_right; ?>px <?php echo $header_close_icon_margin_bottom; ?>px <?php echo $header_close_icon_margin_left; ?>px;

/* Click Icon to Show Search Box */
.header_search_icon_mobile_align() {
<?php if ( 'center' === $search_icon_mobile_align ) { ?>
	margin: 0 auto;
<?php } else { ?>
	float: <?php echo $search_icon_mobile_align; ?>;
<?php } ?>
}
@header_search_icon_mobile_size: <?php echo $search_icon_mobile_size; ?>px;
@header_search_icon_mobile_color: <?php echo $search_icon_mobile_color; ?>;

/* Header PopUp Variables */
.header_popup_border() {
	<?php echo ${WOOPS_PREFIX.'admin_interface'}->generate_border_css( $header_popup_border ); ?>
}
@header_popup_heading_padding: <?php echo $header_popup_heading_padding_tb; ?>px <?php echo $header_popup_heading_padding_lr; ?>px;
@header_popup_heading_bg_color: <?php echo $header_popup_heading_bg_color; ?>;
.header_popup_heading_font() {
	<?php echo ${WOOPS_PREFIX.'fonts_face'}->generate_font_css( $header_popup_heading_font ); ?>
}
.header_popup_heading_border() {
	<?php echo str_replace( 'border:', 'border-bottom:', ${WOOPS_PREFIX.'admin_interface'}->generate_border_style_css( $header_popup_heading_border ) ) ; ?>
}

@header_popup_item_padding_tb: <?php echo $header_popup_item_padding_tb; ?>px;
@header_popup_item_padding_lr: <?php echo $header_popup_item_padding_lr; ?>px;
@header_popup_item_border_hover_color: <?php echo $header_popup_item_border_hover_color; ?>;
@header_popup_item_bg_color: <?php echo $header_popup_item_bg_color; ?>;
@header_popup_item_bg_hover_color: <?php echo $header_popup_item_bg_hover_color; ?>;
.header_popup_item_border() {
	<?php echo str_replace( 'border:', 'border-bottom:', ${WOOPS_PREFIX.'admin_interface'}->generate_border_style_css( $header_popup_item_border ) ) ; ?>
}

@header_popup_img_size: <?php echo $header_popup_item_image_size; ?>px;
@header_popup_content_wide: ~"calc( 100% - <?php echo ( $header_popup_item_image_size + 10 ); ?>px )";
@header_popup_product_name_hover_color: <?php echo $header_popup_product_name_hover_color; ?>;
.header_popup_product_name_font() {
	<?php echo ${WOOPS_PREFIX.'fonts_face'}->generate_font_css( $header_popup_product_name_font ); ?>
}
@header_popup_product_sku_hover_color: <?php echo $header_popup_product_sku_hover_color; ?>;
.header_popup_product_sku_font() {
	<?php echo ${WOOPS_PREFIX.'fonts_face'}->generate_font_css( $header_popup_product_sku_font ); ?>
}
@header_popup_product_price_hover_color: <?php echo $header_popup_product_price_hover_color; ?>;
.header_popup_product_price_font() {
	<?php echo ${WOOPS_PREFIX.'fonts_face'}->generate_font_css( $header_popup_product_price_font ); ?>
}
@header_popup_product_desc_hover_color: <?php echo $header_popup_product_desc_hover_color; ?>;
.header_popup_product_desc_font() {
	<?php echo ${WOOPS_PREFIX.'fonts_face'}->generate_font_css( $header_popup_product_desc_font ); ?>
}
@header_popup_product_stock_qty_hover_color: <?php echo $header_popup_product_stock_qty_hover_color; ?>;
.header_popup_product_stock_qty_font() {
	<?php echo ${WOOPS_PREFIX.'fonts_face'}->generate_font_css( $header_popup_product_stock_qty_font ); ?>
}
@header_popup_product_category_color: <?php echo $header_popup_product_category_color; ?>;
@header_popup_product_category_link_hover_color: <?php echo $header_popup_product_category_link_hover_color; ?>;
@header_popup_product_category_hover_color: <?php echo $header_popup_product_category_hover_color; ?>;
.header_popup_product_category_font() {
	<?php echo ${WOOPS_PREFIX.'fonts_face'}->generate_font_css( $header_popup_product_category_font ); ?>
}

@header_popup_footer_padding: <?php echo $header_popup_footer_padding_tb; ?>px <?php echo $header_popup_footer_padding_lr; ?>px;
@header_popup_footer_bg_color: <?php echo $header_popup_footer_bg_color; ?>;
.header_popup_seemore_font() {
	<?php echo ${WOOPS_PREFIX.'fonts_face'}->generate_font_css( $header_popup_seemore_font ); ?>
}
.header_popup_more_link_font() {
	<?php echo ${WOOPS_PREFIX.'fonts_face'}->generate_font_css( $header_popup_more_link_font ); ?>
}
@header_popup_more_icon_size: <?php echo $header_popup_more_icon_size; ?>px;
@header_popup_more_icon_color: <?php echo $header_popup_more_icon_color; ?>;

<style>
/* Search Bar Header Template */
.wc_ps_bar {
	.wc_ps_mobile_icon.header_temp {
		.header_search_icon_mobile_align();
		font-size: @header_search_icon_mobile_size;
		color: @header_search_icon_mobile_color;

		* {
			color: @header_search_icon_mobile_color;
		}
	}
}

.wc_ps_header_container {
	width: @header_container_wide;
	margin: @header_container_margin;
	.header_container_align();
	.header_container_border();
	.header_container_shadow();

	&.wc_ps_container_active {
		border-color: @header_container_border_focus_color !important;
	}

	/* Category Dropdown */
	.wc_ps_nav_scope {
		background-color: @header_cat_bg_color;
		.header_cat_side_border();

		.wc_ps_category_selector {
			.header_cat_selector_dir();

			option {
				.header_cat_selector_option_dir();
			}
		}

		.wc_ps_nav_facade_label {
			.header_cat_label_font();
		}

		.wc_ps_nav_down_icon {
			font-size: @header_cat_down_icon_size;
			color: @header_cat_down_icon_color;

			* {
				color: @header_cat_down_icon_color;
			}
		}
	}

	/* Search Icon */
	.wc_ps_nav_submit {
		background-color: @header_search_icon_bg_color;
		.header_search_icon_side_border();

		&:hover {
			background-color: @header_search_icon_bg_hover_color;

			.wc_ps_nav_submit_icon,
			.wc_ps_nav_submit_icon * {
				color: @header_search_icon_hover_color;
			}
		}

		.wc_ps_nav_submit_icon {
			font-size: @header_search_icon_size;
			color: @header_search_icon_color;

			* {
				color: @header_search_icon_color;
			}
		}
	}

	/* Search Input */
	.wc_ps_nav_field {
		.header_input_bg_color();

		.wc_ps_search_keyword {
			.header_input_font();
			padding: @header_input_padding;
		}

		.wc_ps_searching_icon {
			font-size: @header_loading_icon_size;
			color: @header_loading_icon_color;
		}

		svg.wc_ps_searching_icon {
			top: @header_loading_icon_top_postition;

			* {
				color: @header_loading_icon_color;
			}
		}
	}
}

.wc_ps_container.wc_ps_header_container {

	.wc_ps_nav_left,
	.wc_ps_nav_right,
	.wc_ps_nav_fill,
	.wc_ps_nav_scope,
	.wc_ps_category_selector,
	.wc_ps_nav_submit,
	.wc_ps_nav_field,
	.wc_ps_search_keyword {
		height: @header_container_height !important;
	}

	.wc_ps_nav_facade_label,
	.wc_ps_nav_down_icon,
	.wc_ps_category_selector,
	.wc_ps_nav_submit_icon,
	.wc_ps_searching_icon {
		line-height: @header_container_height !important;
	}
}

/* Search Popup Header Template */
.predictive_results.predictive_results_header {
	.header_popup_border();

	.ajax_search_content_title {
		padding: @header_popup_heading_padding;
		background-color: @header_popup_heading_bg_color;
		.header_popup_heading_font();
		.header_popup_heading_border();
	}

	.ajax_search_content {
		padding-left: @header_popup_item_padding_lr;
		padding-right: @header_popup_item_padding_lr;
		background-color: @header_popup_item_bg_color;
		.header_popup_item_border();
	}

	.result_row {
		margin-top: @header_popup_item_padding_tb;
		margin-bottom: @header_popup_item_padding_tb;
	}

	.rs_avatar {
		width: @header_popup_img_size;
	}

	.rs_content_popup {
		width: @header_popup_content_wide !important;

		.rs_name {
			.header_popup_product_name_font();
		}

		.rs_sku {
			.header_popup_product_sku_font();
		}

		.rs_price {
			.header_popup_product_price_font();
		}

		.rs_description {
			.header_popup_product_desc_font();
		}

		.rs_stock {
			.header_popup_product_stock_qty_font();
		}

		.rs_cat, .rs_cat > a {
			.header_popup_product_category_font();
		}

		.rs_cat {
			color: @header_popup_product_category_color !important;
		}
	}

	.more_result {
		padding: @header_popup_footer_padding;
		background-color: @header_popup_footer_bg_color;

		span {
			.header_popup_seemore_font();
		}

		a {
			.header_popup_more_link_font();
		}

		.see_more_arrow {
			font-size: @header_popup_more_icon_size !important;
			color: @header_popup_more_icon_color !important;
		}
	}

	.ac_over {
		.ajax_search_content {
			background-color: @header_popup_item_bg_hover_color;
			border-color: @header_popup_item_border_hover_color !important;
		}

		.rs_name {
			color: @header_popup_product_name_hover_color !important;
		}

		.rs_sku {
			color: @header_popup_product_sku_hover_color !important;
		}

		.rs_price {
			color: @header_popup_product_price_hover_color !important;
		}

		.rs_description {
			color: @header_popup_product_desc_hover_color !important;
		}

		.rs_stock {
			color: @header_popup_product_stock_qty_hover_color !important;
		}

		.rs_cat {
			color: @header_popup_product_category_hover_color !important;
		}

		.rs_cat > a {
			color: @header_popup_product_category_link_hover_color !important;
		}
	}

	.ps_close {
		font-size: @header_close_icon_size;
		color: @header_close_icon_color;
		margin: @header_close_icon_margin;
	}
}

@media only screen and (max-width: 420px) {
	.wc_ps_header_container {
		width: @header_container_mobile_wide;
		margin: @header_container_mobile_margin;
	}
}

</style>