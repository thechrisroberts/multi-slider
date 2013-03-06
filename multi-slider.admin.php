<?php
/* Serialized slider array
 *
 * $mslider['slider-slug'] => ['name', 'description', 'use_tracking', 'dimensions', 'transition', 'transition_speed', 'slide_duration', 'hover_pause'];
 *
 * Future: pager, show_prevnext
 *
 */

if (!function_exists('mslider_adminMenu'))
{
	function mslider_adminMenu()
	{
		add_menu_page('Manage Sliders', 'Multi Slider', 'install_plugins', 'mslider-settings', 'mslider_manageSliders');
		add_submenu_page('mslider-settings', 'Add Slider', 'Add Slider', 'install_plugins', 'mslider-add-slider', 'mslider_addSlider');
		add_submenu_page('mslider-settings', 'Click Tracking', 'Click Tracking', 'manage_options', 'mslider-tracking', 'mslider_tracking');
	}
}

add_action('admin_menu', 'mslider_adminMenu');

if (! function_exists('mslider_manageSliders')) {
	function mslider_manageSliders()
	{
		require_once(__DIR__ .'/multi-slider.admin.manageSliders.php');
		
		mslider_manageSliders_panel();
	}
}

if (! function_exists('mslider_addSlider')) {
	function mslider_addSlider()
	{
		require_once(__DIR__ .'/multi-slider.admin.addSlider.php');
		
		mslider_addSlider_panel();
	}
}

if (! function_exists('mslider_delete_slider')) {
	function mslider_delete_slider()
	{
		if (!empty($_POST) && check_admin_referer('multi_slider_delete_slider', 'multi_slider_nonce')) {
			$mslider_slug = sanitize_title_with_dashes($_REQUEST['mslider_slug']);
			$mslider_sliders = get_option('mslider_sliders', array());
			
			if (!is_array($mslider_sliders))
				$mslider_sliders = unserialize($mslider_sliders);
			
			// Remove slider setting
			delete_option('mslider_slide_'. $mslider_slug);
			
			// Remove slider from array
			unset($mslider_sliders[$mslider_slug]);
			update_option('mslider_sliders', serialize($mslider_sliders));
			
			// Remove slider posts
			$mslider_posts = get_posts(array('post_type' => 'mslider_'. $mslider_slug));
			
			if (sizeof($mslider_posts) > 0) {
				foreach ($mslider_posts as $mslider_post) {
					wp_delete_post($mslider_post->ID);
				}
			}
			
		    wp_redirect(admin_url('admin.php') .'?page=mslider-settings&mslider_action=delete&mslider_slug='. $mslider_slug);
	    }
	    exit();
	}
}

add_action('admin_action_mslider_delete_slider', 'mslider_delete_slider');

?>