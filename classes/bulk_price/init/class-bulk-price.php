<?php
namespace tonysong\bulk_price\init;
class Bulk_Price{
	function __construct() {
		add_action('wp_enqueue_scripts', array($this,'load_scripts'));
		add_action('admin_enqueue_scripts', array($this,'load_admin_scripts'));


	}
	function load_scripts(){
		//js

		//css
	}

	function load_admin_scripts(){
		wp_enqueue_script('iris');
	}




}

return new Bulk_Price();