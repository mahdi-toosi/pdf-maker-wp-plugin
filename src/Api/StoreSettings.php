<?php

namespace PM\Api;

class StoreSettings {
	use Auth;

	public function register_routes(): void {
		add_action( 'wp_ajax_pm_store_settings', [ $this, 'store_settings' ] );
	}

	public function store_settings(): void {
		$this->check_admin_permission();

		$params = $_POST;

		if ( ! is_array( $params ) ) {
			wp_send_json_error( 'not valid', 400 );
		}

		update_option( PM_Plugin_Settings_Key, json_encode( $params ) );

		wp_send_json( [ 'status' => 'success', ] );
	}

}
