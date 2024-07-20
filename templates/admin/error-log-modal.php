<?php
// File Security Check
if ( ! defined( 'ABSPATH' ) )  {
	exit;
}
?>

<div class="modal fade wc-ps-modal" id="<?php echo esc_attr( $error_id ); ?>-modal" tabindex="-1" role="dialog" aria-hidden="true" style="display: none;">
	<div class="modal-dialog modal-lg" role="document">
		<div class="modal-content">
			<div class="modal-body m-3">

				<?php echo wpautop( $error_message ); ?>

				<button class="button" data-dismiss="modal"><?php esc_html_e( 'Close', 'woocommerce-predictive-search' ); ?></button>

			</div>

		</div>
	</div>
</div>