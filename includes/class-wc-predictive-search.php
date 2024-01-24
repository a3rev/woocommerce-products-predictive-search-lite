<?php
/**
 * WooCommerce Predictive Search
 *
 * Class Function into woocommerce plugin
 *
 * Table Of Contents
 *
 * install_databases()
 * set_tables_wpdbfix()
 */

namespace A3Rev\WCPredictiveSearch;

class Main
{

	public function __construct() {
		// Set Predictive Search Tables
		add_action( 'plugins_loaded', array( $this, 'set_tables_wpdbfix' ), 0 );
		add_action( 'switch_blog', array( $this, 'set_tables_wpdbfix' ), 0 );
	}

	public function install_databases() {
		global $wc_ps_postmeta_data;
		global $wc_ps_posts_data;
		global $wc_ps_product_sku_data;
		global $wc_ps_exclude_data;

		$wc_ps_posts_data->install_database();
		$wc_ps_postmeta_data->install_database();
		$wc_ps_product_sku_data->install_database();
		$wc_ps_exclude_data->install_database();
	}

	public function set_tables_wpdbfix() {
		global $wc_ps_postmeta_data;
		global $wc_ps_posts_data;
		global $wc_ps_product_sku_data;
		global $wc_ps_exclude_data;

		$wc_ps_posts_data->set_table_wpdbfix();
		$wc_ps_postmeta_data->set_table_wpdbfix();
		$wc_ps_product_sku_data->set_table_wpdbfix();
		$wc_ps_exclude_data->set_table_wpdbfix();
	}

	public function general_sql( $main_sql ) {

		$select_sql = '';
		if ( is_array( $main_sql['select'] ) && count( $main_sql['select'] ) > 0 ) {
			$select_sql = implode( ', ', $main_sql['select'] );
		} elseif ( ! is_array( $main_sql['select'] ) ) {
			$select_sql = $main_sql['select'];
		}

		$from_sql = '';
		if ( is_array( $main_sql['from'] ) && count( $main_sql['from'] ) > 0 ) {
			$from_sql = implode( ', ', $main_sql['from'] );
		} elseif ( ! is_array( $main_sql['from'] ) ) {
			$from_sql = $main_sql['from'];
		}

		$join_sql = '';
		if ( is_array( $main_sql['join'] ) && count( $main_sql['join'] ) > 0 ) {
			$join_sql = implode( ' ', $main_sql['join'] );
		} elseif ( ! is_array( $main_sql['join'] ) ) {
			$join_sql = $main_sql['join'];
		}

		$where_sql = '';
		$where_search_sql = '';
		if ( is_array( $main_sql['where'] ) && count( $main_sql['where'] ) > 0 ) {
			if ( isset( $main_sql['where']['search'] ) ) {
				$where_search = $main_sql['where']['search'];
				unset( $main_sql['where']['search'] );
				if ( is_array( $where_search ) && count( $where_search ) > 0 ) {
					$where_search_sql = implode( ' ', $where_search );
				} elseif ( ! is_array( $where_search ) ) {
					$where_search_sql = $where_search;
				}
			}
			$where_sql = implode( ' ', $main_sql['where'] );
		} elseif ( ! is_array( $main_sql['where'] ) ) {
			$where_sql = $main_sql['where'];
		}

		$groupby_sql = '';
		if ( is_array( $main_sql['groupby'] ) && count( $main_sql['groupby'] ) > 0 ) {
			$groupby_sql = implode( ', ', $main_sql['groupby'] );
		} elseif ( ! is_array( $main_sql['groupby'] ) ) {
			$groupby_sql = $main_sql['groupby'];
		}

		$orderby_sql = '';
		if ( is_array( $main_sql['orderby'] ) && count( $main_sql['orderby'] ) > 0 ) {
			$orderby_sql = implode( ', ', $main_sql['orderby'] );
		} elseif ( ! is_array( $main_sql['orderby'] ) ) {
			$orderby_sql = $main_sql['orderby'];
		}

		$limit_sql = $main_sql['limit'];

		$sql = 'SELECT ';
		if ( '' != trim( $select_sql ) ) {
			$sql .= $select_sql;
		}

		$sql .= ' FROM ';
		if ( '' != trim( $from_sql ) ) {
			$sql .= $from_sql . ' ';
		}

		if ( '' != trim( $join_sql ) ) {
			$sql .= $join_sql . ' ';
		}

		if ( '' != trim( $where_sql ) || '' != trim( $where_search_sql ) ) {
			$sql .= ' WHERE ';
			$sql .= $where_sql . ' ';

			if ( '' != trim( $where_search_sql ) ) {
				if ( '' != trim( $where_sql ) ) {
					$sql .= ' AND ( ' . $where_search_sql . ' ) ';
				} else {
					$sql .= $where_search_sql;
				}
			}
		}

		if ( '' != trim( $groupby_sql ) ) {
			$sql .= ' GROUP BY ';
			$sql .= $groupby_sql . ' ';
		}

		if ( '' != trim( $orderby_sql ) ) {
			$sql .= ' ORDER BY ';
			$sql .= $orderby_sql . ' ';
		}

		if ( '' != trim( $limit_sql ) ) {
			$sql .= ' LIMIT ';
			$sql .= $limit_sql . ' ';
		}

		return $sql;
	}

	public function get_product_search_sql( $search_keyword, $row, $start = 0, $woocommerce_search_focus_enable = '', $woocommerce_search_focus_plugin = '', $post_type = 'product', $term_id = 0, $current_lang = '', $check_exsited = false ) {
		global $wpdb;

		$row += 1;

		$search_keyword_nospecial = preg_replace( "/[^a-zA-Z0-9_.\s]/", " ", $search_keyword );
		if ( $search_keyword == $search_keyword_nospecial ) {
			$search_keyword_nospecial = '';
		} else {
			$search_keyword_nospecial = $wpdb->esc_like( trim( $search_keyword_nospecial ) );
		}

		$search_keyword	= $wpdb->esc_like( trim( $search_keyword ) );

		$main_sql               = array();
		$wpml_sql               = array();

		global $wc_ps_posts_data;
		$main_sql = $wc_ps_posts_data->get_sql( $search_keyword, $search_keyword_nospecial, $post_type, $row, $start, $check_exsited );

		if ( class_exists('SitePress') && '' != $current_lang ) {
			$wpml_sql['join']    = " INNER JOIN ".$wpdb->prefix."icl_translations AS ic ON (ic.element_id = pp.post_id) ";
			$wpml_sql['where'][] = " AND ic.language_code = '".$current_lang."' AND ic.element_type = 'post_{$post_type}' ";
		}

		$main_sql = array_merge_recursive( $main_sql, $wpml_sql );

		$sql = $this->general_sql( $main_sql );

		return $sql;
	}

	/**
	 * Check product is exsited from search term
	 */
	public function check_product_exsited( $search_keyword, $woocommerce_search_focus_enable, $woocommerce_search_focus_plugin, $post_type = 'product', $term_id = 0, $current_lang = '' ) {
		global $wpdb;

		$sql = $this->get_product_search_sql( $search_keyword, 1, 0, $woocommerce_search_focus_enable, $woocommerce_search_focus_plugin, $post_type, $term_id, $current_lang, true );

		$sql = "SELECT EXISTS( " . $sql . ")";

		$have_item = $wpdb->get_var( $sql );
		if ( $have_item == '1' ) {
			return true;
		} else {
			return false;
		}

	}

	/**
	 * Get array product list
	 */
	public function get_product_results( $search_keyword, $row, $start = 0, $woocommerce_search_focus_enable = '', $woocommerce_search_focus_plugin = '', $product_term_id = 0, $text_lenght = 100, $current_lang = '', $include_header = true , $show_price = true, $show_sku = false, $show_addtocart = false, $show_categories = false, $show_tags = false, $in_results_page = false ) {
		global $wpdb;

		$results_display_type = get_option( 'woocommerce_search_result_display_type', 'grid' );

		$have_product = $this->check_product_exsited( $search_keyword, $woocommerce_search_focus_enable, $woocommerce_search_focus_plugin, 'product', $product_term_id, $current_lang );
		if ( ! $have_product ) {
			$item_list = array( 'total' => 0, 'search_in_name' => wc_ps_ict_t__( 'Product Name', __('Product Name', 'woocommerce-predictive-search' ) ) );
			return $item_list;
		}

		$sql = $this->get_product_search_sql( $search_keyword, $row, $start, $woocommerce_search_focus_enable, $woocommerce_search_focus_plugin, 'product', $product_term_id, $current_lang, false );

		$search_products = $wpdb->get_results( $sql );

		$total_product = count( $search_products );
		$item_list = array( 'total' => $total_product, 'search_in_name' => wc_ps_ict_t__( 'Product Name', __('Product Name', 'woocommerce-predictive-search' ) ) );
		if ( $search_products && $total_product > 0 ) {
			$item_list['items'] = array();

			if ( $include_header ) {
				$item_list['items'][] = array(
					'title' 	=> wc_ps_ict_t__( 'Product Name', __('Product Name', 'woocommerce-predictive-search' ) ),
					'keyword'	=> $search_keyword,
					'type'		=> 'header'
				);
			}

			if ( $in_results_page && 'grid' === $results_display_type ) {

				foreach ( $search_products as $current_product ) {
					ob_start();
					$post_object = get_post( $current_product->post_id );

					setup_postdata( $GLOBALS['post'] =& $post_object );

					wc_get_template_part( 'content', 'product' );

					$card_html = ob_get_clean();

					$item_list['items'][] = array( 'card' => $card_html );

					$row-- ;
					if ( $row < 1 ) break;
				}

			} else {

				$thumbnail_size_name = 'woocommerce_thumbnail';

				$current_db_version = get_option( 'woocommerce_db_version', null );

				foreach ( $search_products as $current_product ) {

					$product_id = $current_product->post_id;
					global $product;
					global $post;

					$product = wc_get_product( $product_id );
					$post = get_post( $product_id );

					$product_description = Functions::woops_limit_words( strip_tags( Functions::strip_shortcodes( strip_shortcodes ( $post->post_content ) ) ), $text_lenght, '...' );
					if ( trim( $product_description ) == '' ) $product_description = Functions::woops_limit_words( strip_tags( Functions::strip_shortcodes( strip_shortcodes( $post->post_excerpt ) ) ), $text_lenght, '...' );

					$availability      = $product->get_availability();
					$availability_html = empty( $availability['availability'] ) ? '' : '<span class="stock ' . esc_attr( $availability['class'] ) . '">' . esc_html( $availability['availability'] ) . '</span>';

					$item_data = array(
						'title'       => $current_product->post_title,
						'keyword'     => $current_product->post_title,
						'url'         => $product->get_permalink(),
						'image_url'   => Functions::get_product_thumbnail_url( $product_id, 0, $thumbnail_size_name, 64, 64 ),
						'description' => $product_description,
						'stock'       => $availability_html,
						'type'        => 'product'
					);

					if ( $show_price ) $item_data['price'] = $product->get_price_html();
					if ( $show_sku ) {
						global $wc_ps_product_sku_data;
						$item_data['sku'] = stripslashes( $wc_ps_product_sku_data->get_item( $product_id ) );
					}
					if ( $show_addtocart ) $item_data['addtocart']   = Functions::get_product_addtocart( $product, $in_results_page );
					if ( $show_categories ) $item_data['categories'] = Functions::get_terms_object( $product_id, 'product_cat' );
					if ( $show_tags ) $item_data['tags']             = Functions::get_terms_object( $product_id, 'product_tag' );

					$item_list['items'][] = $item_data;

					$row-- ;
					if ( $row < 1 ) break;
				}
			}
		}

		return $item_list;
	}

	/**
	 * Get array post list
	 */
	public function get_post_results( $search_keyword, $row, $start = 0, $woocommerce_search_focus_enable = '', $woocommerce_search_focus_plugin = '', $post_term_id = 0, $text_lenght = 100, $current_lang = '', $post_type = 'post', $include_header = true , $show_categories = false, $show_tags = false ) {
		global $wpdb;

		$total_post = 0;
		$have_post = $this->check_product_exsited( $search_keyword, $woocommerce_search_focus_enable, $woocommerce_search_focus_plugin, $post_type, $post_term_id, $current_lang );
		if ( ! $have_post ) {
			$item_list = array( 'total' => $total_post, 'search_in_name' => ( $post_type == 'post' ) ? wc_ps_ict_t__( 'Posts', __('Posts', 'woocommerce-predictive-search' ) ) : wc_ps_ict_t__( 'Pages', __('Pages', 'woocommerce-predictive-search' ) ) );
			return $item_list;
		}

		$sql = $this->get_product_search_sql( $search_keyword, $row, $start, $woocommerce_search_focus_enable, $woocommerce_search_focus_plugin, $post_type, $post_term_id, $current_lang, false );

		$search_posts = $wpdb->get_results( $sql );

		$total_post = count( $search_posts );
		$item_list = array( 'total' => $total_post, 'search_in_name' => ( $post_type == 'post' ) ? wc_ps_ict_t__( 'Posts', __('Posts', 'woocommerce-predictive-search' ) ) : wc_ps_ict_t__( 'Pages', __('Pages', 'woocommerce-predictive-search' ) ) );
		if ( $search_posts && $total_post > 0 ) {
			$item_list['items'] = array();

			if ( $include_header ) {
				$item_list['items'][] = array(
					'title' 	=> ( $post_type == 'post' ) ? wc_ps_ict_t__( 'Posts', __('Posts', 'woocommerce-predictive-search' ) ) : wc_ps_ict_t__( 'Pages', __('Pages', 'woocommerce-predictive-search' ) ),
					'keyword'	=> $search_keyword,
					'type'		=> 'header'
				);
			}

			$thumbnail_size_name = 'woocommerce_thumbnail';

			foreach ( $search_posts as $item ) {

				$post_data = get_post( $item->post_id );
				$item_description = Functions::woops_limit_words( strip_tags( Functions::strip_shortcodes( strip_shortcodes ( $post_data->post_content ) ) ), $text_lenght, '...' );
				if ( trim( $item_description ) == '' ) $item_description = Functions::woops_limit_words( strip_tags( Functions::strip_shortcodes( strip_shortcodes( $post_data->post_excerpt ) ) ), $text_lenght, '...' );

				$item_data = array(
					'title'       => $item->post_title,
					'keyword'     => $item->post_title,
					'url'         => get_permalink( $item->post_id ),
					'image_url'   => Functions::get_product_thumbnail_url( $item->post_id, 0, $thumbnail_size_name, 64, 64 ),
					'description' => $item_description,
					'type'        => $post_type
				);

				if ( $show_categories ) $item_data['categories'] = Functions::get_terms_object( $item->post_id, 'category' );
				if ( $show_tags ) $item_data['tags']             = Functions::get_terms_object( $item->post_id, 'post_tag' );

				$item_list['items'][] = $item_data;

				$row-- ;
				if ( $row < 1 ) break;
			}
		}

		return $item_list;
	}


	/**
	 * Get product categories dropdown
	 */
	public function get_product_categories_nested( $parent = 0, $child_space = '', $cats_excluded = NULL ) {
		global $wp_version;

		$categories_list = array();

		if ( is_null( $cats_excluded ) ) {
			global $wc_ps_exclude_data;
			$cats_excluded = apply_filters( 'wc_ps_items_excluded', $wc_ps_exclude_data->get_array_items( 'product_cat' ), 'product_cat' );
		}

		if ( version_compare( $wp_version, '4.5.0', '<' ) ) {
			$top_categories = get_terms( 'product_cat', array(
				'hierarchical' => true,
				'exclude'      => $cats_excluded,
				'parent'       => 0,
			) );
		} else {
			$top_categories = get_terms( array(
				'taxonomy'     => 'product_cat',
				'hierarchical' => true,
				'exclude'      => $cats_excluded,
				'parent'       => 0,
			) );
		}

		if ( ! empty( $top_categories ) && ! is_wp_error( $top_categories ) ) {

			foreach( $top_categories as $p_categories_data ) {
				if ( in_array( $p_categories_data->term_id, $cats_excluded ) ) continue;

				$child_space = '';

				$category_data = array(
					'name' => $child_space . $p_categories_data->name,
					'slug' => $p_categories_data->slug,
					'url'  => get_term_link( $p_categories_data->slug, 'product_cat' )
				);

				$categories_list[$p_categories_data->term_id] = $category_data;

				$child_p_categories = get_terms( 'product_cat', array(
					'hierarchical' => true,
					'exclude'      => $cats_excluded,
					'child_of'     => $p_categories_data->term_id,
				) );

				if ( ! empty( $child_p_categories ) && ! is_wp_error( $child_p_categories ) ) {

					$current_top_cat = $p_categories_data->term_id;
					$current_parent_cat = $p_categories_data->term_id;

					$child_space = '&nbsp;&nbsp;&nbsp;';
					foreach( $child_p_categories as $p_categories_data ) {
						if ( in_array( $p_categories_data->term_id, $cats_excluded ) ) continue;

						if ( $current_top_cat == $p_categories_data->parent ) {
							$child_space = '&nbsp;&nbsp;&nbsp;';
						} elseif( $current_parent_cat == $p_categories_data->parent ) {
							$child_space .= '';
						} else {
							$child_space .= '&nbsp;&nbsp;&nbsp;';
						}

						$current_parent_cat = $p_categories_data->parent;

						$category_data = array(
							'name' => $child_space . $p_categories_data->name,
							'slug' => $p_categories_data->slug,
							'url'  => get_term_link( $p_categories_data->slug, 'product_cat' )
						);

						$categories_list[$p_categories_data->term_id] = $category_data;
					}
				}
			}
		}

		return $categories_list;
	}

}
