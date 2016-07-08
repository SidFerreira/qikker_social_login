<?php
	
	function qikker_add_styles() {

		global $localurl;

		$cssurl = $localurl . '/public/css/styles-@@replace_css.min.css';

		// Load the CSS
		wp_register_style('qikker_css', $cssurl, null, null);
		wp_enqueue_style('qikker_css');

	}

	add_action( 'wp_enqueue_scripts', 'qikker_add_styles' );