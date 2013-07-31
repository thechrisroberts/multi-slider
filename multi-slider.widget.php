<?php
class Multi_Slider_Widget extends WP_Widget
{
	public static function init()
	{
		add_action('widgets_init', array('Multi_Slider_Widget', 'register_widget'));
	}

	public static function register_widget()
	{
		register_widget('Multi_Slider_Widget');
	}

	public function Multi_Slider_Widget()
	{
		parent::__construct(false, 'Multi-Slider', array('description' => 'Display one of your defined sliders.', 'classname' => 'mslider'));
	}

	public function widget($args, $instance)
	{
		$slug = sanitize_title_with_dashes($instance['mslider_slug']);
		Multi_Slider_Slide::show_slides($slug);
	}

	public function update($new_instance, $old_instance)
	{
		$instance = array();
		
		$instance['mslider_slug'] = sanitize_title_with_dashes($new_instance['mslider_slug']);

		return $instance;
	}

	public function form($instance)
	{
		$slug = isset($instance['mslider_slug']) ? $instance['mslider_slug'] : '';
		
		$sliders = get_option('mslider_sliders', array());
	
		if (!is_array($sliders)) {
			$sliders = unserialize($sliders);
		}
		
		if (sizeof($sliders) > 0) {
			echo '<p>';
			echo '<label for="'. $this->get_field_id('mslider_slug') .'">Slider to display:</label><br />';
			
			echo '<select id="'. $this->get_field_id('mslider_slug') .'" name="'. $this->get_field_name('mslider_slug') .'">';
			foreach ($sliders as $slug => $info) {
				echo '<option value="'. $slug .'">'. $info['name'] .'</option>';
			}
			echo '</select><br />';
		}
	}
}
?>