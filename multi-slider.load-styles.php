<?php

class Multi_Slider_Load_Styles
{
	public static function init()
	{
		add_action('wp_enqueue_scripts', array('Multi_Slider_Load_Styles', 'load_styles'));
		add_action('admin_init', array('Multi_Slider_Load_Styles', 'load_styles'));
	}

	public static function load_styles()
	{
		wp_register_style('mslider-style', plugins_url() .'/multi-slider/multi-slider.css');
		wp_enqueue_style('mslider-style');
	}
}

?>