<?php

namespace PM\Api;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use WP_Error;
use WP_REST_Controller;
use WP_REST_Response;
use WP_REST_Server;
use PM\utils\pdfFactory;

/**
 * REST_API Handler
 */
class PostToPdf extends WP_REST_Controller {

	/**
	 * [__construct description]
	 */
	public function __construct() {
		$this->namespace = 'PM/v1';
		$this->rest_base = '/test';
	}

	/**
	 * Register the routes
	 *
	 * @return void
	 */
	public function register_routes(): void {
		register_rest_route(
			$this->namespace,
			$this->rest_base,
			[
				'methods'             => WP_REST_Server::READABLE,
				'callback'            => [ $this, 'get_items' ],
				'permission_callback' => [ $this, 'get_items_permissions_check' ],
				'args'                => $this->get_items_validate_args(),
			]
		);
	}

	/**
	 * Retrieves a collection of items.
	 *
	 * @param WP_REST_Request $request Full details about the request.
	 *
	 * @return WP_REST_Response|WP_Error Response object on success, or WP_Error object on failure.
	 * @throws \Exception
	 */
	public function get_items( $request ): WP_REST_Response|WP_Error {
//		TODO : get dynamic post id
		$post = get_post( 7 );

		$factory = new pdfFactory();
		$factory->set_post( $post );
		$factory->build();

		$items = [
			'req'  => $request->get_json_params(),
			'post' => $post,
		];

		return rest_ensure_response( $items );
	}

	/**
	 * Checks if a given request has access to read the items.
	 *
	 * @param WP_REST_Request $request Full details about the request.
	 *
	 * @return true|WP_Error True if the request has read access, WP_Error object otherwise.
	 */
	public function get_items_permissions_check( $request ): bool|WP_Error {
		return true;
	}

	/**
	 * Retrieves the query params for the items' collection.
	 *
	 * @return array Collection parameters.
	 */
	public function get_items_validate_args(): array {
//		$args = array();
//
//		$args['title'] = array(
//			'type'              => 'string',
//			'required'          => true,
//		);
//
//		$args['color'] = array(
//			'type'     => 'string',
//			'required' => true,
//			'enum'     => array( 'red', 'green', 'blue' ),
//		);
//
//		return $args;
		return [];
	}
}
