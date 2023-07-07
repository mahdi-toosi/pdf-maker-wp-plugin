<?php
/*
Plugin Name: Pdf Maker Plugin
Description: a plugin for export pdf
Version: 0.1
Author: Mahdi Toosi
License: MIT
Text Domain: PdfMaker
Domain Path: /languages
*/

/**
 * Copyright (c) YEAR Mahdi Toosi (email: mailmahditoosi@gmail.com). All rights reserved.
 *
 * This is an add-on for WordPress
 * http://wordpress.org/
 *
 */

// don't call the file directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// psr-4 autoload
require __DIR__ . '/vendor/autoload.php';

use PM\Api\Index as Api;
use PM\Hooks;
use PM\LoadAdminPanel;
use PM\ShortCodes;

final class PdfMaker {

	public string $version = '0.1.0';
	public string $db_version = '0.3.0';

	public function __construct() {

		$this->define_constants();

		register_activation_hook( __FILE__, [ $this, 'activate' ] );
		register_deactivation_hook( __FILE__, [ $this, 'deactivate' ] );

		add_action( 'plugins_loaded', [ $this, 'init_plugin' ] );
	}

	public static function init(): PdfMaker|bool {
		static $instance = false;

		if ( ! $instance ) {
			$instance = new PdfMaker();
		}

		return $instance;
	}

	public function define_constants(): void {
		define( 'PM_Plugin_Version', $this->version );
		define( 'PM_Plugin_Settings_Key', 'pdf_maker_options' );
		define( 'PM_Plugin_PATH', dirname( __FILE__ ) );
		define( 'PM_Plugin_Storage', PM_Plugin_PATH . '/storage' );
		define( 'PM_Plugin_URL', plugins_url( '', __FILE__ ) );
	}

	public function init_plugin(): void {
		$this->includes();
		$this->init_hooks();
	}

	public function activate(): void {

		$installed = get_option( 'pdf_maker_installed' );

		if ( ! $installed ) {
			update_option( 'pdf_maker_installed', time() );
		}

		update_option( 'pdf_maker_version', PM_Plugin_Version );

	}

	public function deactivate() {
	}

	public function includes(): void {
		new ShortCodes();
		new Api();
	}

	public function init_hooks(): void {
		add_action( 'init', [ $this, 'init_classes' ] );
	}


	public function init_classes(): void {
		new Hooks();
		new LoadAdminPanel();
	}


} // PdfMaker

PdfMaker::init();

//function _log( $content, $should_die = true ): void {
//	$file_addr = PM_Plugin_PATH . "/log.txt";
//	file_put_contents( $file_addr, json_encode( $content, JSON_PRETTY_PRINT ) );
//
//	if ( $should_die ) {
//		wp_die();
//	}
//}
