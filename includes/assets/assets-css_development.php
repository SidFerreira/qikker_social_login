<?php

	// If WordPress has loaded then we load the assets for the WordPress implementation of Gromit
	function qikker_add_styles() {

		global $localurl;

		$cssurl = $localurl . '/public/css/styles-1467976930727178287.css';

		// Load the CSS
		wp_register_style('qikker_css', $cssurl, null, null);
		wp_enqueue_style('qikker_css');

	}

	add_action( 'wp_enqueue_scripts', 'qikker_add_styles' );