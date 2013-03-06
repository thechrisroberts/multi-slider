<?php
if (!function_exists('mslider_register_widget')) {
	function mslider_register_widget()
	{
		register_widget('mslider_widget');
	}
}

add_action('widgets_init', 'mslider_register_widget');

class mslider_widget extends WP_Widget
{
	function mslider_widget()
	{
		parent::__construct(false, 'Multi-Slider', array('description' => 'Display one of your defined sliders.', 'classname' => 'mslider'));
	}

	function widget($args, $instance)
	{
		$mslider_slug = sanitize_title_with_dashes($instance['mslider_slug']);
		mslider_show_slide($mslider_slug);
	}

	function update($new_instance, $old_instance)
	{
		$instance = array();
		
		$instance['mslider_slug'] = sanitize_title_with_dashes($new_instance['mslider_slug']);

		return $instance;
	}

	function form($instance)
	{
		$mslider_slug = isset($instance['mslider_slug']) ? $instance['mslider_slug'] : '';
		
		$mslider_sliders = get_option('mslider_sliders', array());
	
		if (!is_array($mslider_sliders))
			$mslider_sliders = unserialize($mslider_sliders);
		
		if (sizeof($mslider_sliders) > 0) {
			echo '<p>';
			echo '<label for="'. $this->get_field_id('mslider_slug') .'">Slider to display:</label><br />';
			
			echo '<select id="'. $this->get_field_id('mslider_slug') .'" name="'. $this->get_field_name('mslider_slug') .'">';
			foreach ($mslider_sliders as $mslider_slug => $mslider_info) {
				echo '<option value="'. $mslider_slug .'">'. $mslider_info['name'] .'</option>';
			}
			echo '</select><br />';
		}
	}
}
?>