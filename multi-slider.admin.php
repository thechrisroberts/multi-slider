<?php

require_once(plugin_dir_path(__FILE__) .'/multi-slider.admin.manage-sliders.php');
require_once(plugin_dir_path(__FILE__) .'/multi-slider.admin.add-slider.php');

class Multi_Slider_Admin
{
	public static function init()
	{
		add_action('admin_init', array('Multi_Slider_Admin', 'prep_admin'));
		add_action('admin_menu', array('Multi_Slider_Admin', 'add_menu'));
		add_action('admin_action_mslider_delete_slider', array('Multi_Slider_Admin', 'delete_slider'));
	}
	
	public static function prep_admin()
	{
		wp_register_style('mslider-admin-style', plugins_url() .'/multi-slider/multi-slider.admin.css');
	}

	public static function add_menu()
	{
		$mpage = add_menu_page('Manage Sliders', 'Multi Slider', 'install_plugins', 'mslider-settings', array('Multi_Slider_Admin', 'manage_sliders'));
		$apage = add_submenu_page('mslider-settings', 'Add Slider', 'Add Slider', 'install_plugins', 'mslider-add-slider', array('Multi_Slider_Admin', 'add_slider'));
		$tpage = add_submenu_page('mslider-settings', 'Click Tracking', 'Click Tracking', 'manage_options', 'mslider-tracking', array('Multi_Slider_Tracking', 'slide_tracking'));

		add_action('admin_print_styles-'. $mpage, array('Multi_Slider_Admin', 'admin_styles'));
		add_action('admin_print_styles-'. $apage, array('Multi_Slider_Admin', 'admin_styles'));
		add_action('admin_print_styles-'. $tpage, array('Multi_Slider_Admin', 'admin_styles'));
	}

	public static function admin_styles()
	{
		wp_enqueue_style('mslider-admin-style');
	}

	public static function manage_sliders()
	{
		Multi_Slider_Admin_Manage::manage_sliders_panel();
	}

	public static function add_slider()
	{
		Multi_Slider_Admin_Add::add_slider_panel();
	}

	public static function delete_slider()
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

?>