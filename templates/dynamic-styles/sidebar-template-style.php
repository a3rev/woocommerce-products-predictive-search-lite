<?php

// Search Bar Sidebar Template
global $wc_predictive_search_sidebar_template_settings;
extract( $wc_predictive_search_sidebar_template_settings );

?>
@sidebar_container_wide : ~"calc( <?php echo $sidebar_search_box_wide; ?>% - <?php echo $sidebar_search_box_margin_right; ?>px - <?php echo $sidebar_search_box_margin_left; ?>px - <?php echo ( (int) $sidebar_search_box_border['width'] * 2 ); ?>px )";
@sidebar_container_mobile_wide : ~"calc( 100% - <?php echo $sidebar_search_box_mobile_margin_right; ?>px - <?php echo $sidebar_search_box_mobile_margin_left; ?>px - <?php echo ( (int) $sidebar_search_box_border['width'] * 2 ); ?>px )";
@sidebar_container_height: <?php echo $sidebar_search_box_height; ?>px;
@sidebar_container_margin: <?php echo $sidebar_search_box_margin_top; ?>px <?php echo $sidebar_search_box_margin_right; ?>px <?php echo $sidebar_search_box_margin_bottom; ?>px <?php echo $sidebar_search_box_margin_left; ?>px;
@sidebar_container_mobile_margin: <?php echo $sidebar_search_box_mobile_margin_top; ?>px <?php echo $sidebar_search_box_mobile_margin_right; ?>px <?php echo $sidebar_search_box_mobile_margin_bottom; ?>px <?php echo $sidebar_search_box_mobile_margin_left; ?>px;
@sidebar_container_border_focus_color: <?php echo $sidebar_search_box_border_color_focus; ?>;
.sidebar_container_align() {
<?php if ( 'center' === $sidebar_search_box_align ) { ?>
	margin: 0 auto;
<?php } else { ?>
	float: <?php echo $sidebar_search_box_align; ?>;
<?php } ?>
}
.sidebar_container_border() {
	<?php echo $GLOBALS[WOOPS_PREFIX.'admin_interface']->generate_border_css( $sidebar_search_box_border ); ?>
}
.sidebar_container_shadow() {
	<?php echo $GLOBALS[WOOPS_PREFIX.'admin_interface']->generate_shadow_css( $sidebar_search_box_shadow ); ?>
}

/* Sidebar Category Dropdown Variables */
@sidebar_cat_align: <?php echo $sidebar_category_dropdown_align; ?>;
@sidebar_cat_bg_color : <?php echo $sidebar_category_dropdown_bg_color; ?>;
@sidebar_cat_down_icon_size: <?php echo $sidebar_category_dropdown_icon_size; ?>px;
@sidebar_cat_down_icon_color: <?php echo $sidebar_category_dropdown_icon_color; ?>;
.sidebar_cat_label_font() {
	<?php echo $GLOBALS[WOOPS_PREFIX.'fonts_face']->generate_font_css( $sidebar_category_dropdown_font ); ?>
}
.sidebar_cat_side_border() {
<?php if ( 'left' === $sidebar_category_dropdown_align ) { ?>
	border-left: none;
	<?php echo str_replace( 'border:', 'border-right:', $GLOBALS[WOOPS_PREFIX.'admin_interface']->generate_border_style_css( $sidebar_category_dropdown_side_border ) ) ; ?>
<?php } else { ?>
	border-right: none;
	<?php echo str_replace( 'border:', 'border-left:', $GLOBALS[WOOPS_PREFIX.'admin_interface']->generate_border_style_css( $sidebar_category_dropdown_side_border ) ) ; ?>
<?php } ?>
}
.sidebar_cat_selector_dir() {
	<?php if ( 'right' === $sidebar_category_dropdown_align ) { ?>
	direction: rtl;
	<?php } ?>
}
.sidebar_cat_selector_option_dir() {
	<?php if ( 'right' === $sidebar_category_dropdown_align ) { ?>
	direction: ltr;
	text-align: left;
	<?php } ?>
}

/* Sidebar Search Icon Variables */
@sidebar_search_icon_size: <?php echo $sidebar_search_icon_size; ?>px;
@sidebar_search_icon_color: <?php echo $sidebar_search_icon_color; ?>;
@sidebar_search_icon_hover_color: <?php echo $sidebar_search_icon_hover_color; ?>;
@sidebar_search_icon_bg_color: <?php echo $sidebar_search_icon_bg_color; ?>;
@sidebar_search_icon_bg_hover_color: <?php echo $sidebar_search_icon_bg_hover_color; ?>;
.sidebar_search_icon_side_border() {
<?php if ( 'left' === $sidebar_category_dropdown_align ) { ?>
	border-right: none;
	<?php echo str_replace( 'border:', 'border-left:', $GLOBALS[WOOPS_PREFIX.'admin_interface']->generate_border_style_css( $sidebar_search_icon_side_border ) ) ; ?>
<?php } else { ?>
	border-left: none;
	<?php echo str_replace( 'border:', 'border-right:', $GLOBALS[WOOPS_PREFIX.'admin_interface']->generate_border_style_css( $sidebar_search_icon_side_border ) ) ; ?>
<?php } ?>
}

/* Sidebar Search Input Variables */
@sidebar_input_padding: <?php echo $sidebar_input_padding_tb; ?>px <?php echo $sidebar_input_padding_lr; ?>px !important;
.sidebar_input_bg_color {
	<?php echo $GLOBALS[WOOPS_PREFIX.'admin_interface']->generate_background_color_css( $sidebar_input_bg_color ); ?>
}
.sidebar_input_font() {
	<?php echo $GLOBALS[WOOPS_PREFIX.'fonts_face']->generate_font_css( $sidebar_input_font ); ?>
}
@sidebar_loading_icon_size: <?php echo $sidebar_loading_icon_size; ?>px;
@sidebar_loading_icon_color: <?php echo $sidebar_loading_icon_color; ?>;
@sidebar_loading_icon_top_postition: ~"calc( <?php echo $sidebar_search_box_height; ?>px/2 - <?php echo $sidebar_loading_icon_size; ?>px/2 )";

/* Sidebar Close Icon Variables */
@sidebar_close_icon_size: <?php echo $sidebar_close_icon_size; ?>px;
@sidebar_close_icon_color: <?php echo $sidebar_close_icon_color; ?>;
@sidebar_close_icon_margin: <?php echo $sidebar_close_icon_margin_top; ?>px <?php echo $sidebar_close_icon_margin_right; ?>px <?php echo $sidebar_close_icon_margin_bottom; ?>px <?php echo $sidebar_close_icon_margin_left; ?>px;

/* Click Icon to Show Search Box */
.sidebar_search_icon_mobile_align() {
<?php if ( 'center' === $search_icon_mobile_align ) { ?>
	margin: 0 auto;
<?php } else { ?>
	float: <?php echo $search_icon_mobile_align; ?>;
<?php } ?>
}
@sidebar_search_icon_mobile_size: <?php echo $search_icon_mobile_size; ?>px;
@sidebar_search_icon_mobile_color: <?php echo $search_icon_mobile_color; ?>;

/* Sidebar PopUp Variables */
.sidebar_popup_border() {
	<?php echo $GLOBALS[WOOPS_PREFIX.'admin_interface']->generate_border_css( $sidebar_popup_border ); ?>
}
@sidebar_popup_heading_padding: <?php echo $sidebar_popup_heading_padding_tb; ?>px <?php echo $sidebar_popup_heading_padding_lr; ?>px;
@sidebar_popup_heading_bg_color: <?php echo $sidebar_popup_heading_bg_color; ?>;
.sidebar_popup_heading_font() {
	<?php echo $GLOBALS[WOOPS_PREFIX.'fonts_face']->generate_font_css( $sidebar_popup_heading_font ); ?>
}
.sidebar_popup_heading_border() {
	<?php echo str_replace( 'border:', 'border-bottom:', $GLOBALS[WOOPS_PREFIX.'admin_interface']->generate_border_style_css( $sidebar_popup_heading_border ) ) ; ?>
}

@sidebar_popup_item_padding_tb: <?php echo $sidebar_popup_item_padding_tb; ?>px;
@sidebar_popup_item_padding_lr: <?php echo $sidebar_popup_item_padding_lr; ?>px;
@sidebar_popup_item_border_hover_color: <?php echo $sidebar_popup_item_border_hover_color; ?>;
@sidebar_popup_item_bg_color: <?php echo $sidebar_popup_item_bg_color; ?>;
@sidebar_popup_item_bg_hover_color: <?php echo $sidebar_popup_item_bg_hover_color; ?>;
.sidebar_popup_item_border() {
	<?php echo str_replace( 'border:', 'border-bottom:', $GLOBALS[WOOPS_PREFIX.'admin_interface']->generate_border_style_css( $sidebar_popup_item_border ) ) ; ?>
}

@sidebar_popup_img_size: <?php echo $sidebar_popup_item_image_size; ?>px;
@sidebar_popup_content_wide: ~"calc( 100% - <?php echo ( $sidebar_popup_item_image_size + 10 ); ?>px )";
@sidebar_popup_product_name_hover_color: <?php echo $sidebar_popup_product_name_hover_color; ?>;
.sidebar_popup_product_name_font() {
	<?php echo $GLOBALS[WOOPS_PREFIX.'fonts_face']->generate_font_css( $sidebar_popup_product_name_font ); ?>
}
@sidebar_popup_product_sku_hover_color: <?php echo $sidebar_popup_product_sku_hover_color; ?>;
.sidebar_popup_product_sku_font() {
	<?php echo $GLOBALS[WOOPS_PREFIX.'fonts_face']->generate_font_css( $sidebar_popup_product_sku_font ); ?>
}
@sidebar_popup_product_price_hover_color: <?php echo $sidebar_popup_product_price_hover_color; ?>;
.sidebar_popup_product_price_font() {
	<?php echo $GLOBALS[WOOPS_PREFIX.'fonts_face']->generate_font_css( $sidebar_popup_product_price_font ); ?>
}
@sidebar_popup_product_desc_hover_color: <?php echo $sidebar_popup_product_desc_hover_color; ?>;
.sidebar_popup_product_desc_font() {
	<?php echo $GLOBALS[WOOPS_PREFIX.'fonts_face']->generate_font_css( $sidebar_popup_product_desc_font ); ?>
}
@sidebar_popup_product_stock_qty_hover_color: <?php echo $sidebar_popup_product_stock_qty_hover_color; ?>;
.sidebar_popup_product_stock_qty_font() {
	<?php echo $GLOBALS[WOOPS_PREFIX.'fonts_face']->generate_font_css( $sidebar_popup_product_stock_qty_font ); ?>
}
@sidebar_popup_product_category_color: <?php echo $sidebar_popup_product_category_color; ?>;
@sidebar_popup_product_category_link_hover_color: <?php echo $sidebar_popup_product_category_link_hover_color; ?>;
@sidebar_popup_product_category_hover_color: <?php echo $sidebar_popup_product_category_hover_color; ?>;
.sidebar_popup_product_category_font() {
	<?php echo $GLOBALS[WOOPS_PREFIX.'fonts_face']->generate_font_css( $sidebar_popup_product_category_font ); ?>
}

@sidebar_popup_footer_padding: <?php echo $sidebar_popup_footer_padding_tb; ?>px <?php echo $sidebar_popup_footer_padding_lr; ?>px;
@sidebar_popup_footer_bg_color: <?php echo $sidebar_popup_footer_bg_color; ?>;
.sidebar_popup_seemore_font() {
	<?php echo $GLOBALS[WOOPS_PREFIX.'fonts_face']->generate_font_css( $sidebar_popup_seemore_font ); ?>
}
.sidebar_popup_more_link_font() {
	<?php echo $GLOBALS[WOOPS_PREFIX.'fonts_face']->generate_font_css( $sidebar_popup_more_link_font ); ?>
}
@sidebar_popup_more_icon_size: <?php echo $sidebar_popup_more_icon_size; ?>px;
@sidebar_popup_more_icon_color: <?php echo $sidebar_popup_more_icon_color; ?>;

<style>
/* Search Bar Sidebar Template */
.wc_ps_bar {
	.wc_ps_mobile_icon.sidebar_temp {
		.sidebar_search_icon_mobile_align();
		font-size: @sidebar_search_icon_mobile_size;
		color: @sidebar_search_icon_mobile_color;

		* {
			color: @sidebar_search_icon_mobile_color;
		}
	}
}

.wc_ps_sidebar_container {
	width: @sidebar_container_wide;
	margin: @sidebar_container_margin;
	.sidebar_container_align();
	.sidebar_container_border();
	.sidebar_container_shadow();

	&.wc_ps_container_active {
		border-color: @sidebar_container_border_focus_color !important;
	}

	/* Category Dropdown */
	.wc_ps_nav_scope {
		background-color: @sidebar_cat_bg_color;
		.sidebar_cat_side_border();

		.wc_ps_category_selector {
			.sidebar_cat_selector_dir();

			option {
				.sidebar_cat_selector_option_dir();
			}
		}

		.wc_ps_nav_facade_label {
			.sidebar_cat_label_font();
		}

		.wc_ps_nav_down_icon {
			font-size: @sidebar_cat_down_icon_size;
			color: @sidebar_cat_down_icon_color;

			* {
				color: @sidebar_cat_down_icon_color;
			}
		}
	}

	/* Search Icon */
	.wc_ps_nav_submit {
		background-color: @sidebar_search_icon_bg_color;
		.sidebar_search_icon_side_border();

		&:hover {
			background-color: @sidebar_search_icon_bg_hover_color;

			.wc_ps_nav_submit_icon,
			.wc_ps_nav_submit_icon * {
				color: @sidebar_search_icon_hover_color;
			}
		}

		.wc_ps_nav_submit_icon {
			font-size: @sidebar_search_icon_size;
			color: @sidebar_search_icon_color;

			* {
				color: @sidebar_search_icon_color;
			}
		}
	}

	/* Search Input */
	.wc_ps_nav_field {
		.sidebar_input_bg_color();

		.wc_ps_search_keyword {
			.sidebar_input_font();
			padding: @sidebar_input_padding;
		}

		.wc_ps_searching_icon {
			font-size: @sidebar_loading_icon_size;
			color: @sidebar_loading_icon_color;
		}

		svg.wc_ps_searching_icon {
			top: @sidebar_loading_icon_top_postition;

			* {
				color: @sidebar_loading_icon_color;
			}
		}
	}
}

.wc_ps_container.wc_ps_sidebar_container {

	.wc_ps_nav_left,
	.wc_ps_nav_right,
	.wc_ps_nav_fill,
	.wc_ps_nav_scope,
	.wc_ps_category_selector,
	.wc_ps_nav_submit,
	.wc_ps_nav_field,
	.wc_ps_search_keyword {
		height: @sidebar_container_height !important;
	}

	.wc_ps_nav_facade_label,
	.wc_ps_nav_down_icon,
	.wc_ps_category_selector,
	.wc_ps_nav_submit_icon,
	.wc_ps_searching_icon {
		line-height: @sidebar_container_height !important;
	}
}

/* Search Popup Sidebar Template */
.predictive_results.predictive_results_sidebar {
	.sidebar_popup_border();

	.ajax_search_content_title {
		padding: @sidebar_popup_heading_padding;
		background-color: @sidebar_popup_heading_bg_color;
		.sidebar_popup_heading_font();
		.sidebar_popup_heading_border();
	}

	.ajax_search_content {
		padding-left: @sidebar_popup_item_padding_lr;
		padding-right: @sidebar_popup_item_padding_lr;
		background-color: @sidebar_popup_item_bg_color;
		.sidebar_popup_item_border();
	}

	.result_row {
		margin-top: @sidebar_popup_item_padding_tb;
		margin-bottom: @sidebar_popup_item_padding_tb;
	}

	.rs_avatar {
		width: @sidebar_popup_img_size;
	}

	.rs_content_popup {
		width: @sidebar_popup_content_wide !important;

		.rs_name {
			.sidebar_popup_product_name_font();
		}

		.rs_sku {
			.sidebar_popup_product_sku_font();
		}

		.rs_price,
		.rs_price .woocommerce-Price-amount,
		.rs_price .woocommerce-Price-currencySymbol {
			.sidebar_popup_product_price_font();
		}

		.rs_description {
			.sidebar_popup_product_desc_font();
		}

		.rs_stock {
			.sidebar_popup_product_stock_qty_font();
		}

		.rs_cat, .rs_cat > a {
			.sidebar_popup_product_category_font();
		}

		.rs_cat {
			color: @sidebar_popup_product_category_color !important;
		}
	}

	.more_result {
		padding: @sidebar_popup_footer_padding;
		background-color: @sidebar_popup_footer_bg_color;

		span {
			.sidebar_popup_seemore_font();
		}

		a {
			.sidebar_popup_more_link_font();
		}

		.see_more_arrow {
			font-size: @sidebar_popup_more_icon_size !important;
			color: @sidebar_popup_more_icon_color !important;
		}
	}

	.ac_over {
		.ajax_search_content {
			background-color: @sidebar_popup_item_bg_hover_color;
			border-color: @sidebar_popup_item_border_hover_color !important;
		}

		.rs_name {
			color: @sidebar_popup_product_name_hover_color !important;
		}

		.rs_sku {
			color: @sidebar_popup_product_sku_hover_color !important;
		}

		.rs_price,
		.rs_price .woocommerce-Price-amount,
		.rs_price .woocommerce-Price-currencySymbol {
			color: @sidebar_popup_product_price_hover_color !important;
		}

		.rs_description {
			color: @sidebar_popup_product_desc_hover_color !important;
		}

		.rs_stock {
			color: @sidebar_popup_product_stock_qty_hover_color !important;
		}

		.rs_cat {
			color: @sidebar_popup_product_category_hover_color !important;
		}

		.rs_cat > a {
			color: @sidebar_popup_product_category_link_hover_color !important;
		}
	}

	.ps_close {
		font-size: @sidebar_close_icon_size;
		color: @sidebar_close_icon_color;
		margin: @sidebar_close_icon_margin;
	}
}

@media only screen and (max-width: 420px) {
	.wc_ps_sidebar_container {
		width: @sidebar_container_mobile_wide;
		margin: @sidebar_container_mobile_margin;
	}
}

</style>