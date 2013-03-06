<?php

if (!function_exists('mslider_shortcode')) {
	function mslider_shortcode($mslider_attributes)
	{
		$mslider_slug = sanitize_title_with_dashes($mslider_attributes['slug']);
		$mslider_container = mslider_show_slide($mslider_slug, true);
		
		return $mslider_container;
	}
}

add_shortcode('mslider', 'mslider_shortcode');

?>