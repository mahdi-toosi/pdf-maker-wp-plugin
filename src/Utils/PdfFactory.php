<?php

namespace PM\Utils;

use WP_Post;
use Mpdf\Mpdf;
use Mpdf\MpdfException;
use Mpdf\Config\ConfigVariables;
use Mpdf\Config\FontVariables;

class PdfFactory {
	protected string $content;
	protected string $type;

	public function set_post( WP_Post|null $post ): void {
		if ( empty( $post ) ) {
			wp_send_json_error( "you didnt pass post", 500 );
		}

		$content       = '<h1>' . $post->post_title . '</h1>'
		                 . get_the_post_thumbnail( $post->ID, 'medium' )
		                 . $post->post_content;
		$this->content = $content;
		$this->type    = 'post';
	}


	public static function get_settings(): array {
		$defaultSettings = [
			'mode'                    => '',
			'format'                  => 'A4',
			'default_font_size'       => '12',
			'default_font'            => 'sans-serif',
			'margin_left'             => 10,
			'margin_right'            => 10,
			'margin_top'              => 10,
			'margin_bottom'           => 10,
			'margin_header'           => 0,
			'margin_footer'           => 0,
			'orientation'             => 'P',
			'show_watermark'          => false,
			'display_mode'            => 'fullpage',
			'custom_font_dir'         => '',
			'custom_font_data'        => [],
			'auto_language_detection' => false,
			'pdfa'                    => false,
			'pdfaauto'                => false,
			'direction'               => 'rtl',
		];

		$defaultConfig = ( new ConfigVariables() )->getDefaults();
		$fontDirs      = $defaultConfig['fontDir'];

		$defaultFontConfig = ( new FontVariables() )->getDefaults();
		$fontData          = $defaultFontConfig['fontdata'];

		$stored_settings = json_decode(
			get_option( PM_Plugin_Settings_Key, "[]" ), true
		);

		$settings = array_merge(
			$defaultSettings,
			$stored_settings,
			[
				'default_font' => 'custom',
				'fontDir'      => array_merge(
					$fontDirs,
					[ PM_Plugin_PATH . '/Assets', ]
				),
				'fontdata'     => array_merge(
					$fontData,
					[
						'custom' => [
							'R'          => 'Vazir-Regular.ttf',
							'useOTL'     => 0xFF,
							'useKashida' => 75,
						]
					]
				),
			]
		);

		return $settings;
	}

	/**
	 * @throws MpdfException
	 * @throws \Exception
	 */
	public function build(): array {
		if ( empty( $this->content ) ) {
			throw new \Exception( "you didn't pass html" );
		}

		$settings = $this->get_settings();
		$mpdf     = new Mpdf( $settings );
		$styles   = file_get_contents(
			            PM_Plugin_PATH
			            . '/Assets/styles/pdf-styles.css' )
		            . $settings['styles'];

		$mpdf->WriteHTML( $styles, \Mpdf\HTMLParserMode::HEADER_CSS );

		$html = '<html lang="ar" dir="rtl">
					<body>
						' . $this->content . '
					</body>
				</html>';
		$mpdf->writeHtml( $html );

		$file_name    = $this->type . '_' . time();
		$file_address = $this->get_file_address( $file_name );
		$mpdf->OutputFile( $file_address );

		return [ 'name' => $file_name, 'address' => $file_address ];
	}

	public static function get_file_address( $file_name ): string {
		return PM_Plugin_Storage . '/' . $file_name . '.pdf';
	}
}
