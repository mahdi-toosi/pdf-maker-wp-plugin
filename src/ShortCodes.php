<?php

namespace PM;

class ShortCodes {
	public function __construct() {
		add_shortcode( 'pm_download_btn', [ $this, 'render_pm_download_btn' ] );
	}

	public function render_pm_download_btn(): bool|string {
		ob_start();
		if ( is_user_logged_in() ) {
			global $post;
			if ( ! isset( $post ) ) {
				return ob_get_clean();
			}

			wp_enqueue_script(
				'pmGetPdf',
				PM_Plugin_URL . '/Assets/js/pm_download_btn_script.js',
				[],
				PM_Plugin_Version,
				true
			);

			echo "<button id='pmGetPdf' onclick='pmGetPdf()' postId='$post->ID'>download pdf</button>";
		} else {
			$endpoint = wp_login_url( get_permalink() );
			echo "<a href='$endpoint'> دانلود PDF </a>";
		}

		return ob_get_clean();
	}

}
