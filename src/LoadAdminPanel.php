<?php

namespace PM;

use PM\Utils\PdfFactory;

class LoadAdminPanel {
	public function __construct() {
		add_action( 'admin_menu',
			[ $this, 'register_settings_page' ] );
	}

	public function register_settings_page(): void {
		add_options_page(
			'MyPlugin Settings',
			'pdf maker',
			'manage_options',
			'pdf-maker-settings',
			[ $this, 'render_settings_page' ]
		);
	}

	function render_settings_page(): void {
		$settings = PdfFactory::get_settings();

		wp_enqueue_style(
			'admin-setting-pag',
			PM_Plugin_URL . '/Assets/styles/admin-setting-page.css',
			[],
			PM_Plugin_Version
		);

		?>


		<h1>pdf maker</h1>

		<form id="pm-settings">
			<div>
				<label> شورتکد دکمه دانلود </label>
				<h3 class="shortcode-text">[pm_download_btn]</h3>
			</div>

			<div>
				<label for="format"> سایز برگه </label>
				<input
					id="format"
					name="format"
					value="<?= $settings['format'] ?>"
				/>
				<small>( A4 | A3 | A5 ) </small>
			</div>

			<div>
				<label for="default_font_size"> سایز فونت متن</label>
				<input
					id="default_font_size"
					name="default_font_size"
					value="<?= $settings['default_font_size'] ?>"
				/>
				<small>( 12 | 13 | 14 ) </small>
			</div>

			<div>
				<label for="margin_top"> فاصله از بالا </label>
				<input
					id="margin_top" name="margin_top"
					value="<?= $settings['margin_top'] ?>"
				/>
				<small>( 10 | 11 | 12 ) </small>
			</div>

			<div>
				<label for="margin_bottom"> فاصله از پایین </label>
				<input
					id="margin_bottom" name="margin_bottom"
					value="<?= $settings['margin_bottom'] ?>"
				/>
				<small>( 10 | 11 | 12 ) </small>
			</div>

			<div>
				<label for="margin_left"> فاصله از چپ </label>
				<input
					id="margin_left" name="margin_left"
					value="<?= $settings['margin_left'] ?>"
				/>
				<small>( 10 | 11 | 12 ) </small>
			</div>

			<div>
				<label for="margin_right"> فاصله از راست </label>
				<input
					id="margin_right" name="margin_right"
					value="<?= $settings['margin_right'] ?>"
				/>
				<small>( 10 | 11 | 12 ) </small>
			</div>


			<div>
				<label for="styles"> استایل ها </label>
				<textarea id="styles" name="styles"><?= $settings['styles'] ??
														'' ?></textarea>
			</div>

			<button id="form" type="submit">ذخیره</button>
		</form>

		<script>
			function OnClickUploadButton() {
				document.getElementById('default_font').click()
			}

			const form = document.getElementById("pm-settings");
			form.addEventListener("submit", handleSubmit);

			async function handleSubmit(event) {
				event.preventDefault();

				const data = new FormData(event.target);

				const values = Object.fromEntries(data.entries());
				const formData = new FormData();
				for (const name in values) {
					formData.append(name, values[name]);
				}

				const submitBtn = document.querySelector('#pm-settings button[type="submit"]')
				submitBtn.innerHTML = 'در حال ذخیره سازی'
				submitBtn.disabled = true

				await fetch('/wp-admin/admin-ajax.php?action=pm_store_settings', {
					method: 'POST',
					body: formData,
					credentials: 'same-origin',
				})

				submitBtn.innerHTML = 'ذخیره'
				submitBtn.disabled = false
			}
		</script>
		<?php
	}
}

