<?php

// Make sure the theme supports post thumbnails
add_theme_support('post-thumbnails');

if (!function_exists('mslider_register_image_sizes')) {
	function mslider_register_image_sizes()
	{
		$mslider_sliders = get_option('mslider_sliders', array());
		
		if (!is_array($mslider_sliders))
			$mslider_sliders = unserialize($mslider_sliders);
		
		if (sizeof($mslider_sliders) > 0) {
			foreach ($mslider_sliders as $mslider_slug => $mslider_info) {
				$mslider_data = unserialize(get_option('mslider_slide_'. $mslider_slug));
				
				add_image_size('mslider_'. $mslider_slug, $mslider_data['width'], $mslider_data['height'], true);
			}
		}
	}
}

// Add our thumbnail sizes
mslider_register_image_sizes();

/* Create custom post types for all of our sliders */

add_action('init', 'mslider_create_slider_types');

if (!function_exists('mslider_create_slider_types')) {
	function mslider_create_slider_types()
	{
		$mslider_sliders = get_option('mslider_sliders', array());
		
		if (!is_array($mslider_sliders))
			$mslider_sliders = unserialize($mslider_sliders);
		
		if (sizeof($mslider_sliders) > 0) {
			foreach ($mslider_sliders as $mslider_slug => $mslider_info) {
				$mslider_data = unserialize(get_option('mslider_slide_'. $mslider_slug));
				
				// Check our plural name
				if (substr($mslider_data['name'], strlen($mslider_data['name']) - 1, 1) == "s") {
					$pluralName = $mslider_data['name'];
				} else {
					$pluralName = $mslider_data['name'] ."s";
				}
				
				$mslider_register_post = register_post_type('mslider_'. $mslider_slug,
															array(
																'labels' => array(
																	'name' => __($pluralName),
																	'singular_name' => __($mslider_data['name']),
																	'edit_item' => __('Edit '. $mslider_data['name']),
																	'add_new_item' => __('Add New Slide'),
																	'new_item' => __('New '. $mslider_data['name']),
																	'view_item' => __('View '. $mslider_data['name']),
																	'search_items' => __('Search '. $pluralName),
																	'not_found' => __('No '. $pluralName .' found'),
																	'not_found_in_trash' => __('No '. $pluralName .' found in trash')
																),
																'public' => true,
																'has_archive' => false,
																'description' => $mslider_data['description'],
																'exclude_from_search' => true,
																'supports' => array('title', 'editor', 'thumbnail'),
																'show_in_nav_menus' => false
																)
														);
			}
		}
	}
}

add_action('admin_init', 'mslider_init_slides');

if (!function_exists('mslider_init_slides')) {
	function mslider_init_slides()
	{
		$mslider_sliders = get_option('mslider_sliders', array());
		
		if (!is_array($mslider_sliders))
			$mslider_sliders = unserialize($mslider_sliders);
		
		if (sizeof($mslider_sliders) > 0) {
			foreach ($mslider_sliders as $mslider_slug => $mslider_info) {
				add_meta_box('slider_order-meta', 'Presentation Order', 'mslider_slider_order', 'mslider_'. $mslider_slug, 'side', 'default');
				add_meta_box('slider_link-meta', 'Slide Link', 'mslider_slider_link', 'mslider_'. $mslider_slug, 'normal', 'default');
			}
		}
	}
}

if (!function_exists('mslider_slider_order')) {
	function mslider_slider_order()
	{
		global $post;
		
		$custom = get_post_custom($post->ID);
		$slider_order = isset($custom['slider_order']) ? $custom['slider_order'][0] : '';
		?>
			<label>Slide Position:</label>
			<input name="slider_order" value="<?php echo $slider_order; ?>" />
		<?php
	}
}

if (!function_exists('mslider_slider_link')) {
	function mslider_slider_link()
	{
		global $post;
		
		$custom = get_post_custom($post->ID);
		$slider_link = isset($custom['slider_link']) ? $custom['slider_link'][0] : '';
		?>
			<label>URL for the slide:</label>
			<input name="slider_link" value="<?php echo $slider_link; ?>" size="75" />
		<?php
	}
}

add_action('save_post', 'mslider_save_slide');

if (!function_exists('mslider_save_slide')) {
	function mslider_save_slide($post_id)
	{
		if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE)
			return;
		
		if (isset($_POST['post_type']) && strstr($_POST['post_type'], 'mslider_') !== false) {
			update_post_meta($post_id, 'slider_order', intval($_POST['slider_order']));
			update_post_meta($post_id, 'slider_link', esc_url($_POST['slider_link']));
		}
	}
}

if (!function_exists('mslider_show_slide')) {
	function mslider_show_slide($mslider_slug, $mslider_return_output = false)
	{
		$mslider_slug = sanitize_title_with_dashes($mslider_slug);
		
		$mslider_slides = new WP_query(array('post_type' => 'mslider_'. $mslider_slug, 'posts_per_page' => 10, 'order' => 'ASC', 'orderby' => 'meta_value_num', 'meta_key' => 'slider_order'));
		
		$mslider_output = '';
		
		if ($mslider_slides->found_posts > 0) {
			$mslider_output .= '<div class="mslider mslider_'. $mslider_slug .' '. $mslider_slug .'">';
			
			$counter = 0;
			while ($mslider_slides->have_posts()) {
				$mslider_slides->the_post();
				
				$counter++;
				
				$slideLink = get_post_custom_values('slider_link');
				$blogurl = get_bloginfo('template_url');
				
				$mslider_output .= '<div id="slide_'. $counter .'" class="slide_'. $mslider_slug .'">';
				
				$mslider_output .= '<div class="slideContent">';
				
				$mslider_thumbnail = get_the_post_thumbnail(get_the_ID(), 'mslider_'. $mslider_slug);
				
				$mslider_content = get_the_content();
				$mslider_content = apply_filters('the_content', $mslider_content);
				$mslider_content = str_replace(']]>', ']]&gt;', $mslider_content);
				
				if (empty($mslider_thumbnail) && !empty($mslider_content)) {
					$mslider_output .= $mslider_content;
				} else if (!empty($mslider_thumbnail)) {
					if (!empty($slideLink[0])) {
						$mslider_output .= '<a href="'. $slideLink[0] .'">';
						$mslider_output .= $mslider_thumbnail;
						$mslider_output .= '</a>';
					} else {
						$mslider_output .= $mslider_thumbnail;
					}
				}
				
				$mslider_output .= '</div>';
				
				$mslider_output .= '</div>';
			}
			
			wp_reset_postdata();
			
			$mslider_output .= '</div>';
		}
		
		if ($mslider_return_output) {
			return $mslider_output;
		} else {
			echo $mslider_output;
		}
	}
}

?>