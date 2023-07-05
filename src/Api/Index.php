<?php

namespace PM\Api;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use WP_REST_Controller;

/**
 * REST_API Handler
 */
class Index extends WP_REST_Controller {

	/**
	 * [__construct description]
	 */
	public function __construct() {
		$this->includes();

		add_action( 'rest_api_init', [ $this, 'register_routes' ] );
	}

	/**
	 * Include the controller classes
	 *
	 * @return void
	 */
	private function includes(): void {
	}

	/**
	 * Register the API routes
	 *
	 * @return void
	 */
	public function register_routes(): void {
		( new PostToPdf() )->register_routes();
	}
}
