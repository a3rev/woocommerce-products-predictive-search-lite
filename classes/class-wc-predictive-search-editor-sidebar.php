<?php
/**
 * WC Predictive Search Lite - Block Editor Sidebar
 *
 * Replaces the legacy post_submitbox_misc_actions checkbox with a modern
 * Plugin Document Setting Panel for the Block Editor. Uses register_post_meta
 * + REST API so it's compatible with Real-Time Collaboration.
 *
 * The lite version only supports the exclude toggle (no Focus Keywords).
 * Data continues to live in the custom ps_exclude table via metadata filters.
 */

namespace A3Rev\WCPredictiveSearch;

class EditorSidebar {

	const POST_TYPES = array( 'post', 'page', 'product' );

	public static function init() {
		add_action( 'init', array( __CLASS__, 'register_meta_fields' ) );
		add_filter( 'get_post_metadata', array( __CLASS__, 'filter_get_metadata' ), 10, 4 );
		add_filter( 'update_post_metadata', array( __CLASS__, 'filter_update_metadata' ), 10, 5 );
		add_filter( 'add_post_metadata', array( __CLASS__, 'filter_add_metadata' ), 10, 5 );
		add_filter( 'delete_post_metadata', array( __CLASS__, 'filter_delete_metadata' ), 10, 5 );
		add_action( 'enqueue_block_editor_assets', array( __CLASS__, 'enqueue_sidebar_assets' ) );
	}

	public static function register_meta_fields() {
		foreach ( self::POST_TYPES as $post_type ) {
			register_post_meta( $post_type, '_ps_exclude_item', array(
				'show_in_rest'  => true,
				'single'        => true,
				'type'          => 'string',
				'default'       => '',
				'auth_callback' => function () {
					return current_user_can( 'edit_posts' );
				},
			) );
		}
	}

	public static function filter_get_metadata( $value, $object_id, $meta_key, $single ) {
		if ( '_ps_exclude_item' !== $meta_key ) {
			return $value;
		}

		global $wc_ps_exclude_data;
		if ( ! isset( $wc_ps_exclude_data ) ) {
			return $value;
		}

		$post_type   = get_post_type( $object_id );
		$is_excluded = $wc_ps_exclude_data->get_item( $object_id, $post_type ) > 0;
		$result      = $is_excluded ? '1' : '';

		return $single ? $result : array( $result );
	}

	public static function filter_update_metadata( $check, $object_id, $meta_key, $meta_value, $prev_value ) {
		if ( '_ps_exclude_item' !== $meta_key ) {
			return $check;
		}

		global $wc_ps_exclude_data;
		if ( ! isset( $wc_ps_exclude_data ) ) {
			return $check;
		}

		$post_type = get_post_type( $object_id );

		if ( '1' === $meta_value || 1 === $meta_value || true === $meta_value ) {
			$wc_ps_exclude_data->insert_item( $object_id, $post_type );
		} else {
			$wc_ps_exclude_data->delete_item( $object_id, $post_type );
		}

		return true;
	}

	public static function filter_add_metadata( $check, $object_id, $meta_key, $meta_value, $unique ) {
		if ( '_ps_exclude_item' === $meta_key ) {
			return self::filter_update_metadata( $check, $object_id, $meta_key, $meta_value, '' );
		}

		return $check;
	}

	public static function filter_delete_metadata( $check, $object_id, $meta_key, $meta_value, $delete_all ) {
		if ( '_ps_exclude_item' !== $meta_key ) {
			return $check;
		}

		global $wc_ps_exclude_data;
		if ( ! isset( $wc_ps_exclude_data ) ) {
			return $check;
		}

		$post_type = get_post_type( $object_id );
		$wc_ps_exclude_data->delete_item( $object_id, $post_type );

		return true;
	}

	public static function enqueue_sidebar_assets() {
		$screen = get_current_screen();
		if ( ! $screen || ! in_array( $screen->post_type, self::POST_TYPES, true ) ) {
			return;
		}

		wp_enqueue_script(
			'wc-ps-editor-sidebar',
			WOOPS_URL . '/admin/assets/js/wc-ps-editor-sidebar.js',
			array(
				'wp-plugins',
				'wp-edit-post',
				'wp-editor',
				'wp-components',
				'wp-data',
				'wp-element',
				'wp-i18n',
			),
			WOOPS_VERSION,
			true
		);
	}
}
