<?php
/**
 * Predictive Search Meta
 *
 * Class Function into WP e-Commerce plugin
 *
 * Table Of Contents
 *
 *
 * create_custombox()
 * a3_people_metabox()
 */

namespace A3Rev\WCPredictiveSearch;

class MetaBox
{
	public static function create_custombox() {
		global $post;

		add_action( 'post_submitbox_misc_actions', array( __CLASS__, 'hide_from_results_box' ) );
	}

	public static function hide_from_results_box() {
		global $post;

		if ( ! in_array( $post->post_type, array( 'post', 'page', 'product' ) ) ) {
			return;
		}

		global $wc_ps_exclude_data;

		$postid      = $post->ID;
		$is_excluded = false;

		if ( $wc_ps_exclude_data->get_item( $postid, get_post_type( $postid ) ) > 0 ) {
			$is_excluded = true;
		}
	?>
		<div class="misc-pub-section">
			<label>
				<input type="checkbox" <?php checked( true, $is_excluded, true ); ?> value="1" name="ps_exclude_item" class="a3_ps_exclude_item" />
				<?php esc_html_e( 'Hide from Predictive Search results', 'woocommerce-predictive-search' ); ?>
			</label>
		</div>
	<?php
		// Add an nonce field so we can check for it later.
		wp_nonce_field( 'a3_ps_metabox_action', 'a3_ps_metabox_nonce_field' );
	}

	public static function save_custombox( $post_id = 0 ) {
		if ( $post_id < 1 ) {
			global $post;
			$post_id = $post->ID;
		}

		// Check if our nonce is set.
		if ( ! isset( $_POST['a3_ps_metabox_nonce_field'] ) || ! check_admin_referer( 'a3_ps_metabox_action', 'a3_ps_metabox_nonce_field' ) )
			return $post_id;

		// If this is an autosave, our form has not been submitted,
		// so we don't want to do anything.
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )
			return $post_id;

		if ( ! current_user_can( 'edit_post', $post_id ) )
			return $post_id;

		$post_type = get_post_type( $post_id );

		if ( ! in_array( $post_type, array( 'product', 'post', 'page' ) ) )
			return $post_id;

		$post_status = get_post_status( $post_id );
		if ( $post_status == 'inherit' )
			return $post_id;

		global $wc_ps_exclude_data;

		if ( isset( $_REQUEST['ps_exclude_item'] ) && $_REQUEST['ps_exclude_item'] == 1 ) {
			$wc_ps_exclude_data->insert_item( $post_id , $post_type );
		} else {
			$wc_ps_exclude_data->delete_item( $post_id, $post_type );
		}
	}
}
