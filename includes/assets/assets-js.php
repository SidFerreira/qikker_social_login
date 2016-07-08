<?php

	function qikker_add_scripts() {

		global $localurl;
		global $gravityforms;

		$jsurl = $localurl . '/public/js/custom-1467976930727178287.js';

		// Load the JS
		wp_register_script('qikker_js', $jsurl, null, null, $gravityforms);
		wp_enqueue_script('qikker_js');

	}

	add_action( 'wp_enqueue_scripts', 'qikker_add_scripts' );