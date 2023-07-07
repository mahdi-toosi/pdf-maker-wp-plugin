<?php

namespace PM\Api;

trait Auth {
	public function check_admin_permission(): void {
		if ( ! is_user_logged_in() || ! is_admin() ) {
			wp_send_json_error( [ 'authorization error' ], 401 );
		}
	}
}
