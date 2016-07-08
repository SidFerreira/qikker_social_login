<?php

	function qikker_add_vendor_scripts() {

		global $localurl;
		global $gravityforms;

		$vendorurl = $localurl . '/public/js/vendor-@@replace_vendor.js';

		// Load the JS
		wp_register_script('qikker_vendor_js', $vendorurl, null, null, $gravityforms);
		wp_enqueue_script('qikker_vendor_js');

	}

	add_action( 'wp_enqueue_scripts', 'qikker_add_vendor_scripts' );