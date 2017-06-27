<?php
/* "Copyright 2012 A3 Revolution Web Design" This software is distributed under the terms of GNU GENERAL PUBLIC LICENSE Version 3, 29 June 2007 */
// File Security Check
if ( ! defined( 'ABSPATH' ) ) exit;
?>
<?php
class WC_Predictive_Search_Synch
{
	public function __construct() {

		// Synch for post
		add_action( 'init', array( $this, 'sync_process_post' ), 1 );

		add_action( 'admin_notices', array( $this, 'start_sync_data_notice' ), 11 );

		/*
		 *
		 * Synch for custom mysql query from 3rd party plugin
		 * Call below code on 3rd party plugin when create post by mysql query
		 * do_action( 'mysql_inserted_post', $post_id );
		 */
		add_action( 'mysql_inserted_post', array( $this, 'synch_mysql_inserted_post' ) );

		if ( is_admin() ) {
			// AJAX sync data
			add_action('wp_ajax_wc_predictive_search_sync_products', array( $this, 'wc_predictive_search_sync_products_ajax' ) );
			add_action('wp_ajax_nopriv_wc_predictive_search_sync_products', array( $this, 'wc_predictive_search_sync_products_ajax' ) );

			add_action('wp_ajax_wc_predictive_search_sync_product_skus', array( $this, 'wc_predictive_search_sync_product_skus_ajax' ) );
			add_action('wp_ajax_nopriv_wc_predictive_search_sync_product_skus', array( $this, 'wc_predictive_search_sync_product_skus_ajax' ) );

			add_action('wp_ajax_wc_predictive_search_sync_categories', array( $this, 'wc_predictive_search_sync_categories_ajax' ) );
			add_action('wp_ajax_nopriv_wc_predictive_search_sync_categories', array( $this, 'wc_predictive_search_sync_categories_ajax' ) );

			add_action('wp_ajax_wc_predictive_search_sync_tags', array( $this, 'wc_predictive_search_sync_tags_ajax' ) );
			add_action('wp_ajax_nopriv_wc_predictive_search_sync_tags', array( $this, 'wc_predictive_search_sync_tags_ajax' ) );

			add_action('wp_ajax_wc_predictive_search_sync_relationships', array( $this, 'wc_predictive_search_sync_relationships_ajax' ) );
			add_action('wp_ajax_nopriv_wc_predictive_search_sync_relationships', array( $this, 'wc_predictive_search_sync_relationships_ajax' ) );

			add_action('wp_ajax_wc_predictive_search_sync_posts', array( $this, 'wc_predictive_search_sync_posts_ajax' ) );
			add_action('wp_ajax_nopriv_wc_predictive_search_sync_posts', array( $this, 'wc_predictive_search_sync_posts_ajax' ) );

			add_action('wp_ajax_wc_predictive_search_sync_pages', array( $this, 'wc_predictive_search_sync_pages_ajax' ) );
			add_action('wp_ajax_nopriv_wc_predictive_search_sync_pages', array( $this, 'wc_predictive_search_sync_pages_ajax' ) );

			add_action('wp_ajax_wc_predictive_search_sync_end', array( $this, 'wc_predictive_search_sync_end_ajax' ) );
			add_action('wp_ajax_nopriv_wc_predictive_search_sync_end', array( $this, 'wc_predictive_search_sync_end_ajax' ) );
		}
	}

	public function start_sync_data_notice() {
		$had_sync_posts_data = get_option( 'wc_predictive_search_had_sync_posts_data', 0 );
		$is_upgraded_new_sync_data = get_option( 'wc_ps_upgraded_to_new_sync_data', 0 );

		if ( 0 != $had_sync_posts_data && 0 != $is_upgraded_new_sync_data ) return;

		if ( 0 == $is_upgraded_new_sync_data ) {
			$heading_text = __( 'Thanks for upgrading to latest version of WooCommerce Predictive Search' , 'woocommerce-predictive-search' );
			$warning_text = __( 'The setup is almost done. Just one more step and you are ready to go. Please run database Sync to populate your Search engine database.' , 'woocommerce-predictive-search' );
		} else {
			$heading_text = __( 'Thanks for installing WooCommerce Predictive Search' , 'woocommerce-predictive-search' );
			$warning_text = __( 'The setup is almost done. Just one more step and you are ready to go. Please run database Sync to populate your Search engine database.' , 'woocommerce-predictive-search' );
		}

		$warning_text = __( 'The setup is almost done. Just one more step and you are ready to go. Please run database Sync to populate your Search engine database.' , 'woocommerce-predictive-search' );

		$sync_data_url = admin_url( 'admin.php?page=woo-predictive-search&tab=performance-settings&box_open=predictive_search_synch_data#predictive_search_synch_data', 'relative' );
	?>
		<div class="message error wc_ps_sync_data_warning">
    		<p>
    			<strong><?php echo $heading_text; ?></strong>
    			- <?php echo $warning_text; ?>
    		</p>
    		<p>
    			<a class="button button-primary" href="<?php echo $sync_data_url; ?>" target="_parent"><?php echo __( 'Sync Now' , 'woocommerce-predictive-search' ); ?></a>
    		</p>
    	</div>
	<?php
	}

	public function get_sync_posts_statistic( $post_type = 'product' ) {
		$status = 'completed';

		global $wc_ps_posts_data;
		$current_items = $wc_ps_posts_data->get_total_items_synched( $post_type );

		$all_items      = wp_count_posts( $post_type );
		$total_items    = isset( $all_items->publish ) ? $all_items->publish : 0;

		if ( $total_items > $current_items ) {
			$status = 'continue';
		}

		return array( 'status' => $status, 'current_items' => $current_items, 'total_items' => $total_items );
	}

	public function get_sync_product_skus_statistic() {
		$status = 'completed';

		global $wc_ps_product_sku_data;
		$current_skus = $wc_ps_product_sku_data->get_total_items_synched();

		$total_skus = $wc_ps_product_sku_data->get_total_items_need_sync();
		$total_skus = ! empty( $total_skus ) ? $total_skus : 0;

		if ( $total_skus > $current_skus ) {
			$status = 'continue';
		}

		return array( 'status' => $status, 'current_items' => $current_skus, 'total_items' => $total_skus );
	}

	public function wc_predictive_search_sync_posts( $post_type = 'product' ) {
		$end_time = time() + 16;

		$this->migrate_posts( $post_type, $end_time );

		return $this->get_sync_posts_statistic( $post_type );
	}

	public function wc_predictive_search_sync_product_skus() {
		$end_time = time() + 16;

		$this->migrate_skus( $end_time );

		return $this->get_sync_product_skus_statistic();
	}

	public function wc_predictive_search_sync_products_ajax() {
		$result = $this->wc_predictive_search_sync_posts( 'product' );

		echo json_encode( $result );

		die();
	}

	public function wc_predictive_search_sync_product_skus_ajax() {
		$result = $this->wc_predictive_search_sync_product_skus();

		echo json_encode( $result );

		die();
	}

	public function wc_predictive_search_sync_categories_ajax() {
		$status = 'completed';

		echo json_encode( array( 'status' => $status, 'current_items' => 0, 'total_items' => 0 ) );

		die();
	}

	public function wc_predictive_search_sync_tags_ajax() {
		$status = 'completed';

		echo json_encode( array( 'status' => $status, 'current_items' => 0, 'total_items' => 0 ) );

		die();
	}

	public function wc_predictive_search_sync_relationships_ajax() {
		$status = 'completed';

		echo json_encode( array( 'status' => $status, 'current_items' => 0, 'total_items' => 0 ) );

		die();
	}

	public function wc_predictive_search_sync_posts_ajax() {
		$result = $this->wc_predictive_search_sync_posts( 'post' );

		echo json_encode( $result );

		die();
	}

	public function wc_predictive_search_sync_pages_ajax() {
		$result = $this->wc_predictive_search_sync_posts( 'page' );

		echo json_encode( $result );

		die();
	}

	public function wc_predictive_search_sync_end_ajax() {
		update_option( 'wc_predictive_search_synced_posts_data', 1 );
		update_option( 'wc_predictive_search_manual_synced_completed_time', current_time( 'timestamp' ) );

		wp_send_json( array( 'status' => 'OK', 'date' => date_i18n( get_option( 'date_format' ) . ' - ' . get_option( 'time_format' ) ) ) );

		die();
	}

	public function sync_process_post() {
		add_action( 'save_post', array( $this, 'synch_save_post' ), 12, 2 );
		add_action( 'delete_post', array( $this, 'synch_delete_post' ) );
	}

	public function empty_posts() {
		global $wc_ps_posts_data;
		global $wc_ps_postmeta_data;
		global $wc_ps_product_sku_data;

		// Empty all tables
		$wc_ps_posts_data->empty_table();
		$wc_ps_postmeta_data->empty_table();
		$wc_ps_product_sku_data->empty_table();

		update_option( 'wc_predictive_search_synced_posts_data', 0 );
	}

	public function update_sync_status() {
		update_option( 'wc_predictive_search_had_sync_posts_data', 1 );
		update_option( 'wc_ps_upgraded_to_new_sync_data', 1 );
	}

	public function migrate_posts( $post_types = array( 'product' ), $end_time = 0 ) {
		global $wpdb;
		global $wc_ps_posts_data;
		global $wc_ps_postmeta_data;

		$this->update_sync_status();

		if ( ! is_array( $post_types ) ) {
			$post_types = array( $post_types );
		}
		$post_types = apply_filters( 'predictive_search_post_types_support', $post_types );

		// Check if synch data is stopped at latest run then continue synch without empty all the tables
		$synced_data = get_option( 'wc_predictive_search_synced_posts_data', 0 );

		if ( 0 == $synced_data ) {
			// continue synch data from stopped post ID
			$stopped_ID = $wc_ps_posts_data->get_latest_post_id( $post_types );
			if ( empty( $stopped_ID ) || is_null( $stopped_ID ) ) {
				$stopped_ID = 0;
			}
		} else {
			$this->empty_posts();
			$stopped_ID = 0;
		}

		// If it's newest ID then fetch missed old ID to sync
		if ( $wc_ps_posts_data->is_newest_id( $post_types ) ) {
			$all_posts = $wpdb->get_results(
				$wpdb->prepare(
					"SELECT p.ID, p.post_title, p.post_type FROM {$wpdb->posts} AS p WHERE p.post_status = %s AND p.post_type IN ('". implode("','", $post_types ) ."') AND NOT EXISTS ( SELECT 1 FROM {$wpdb->ps_posts} AS pp WHERE p.ID = pp.post_id ) ORDER BY p.ID ASC LIMIT 0, 500" ,
					'publish'
				)
			);

		// Or continue sync based latest ID have synced
		} else {
			$all_posts = $wpdb->get_results(
				$wpdb->prepare(
					"SELECT p.ID, p.post_title, p.post_type FROM {$wpdb->posts} AS p WHERE p.ID > %d AND p.post_status = %s AND p.post_type IN ('". implode("','", $post_types ) ."') ORDER BY p.ID ASC LIMIT 0, 500" ,
					$stopped_ID,
					'publish'
				)
			);
		}

		if ( $all_posts && is_array( $all_posts ) && count( $all_posts ) > 0 ) {

			foreach ( $all_posts as $item ) {

				// Stop command after timeout is set
				if ( $end_time > 0 && $end_time <= time() ) {
					break;
				}

				$post_id       = $item->ID;

				$item_existed = $wc_ps_posts_data->is_item_existed( $post_id );
				if ( '0' == $item_existed ) {
					$post_title = $item->post_title;
					if ( in_array( $item->post_type, array( 'product_variation' ) ) ) {
						$post_title = WC_Predictive_Search_Functions::get_product_variation_name( $post_id );
					}
					$wc_ps_posts_data->insert_item( $post_id, $post_title, $item->post_type );
				}
			}
		}
	}

	public function migrate_skus( $end_time = 0 ) {
		global $wpdb;
		global $wc_ps_postmeta_data;
		global $wc_ps_product_sku_data;

		$this->update_sync_status();

		// Check if synch data is stopped at latest run then continue synch without empty all the tables
		$synced_data = get_option( 'wc_predictive_search_synced_posts_data', 0 );

		if ( 0 == $synced_data ) {
			// continue synch data from stopped post ID
			$stopped_ID = $wc_ps_product_sku_data->get_latest_post_id();
			if ( empty( $stopped_ID ) || is_null( $stopped_ID ) ) {
				$stopped_ID = 0;
			}
		} else {
			$wc_ps_product_sku_data->empty_table();
			$stopped_ID = 0;
		}

		/*$all_skus = $wpdb->get_results(
			$wpdb->prepare(
				"SELECT p.ID, p.post_parent, pm.meta_value FROM {$wpdb->posts} AS p INNER JOIN {$wpdb->postmeta} AS pm ON (p.ID=pm.post_id) WHERE p.ID > %d AND p.post_type IN ('". implode("','", array( 'product', 'product_variation' ) ) ."') AND p.post_status = %s AND pm.meta_key = %s AND pm.meta_value NOT LIKE '' ORDER BY p.ID ASC LIMIT 0, 500",
				$stopped_ID,
				'publish',
				'_sku'
			)
		);*/

		// If it's newest ID then fetch missed old ID to sync
		if ( $wc_ps_product_sku_data->is_newest_id() ) {
			$all_skus = $wpdb->get_results(
				$wpdb->prepare(
					"SELECT p.ID, p.post_parent FROM {$wpdb->posts} AS p WHERE p.post_type IN ('". implode("','", array( 'product' ) ) ."') AND p.post_status = %s AND NOT EXISTS ( SELECT 1 FROM {$wpdb->ps_product_sku} AS ps WHERE p.ID = ps.post_id ) ORDER BY p.ID ASC LIMIT 0, 500" ,
					'publish'
				)
			);

		// Or continue sync based latest ID have synced
		} else {
			$all_skus = $wpdb->get_results(
				$wpdb->prepare(
					"SELECT p.ID, p.post_parent FROM {$wpdb->posts} AS p WHERE p.ID > %d AND p.post_type IN ('". implode("','", array( 'product' ) ) ."') AND p.post_status = %s ORDER BY p.ID ASC LIMIT 0, 500",
					$stopped_ID,
					'publish'
				)
			);
		}

		if ( $all_skus && is_array( $all_skus ) && count( $all_skus ) > 0 ) {

			foreach ( $all_skus as $item ) {

				// Stop command after timeout is set
				if ( $end_time > 0 && $end_time <= time() ) {
					break;
				}

				$post_id     = $item->ID;
				$post_parent = $item->post_parent;
				$sku         = get_post_meta( $post_id, '_sku', true );

				if ( empty( $sku ) || '' == trim( $sku ) ) {
					$sku = '';
				}

				$item_existed = $wc_ps_product_sku_data->is_item_existed( $post_id );
				if ( '0' == $item_existed ) {
					$wc_ps_product_sku_data->insert_item( $post_id, $sku, $post_parent );
				}

				// Migrate Product Out of Stock
				if ( version_compare( WC_VERSION, '3.0.0', '<' ) ) {
					$outofstock = get_post_meta( $post_id, '_stock_status', true );
					if ( ! empty( $outofstock ) && 'outofstock' == trim( $outofstock ) ) {
						$outofstock = true;
					} else {
						$outofstock = false;
					}
				} else {
					$terms      = get_the_terms( $post_id, 'product_visibility' );
					$term_names = is_array( $terms ) ? wp_list_pluck( $terms, 'name' ) : array();
					$outofstock = in_array( 'outofstock', $term_names );
				}

				if ( $outofstock ) {
					$wc_ps_postmeta_data->update_item_meta( $post_id, '_stock_status', 'outofstock' );
				} else {
					$wc_ps_postmeta_data->delete_item_meta( $post_id, '_stock_status' );
				}
			}
		}
	}

	// This function just for auto update to version 3.2.0
	public function migrate_products_out_of_stock() {
		global $wpdb;
		global $wc_ps_postmeta_data;

		$all_out_of_stock = $wpdb->get_results(
			$wpdb->prepare(
				"SELECT post_id FROM {$wpdb->postmeta} WHERE meta_key = %s AND meta_value = %s",
				'_stock_status',
				'outofstock'
			)
		);

		if ( $all_out_of_stock ) {
			foreach ( $all_out_of_stock as $item ) {
				$wc_ps_postmeta_data->update_item_meta( $item->post_id, '_stock_status', 'outofstock' );
			}
		}
	}

	public function synch_full_database() {
		$this->migrate_posts();
	}

	public function delete_post_data( $post_id ) {
		global $wc_ps_posts_data;
		global $wc_ps_postmeta_data;
		global $wc_ps_product_sku_data;

		$wc_ps_posts_data->delete_item( $post_id );
		$wc_ps_postmeta_data->delete_item_metas( $post_id );
		$wc_ps_product_sku_data->delete_item( $post_id );
	}

	public function synch_save_post( $post_id, $post ) {
		global $wpdb;
		global $wc_ps_posts_data;
		global $wc_ps_postmeta_data;
		global $wc_ps_product_sku_data;

		$this->delete_post_data( $post_id );

		$post_types = apply_filters( 'predictive_search_post_types_support', array( 'post', 'page', 'product' ) );

		if ( 'publish' == $post->post_status && in_array( $post->post_type, $post_types ) ) {

			$wc_ps_posts_data->update_item( $post_id, $post->post_title, $post->post_type );

			if ( 'product' == $post->post_type ) {
				$sku = get_post_meta( $post_id, '_sku', true );
				if ( empty( $sku ) || '' == trim( $sku ) ) {
					$sku = '';
				}
				$wc_ps_product_sku_data->update_item( $post_id, $sku, 0 );

				// Migrate Product Out of Stock
				if ( version_compare( WC_VERSION, '3.0.0', '<' ) ) {
					$outofstock = get_post_meta( $post_id, '_stock_status', true );
					if ( ! empty( $outofstock ) && 'outofstock' == trim( $outofstock ) ) {
						$outofstock = true;
					} else {
						$outofstock = false;
					}
				} else {
					$terms      = get_the_terms( $post_id, 'product_visibility' );
					$term_names = is_array( $terms ) ? wp_list_pluck( $terms, 'name' ) : array();
					$outofstock = in_array( 'outofstock', $term_names );
				}

				if ( $outofstock ) {
					$wc_ps_postmeta_data->update_item_meta( $post_id, '_stock_status', 'outofstock' );
				} else {
					$wc_ps_postmeta_data->delete_item_meta( $post_id, '_stock_status' );
				}
			}

			if ( 'page' == $post->post_type ) {
				global $woocommerce_search_page_id;

				// flush rewrite rules if page is editing is WooCommerce Search Result page
				if ( $post_id == $woocommerce_search_page_id ) {
					flush_rewrite_rules();
				}
			}

		}
	}

	public function synch_delete_post( $post_id ) {
		global $wc_ps_exclude_data;

		$this->delete_post_data( $post_id );

		$post_type = get_post_type( $post_id );

		$wc_ps_exclude_data->delete_item( $post_id, $post_type );
	}

	public function synch_mysql_inserted_post( $post_id = 0 ) {
		if ( $post_id < 1 ) return;

		global $wpdb;
		$post_types = apply_filters( 'predictive_search_post_types_support', array( 'post', 'page', 'product' ) );

		$item = $wpdb->get_row(
			$wpdb->prepare(
				"SELECT ID, post_title, post_type, post_parent FROM {$wpdb->posts} WHERE ID = %d AND post_status = %s AND post_type IN ('". implode("','", $post_types ) ."')" ,
				$post_id,
				'publish'
			)
		);

		if ( $item ) {
			global $wc_ps_posts_data;
			global $wc_ps_postmeta_data;
			global $wc_ps_product_sku_data;

			$item_existed = $wc_ps_posts_data->is_item_existed( $post_id );
			if ( '0' == $item_existed ) {
				$wc_ps_posts_data->insert_item( $post_id, $item->post_title, $item->post_type );
			}

			if ( in_array( $item->post_type, array( 'product', 'product_variation' ) ) ) {
				$sku         = get_post_meta( $post_id, '_sku', true );
				$post_parent = $item->post_parent;

				if ( ( empty( $sku ) || '' == trim( $sku ) ) && $post_parent > 0 ) {
					$sku = get_post_meta( $post_parent, '_sku', true );
				}

				if ( empty( $sku ) || '' == trim( $sku ) ) {
					$sku = '';
				}

				$item_existed = $wc_ps_product_sku_data->is_item_existed( $post_id );
				if ( '0' == $item_existed ) {
					$wc_ps_product_sku_data->insert_item( $post_id, $sku, $post_parent );
				}

				// Migrate Product Out of Stock
				if ( version_compare( WC_VERSION, '3.0.0', '<' ) ) {
					$outofstock = get_post_meta( $post_id, '_stock_status', true );
					if ( ! empty( $outofstock ) && 'outofstock' == trim( $outofstock ) ) {
						$outofstock = true;
					} else {
						$outofstock = false;
					}
				} else {
					$terms      = get_the_terms( $post_id, 'product_visibility' );
					$term_names = is_array( $terms ) ? wp_list_pluck( $terms, 'name' ) : array();
					$outofstock = in_array( 'outofstock', $term_names );
				}
				if ( $outofstock ) {
					$wc_ps_postmeta_data->update_item_meta( $post_id, '_stock_status', 'outofstock' );
				} else {
					$wc_ps_postmeta_data->delete_item_meta( $post_id, '_stock_status' );
				}
			}
		}
	}
}

global $wc_ps_synch;
$wc_ps_synch = new WC_Predictive_Search_Synch();
?>