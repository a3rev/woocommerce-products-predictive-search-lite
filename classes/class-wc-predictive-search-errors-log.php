<?php
/* "Copyright 2012 A3 Revolution Web Design" This software is distributed under the terms of GNU GENERAL PUBLIC LICENSE Version 3, 29 June 2007 */

namespace A3Rev\WCPredictiveSearch;

// File Security Check
if ( ! defined( 'ABSPATH' ) ) exit;

class Errors_Log
{

	public $error_prefix = 'wc_ps_error_';

	public function __construct() {

	}

	public function set_error( $error_id, $error_type = '', $error_message = '' ) {
		$current_error_message = $this->get_error( $error_id );
		$error_message         = date('Y-m-d H:i:s') . ' - ' . $error_type . ' - ' . $error_message;
		$new_error_message     = $current_error_message . '<br>' . $error_message;

		update_option( $this->error_prefix . $error_id, $new_error_message );
	}

	public function get_error( $error_id ) {
		return get_option( $this->error_prefix . $error_id, '' );
	}

	public function delete_error( $error_id ) {
		delete_option( $this->error_prefix . $error_id );
	}

	public function log_errors( $error_id, $error_type = '' ) {
		// Ensures fatal errors are logged so admin can view log on popup.
		register_shutdown_function( array( $this, 'shutdown_handler' ), $error_id, $error_type );
	}

	public function shutdown_handler( $error_id, $error_type = '' ) {
		$e = error_get_last();
		
		if ( empty( $e ) or ! ( $e['type'] & ( E_ERROR | E_PARSE | E_COMPILE_ERROR | E_COMPILE_WARNING | E_USER_ERROR | E_RECOVERABLE_ERROR ) ) ) {
			return;
		}

		if ( $e['type'] & E_RECOVERABLE_ERROR ) {
			$error = 'Catchable fatal error';
		} else if ( $e['type'] & E_COMPILE_WARNING ) {
			$error = 'Warning';
		} else {
			$error = 'Fatal error';
		}

		$error_message = $e['message'];

		$this->set_error( $error_id, $error_type, $error_message );
	}

	public function error_modal( $error_id, $error_message = '' ) {

		ob_start();

		wc_ps_error_modal_tpl( array( 'error_id' => $error_id, 'error_message' => $error_message ) );

		$error_modal_output = ob_get_clean();

		return $error_modal_output;
	}
}
