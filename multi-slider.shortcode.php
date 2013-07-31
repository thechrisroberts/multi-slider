<?php

class Multi_Slider_Shortcode
{
	public static function init()
	{
		add_shortcode('mslider', array('Multi_Slider_Shortcode', 'shortcode'));
	}

	public static function shortcode($attributes)
	{
		$slug = sanitize_title_with_dashes($attributes['slug']);
		$container = Multi_Slider_Slide::show_slides($slug, true);
		
		return $container;
	}
}

?>