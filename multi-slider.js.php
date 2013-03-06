<?php

add_action('wp_head', 'mslider_load_js');

if (!function_exists('mslider_load_js')) {
	function mslider_load_js()
	{
		$mslider_sliders = get_option('mslider_sliders', array());
		
		if (!is_array($mslider_sliders))
			$mslider_sliders = unserialize($mslider_sliders);
		
		if (sizeof($mslider_sliders) > 0) {
			echo "<script type=\"text/javascript\">\n";
			echo "jQuery(document).ready(function() {\n";
			
			foreach ($mslider_sliders as $mslider_slug => $mslider_info) {
				echo mslider_js_for_slug($mslider_slug);
			}
			
			echo "jQuery('.mslider').show();\n";
			
			echo "});\n";
			echo "</script>\n";
		}
	}
}

if (!function_exists('mslider_js_for_slug')) {
	function mslider_js_for_slug($mslider_slug)
	{
		$mslider_js = '';
		
		$mslider_data = unserialize(get_option('mslider_slide_'. $mslider_slug));
		
		if (is_array($mslider_data)) {
			if ($mslider_data['pause_on_hover']) {
				$pause = 1;
			} else {
				$pause = 0;
			}
			
			$mslider_js .= "jQuery('.mslider_". $mslider_slug ."').cycle({\n";
			$mslider_js .= "	cleartype: 0,\n";
			$mslider_js .= "	timeout: ". $mslider_data['delay_time'] .",\n";
			$mslider_js .= "	speed: ". $mslider_data['transition_speed'] .",\n";
			$mslider_js .= "	fx: '". $mslider_data['transition'] ."',\n";
			$mslider_js .= "	pause: ". $pause .",\n";
			$mslider_js .= "	width: ". $mslider_data['width'] .",\n";
			$mslider_js .= "	height: ". $mslider_data['height'] ."\n";
			$mslider_js .= "});\n";
		}
		
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