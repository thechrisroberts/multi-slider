<?php

class Multi_Slider_Load_Scripts
{
	public static function init()
	{
		add_action('wp_print_scripts', array('Multi_Slider_Load_Scripts', 'load_scripts'));
		add_action('admin_init', array('Multi_Slider_Load_Scripts', 'load_admin_scripts'));

		add_action('wp_head', array('Multi_Slider_Load_Scripts', 'insert_js'));
	}

	public static function load_scripts()
	{
		// Load jQuery, if not already present
		wp_enqueue_script('jquery');
		
		// Load the cycle script
		wp_register_script('jquery_cycle', plugins_url() .'/multi-slider/js/jquery.cycle2.min.js', array('jquery'));
		wp_enqueue_script('jquery_cycle');
	}

	public static function load_admin_scripts()
	{
		wp_register_style('jquery-ui-style', 'http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.1/themes/smoothness/jquery-ui.css', true);
        wp_enqueue_style('jquery-ui-style' );
        
        wp_enqueue_script('jquery');
		wp_enqueue_script('jquery-ui-datepicker');
	}

	public static function insert_js()
	{
		$mslider_sliders = get_option('mslider_sliders', array());
		
		if (!is_array($mslider_sliders))
			$mslider_sliders = unserialize($mslider_sliders);
		
		if (sizeof($mslider_sliders) > 0) {
			echo "<script type=\"text/javascript\">\n";
			echo "jQuery(document).ready(function() {\n";
			
			foreach ($mslider_sliders as $mslider_slug => $mslider_info) {
				echo self::js_for_slug($mslider_slug);
			}
			
			echo "jQuery('.mslider').show();\n";
			
			echo "});\n";
			echo "</script>\n";
		}
	}

	public static function js_for_slug($mslider_slug)
	{
		$mslider_js = '';
		
		$mslider_data = unserialize(get_option('mslider_slide_'. $mslider_slug));
		
		if ($mslider_data['track_clicks']) {
			$mslider_js .= "jQuery('.mslider_". $mslider_slug ." a').mousedown(function() {\n";
			$mslider_js .= "	var rpcrequrl = '". admin_url('admin-ajax.php') ."';\n";
			$mslider_js .= "	var logData = {\n";
			$mslider_js .= "		action: 'trackMslider', \n";
			$mslider_js .= "		slug: '". $mslider_slug ."', \n";
			$mslider_js .= "		slide: jQuery('img', this).attr('src')\n";
			$mslider_js .= "	};\n";
			$mslider_js .= "	jQuery.post(rpcrequrl, logData, function(response) { });\n";
			$mslider_js .= "});\n";
		}
		
		return $mslider_js;
	}
}

?>