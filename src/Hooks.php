<?php

namespace PM;

class Hooks {
	public function __construct() {
		add_filter( 'manage_posts_columns',
			[ $this, 'custom_posts_table_columns' ] );

		add_action( 'manage_posts_custom_column',
			[ $this, 'custom_posts_table_column_data' ], 10, 2 );
	}

	function custom_posts_table_columns( $columns ) {
		$columns['count_download'] = 'تعداد دانلود pdf';

		return $columns;
	}

	function custom_posts_table_column_data( $column, $post_id ): void {
		if ( $column !== 'count_download' ) {
			return;
		}

		echo get_post_meta(
			$post_id,
			'pm_download_count',
			true
		);
	}

}
