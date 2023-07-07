<?php

namespace PM\Api;

class Index {

	public function __construct() {
		$this->register_routes();
	}

	private function register_routes(): void {
		( new GetPdf() )->register_routes();
		( new StoreSettings() )->register_routes();
	}

}
