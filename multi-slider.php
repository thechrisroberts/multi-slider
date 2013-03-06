<?php
/*
Plugin Name: Multi Slider
Plugin URI: http://croberts.me/multi-slider/
Description: Provides a simple method to add one or more sliders to your website.
Version: 1.0.1
Author: Chris Roberts
Author URI: http://croberts.me/
*/

/*  Copyright 2013 Chris Roberts (email : columcille@gmail.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*/

// Make sure we have our database for click tracking
register_activation_hook(__FILE__, 'mslider_install_db');

if (!function_exists('mslider_install_db')) {
	function mslider_install_db() {
		global $wpdb;
		
		$table_name = $wpdb->prefix . "mslider_tracking";
		
		$sql = "CREATE TABLE $table_name (
			slug varchar(20) NOT NULL,
			slide_clicked tinytext NOT NULL,
			visitor tinytext NOT NULL,
			click_time date NOT NULL
		);";
		
		require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
		dbDelta($sql);
	}
}

if (! function_exists('mslider_load_scripts')) {
	function mslider_load_scripts()
	{
		// Load jQuery, if not already present
		wp_enqueue_script('jquery');
		
		// Load the cycle script
		wp_register_script('jquery_cycle', plugins_url() .'/multi-slider/jquery.cycle.all.js', array('jquery'));
		wp_enqueue_script('jquery_cycle');
	}
}

if (! function_exists('mslider_load_styles')) {
	function mslider_load_styles()
	{
		wp_register_style('mslider-style', plugins_url() .'/multi-slider/multi-slider.css');
		wp_enqueue_style('mslider-style');
	}
}

if (! function_exists('mslider_load_admin_scripts')) {
	function mslider_load_admin_scripts()
	{
		wp_register_style('jquery-ui-style', 'http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.1/themes/smoothness/jquery-ui.css', true);
        wp_enqueue_style('jquery-ui-style' );
        
        wp_enqueue_script('jquery');
		wp_enqueue_script('jquery-ui-datepicker');
	}
}

define('MSLIDER_PATH', plugin_dir_path(__FILE__));

require_once(MSLIDER_PATH .'/multi-slider.custom-type.php');
require_once(MSLIDER_PATH .'/multi-slider.widget.php');
require_once(MSLIDER_PATH .'/multi-slider.shortcode.php');
require_once(MSLIDER_PATH .'/multi-slider.js.php');
require_once(MSLIDER_PATH .'/multi-slider.tracking.php');

add_action('wp_print_scripts', 'mslider_load_scripts');
add_action('wp_enqueue_scripts', 'mslider_load_styles');
add_action('admin_init', 'mslider_load_styles');
add_action('admin_init', 'mslider_load_admin_scripts');

if (is_admin()) {
	require_once(MSLIDER_PATH .'/multi-slider.admin.php');
}

?>