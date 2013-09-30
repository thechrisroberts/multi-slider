<?php
/*
Plugin Name: Multi Slider
Plugin URI: http://croberts.me/multi-slider/
Description: Provides a simple method to add one or more sliders to your website.
Version: 1.3.1
Author: Chris Roberts
Author URI: http://croberts.me/
*/

/*  Copyright 2013 Chris Roberts (email : chris@dailycross.net)

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

require_once(plugin_dir_path(__FILE__) .'multi-slider.db.php');
require_once(plugin_dir_path(__FILE__) .'/multi-slider.load-styles.php');
require_once(plugin_dir_path(__FILE__) .'/multi-slider.load-scripts.php');
require_once(plugin_dir_path(__FILE__) .'/multi-slider.shortcode.php');
require_once(plugin_dir_path(__FILE__) .'/multi-slider.tracking.php');
require_once(plugin_dir_path(__FILE__) .'/multi-slider.widget.php');
require_once(plugin_dir_path(__FILE__) .'/multi-slider.custom-type.php');
require_once(plugin_dir_path(__FILE__) .'/multi-slider.admin.php');

class Multi_Slider
{
	public static function init()
	{
		Multi_Slider_DB::init();
		Multi_Slider_Load_Styles::init();
		Multi_Slider_Load_Scripts::init();
		Multi_Slider_Shortcode::init();
		Multi_Slider_Tracking::init();
		Multi_Slider_Widget::init();
		Multi_Slider_Slide::init();

		if (is_admin()) {
			Multi_Slider_Admin::init();
		}
	}
}

Multi_Slider::init();

// Helper functions
function mslider_show_slide($slug)
{
	mslider_show_slides($slug);
}

function mslider_show_slides($slug)
{
	Multi_Slider_Slide::show_slides($slug);
}
?>