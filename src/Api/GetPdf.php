<?php

namespace PM\Api;

use Mpdf\MpdfException;
use PM\Utils\PdfFactory;

class GetPdf {
	use Auth;

	public function register_routes(): void {
		add_action( 'wp_ajax_pm_get_pdf', [ $this, 'get_pdf_from_post' ] );
	}


	/**
	 * @throws MpdfException
	 */
	public function get_pdf_from_post(): void {
		$this->check_admin_permission();

		$post_id = $_GET['post_id'];
		if ( empty( $post_id ) ) {
			wp_send_json_error( [ 'bad req' ], 422 );
		}
		$post_meta = get_post_meta( $post_id );

		$file_name = $this->get_file_name( $post_id, $post_meta );

		do_action(
			"pm_rest_get_pdf_from_post",
			$post_id,
			get_current_user_id()
		);

		$meta_key = 'pm_download_count';

		if ( ! isset( $post_meta[ $meta_key ] ) ) {
			add_post_meta( $post_id, $meta_key, 1 );
		} else {
			update_post_meta( $post_id, $meta_key,
				intval( $post_meta[ $meta_key ][0] ) + 1 );
		}

		$file_address = PdfFactory::get_file_address( $file_name );

		// Send the PDF file to the user
		header( 'Content-Type: application/pdf' );
		header( 'Content-Disposition: attachment; filename="my-pdf-file.pdf"' );
		header( 'Content-Length: ' . filesize( $file_address ) );
		readfile( $file_address );

		// End the AJAX response
		wp_die();
	}

	/**
	 * @throws MpdfException
	 */
	public function get_file_name( $post_id, $post_meta ): ?string {

		$post                      = get_post( $post_id ); // todo => with type of post
		$file_name_meta_key        = 'pm_file_name';
		$last_modified_at_meta_key = 'pm_last_modified_at';

		$meta_file_name
			= isset( $post_meta[ $file_name_meta_key ] )
			? $post_meta[ $file_name_meta_key ][0] : null;

		$meta_last_modified_at
			= isset( $post_meta[ $last_modified_at_meta_key ] )
			? $post_meta[ $last_modified_at_meta_key ][0] : null;

		if (
			empty( $meta_file_name )
			|| $meta_last_modified_at !== $post->post_modified
		) {
//			doesn't exists or post got updated
			if ( empty( $meta_file_name ) ) {
				$file_address = PdfFactory::get_file_address( $meta_file_name );
				if ( file_exists( $file_address ) ) {
					unlink( $file_address );
				}
			}

			$file_name = $this->generate_pdf( $post );
			if ( empty( $meta_file_name ) ) {

				add_post_meta( $post_id, $file_name_meta_key, $file_name );
				add_post_meta(
					$post_id,
					$last_modified_at_meta_key,
					$post->post_modified
				);

			} else {
				update_post_meta( $post_id, $file_name_meta_key, $file_name );
				update_post_meta(
					$post_id,
					$last_modified_at_meta_key,
					$post->post_modified
				);
			}
		} else {
//			already exist
			$file_name = $meta_file_name;
		}

		if ( ! $file_name ) {
			wp_send_json_error( 'there is no post with this id', 400 );
		}

		return $file_name;
	}

	/**
	 * @throws MpdfException
	 */
	public function generate_pdf( ?\WP_Post $post ) {
		if (
			empty( $post )
			|| $post->post_type !== 'post'
			|| $post->post_status !== 'publish'
		) {
			return null;
		}

		$pdfMaker = new PdfFactory();
		$pdfMaker->set_post( $post );
		$file = $pdfMaker->build();

		return $file['name'];
	}
}
