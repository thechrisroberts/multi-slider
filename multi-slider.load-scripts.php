<?php

class Multi_Slider_Load_Scripts
{
	public static function init()
	{
		add_action('wp_print_scripts', array('Multi_Slider_Load_Scripts', 'load_scripts'));
		add_action('admin_init', array('Multi_Slider_Load_Scripts', 'load_admin_scripts'));
	}

	public static function load_scripts()
	{
		// Load jQuery, if not already present
		wp_enqueue_script('jquery');
		
		// Load RefineSlide		
		wp_register_script('jquery_refine', plugins_url() .'/multi-slider/js/jquery.refineslide.min.js', array('jquery'));
		wp_enqueue_script('jquery_refine');
	}

	public static function load_admin_scripts()
	{
		wp_register_style('jquery-ui-style', 'http://code.jquery.com/ui/1.10.3/themes/smoothness/jquery-ui.css', true);
        wp_enqueue_style('jquery-ui-style' );
        
        wp_enqueue_script('jquery');
	}

	public static function js_for_slug($mslider_slug, $mslider_settings = false)
	{
		$mslider_js = '';
		
		if (!$mslider_settings) {
			$mslider_settings = unserialize(get_option('mslider_slide_'. $mslider_slug));
		}
		
		$useThumbs = $mslider_settings["show_thumbnails"] == true ? "true" : "false";
		$useArrows = $mslider_settings["show_arrows"] == true ? "true" : "false";
		$autoPlay = $mslider_settings["autoplay"] == true ? "true" : "false";
					
		$mslider_js .= '
			<script type="text/javascript">
				jQuery(document).ready(function() {
			        jQuery(".mslider_'. $mslider_slug .'_thumbs").refineSlide({
			            keyNav: false,
			            maxWidth: '. $mslider_settings["width"] .',
			            transition: \''. $mslider_settings["transition"] .'\',
			            transitionDuration: '. $mslider_settings["transition_speed"] .',
			            delay: '. $mslider_settings["delay_time"] .',
			            useThumbs: '. $useThumbs .', 
			            useArrows: '. $useArrows .', 
			            autoPlay: '. $autoPlay .'
			        });
				';
		
		if ($mslider_settings['track_clicks']) {
			$mslider_js .= '
			jQuery(".mslider_'. $mslider_slug .'_thumbs a").mousedown(function() {
				var rpcrequrl = "'. admin_url('admin-ajax.php') .'";
				var logData = {
					action: "trackMslider", 
					slug: "'. $mslider_slug .'", 
					slide: jQuery("img", this).attr("src")
				};
				jQuery.post(rpcrequrl, logData, function(response) { });
			});
			';
		}
		
		$mslider_js .= '
				});
			</script>
		';
		
		return $mslider_js;
	}
}

?>