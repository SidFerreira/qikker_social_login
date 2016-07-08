<?php

	function qikker_add_vendor_scripts() {

		global $localurl;
		global $gravityforms;

		$vendorurl = $localurl . '/public/js/vendor-1467976930727178287.js';

		// Load the JS
		wp_register_script('qikker_vendor_js', $vendorurl, null, null, $gravityforms);
		wp_enqueue_script('qikker_vendor_js');

	}

	add_action( 'wp_enqueue_scripts', 'qikker_add_vendor_scripts' );