<?php
class Multi_Slider_Slide
{
	public static function init()
	{
		add_action('init', array('Multi_Slider_Slide', 'create_slider_types'));
		add_action('admin_init', array('Multi_Slider_Slide', 'init_slides'));
		add_action('save_post', array('Multi_Slider_Slide', 'save_slide'));
		add_action('post_edit_form_tag', array('Multi_Slider_Slide', 'post_edit_form_tag'));

		add_theme_support('post-thumbnails');
		self::register_image_sizes();
	}

	public static function register_image_sizes()
	{
		$sliders = get_option('mslider_sliders', array());
		
		if (!is_array($sliders))
			$sliders = unserialize($sliders);
		
		if (sizeof($sliders) > 0) {
			foreach ($sliders as $slug => $info) {
				$slide_data = unserialize(get_option('mslider_slide_'. $slug));
				
				add_image_size('mslider_'. $slug, $slide_data['width'], $slide_data['height'], true);
			}
		}
	}

	public function post_edit_form_tag( ) {
		echo ' enctype="multipart/form-data"';
	}

	public static function create_slider_types()
	{
		$sliders = get_option('mslider_sliders', array());
		
		if (!is_array($sliders))
			$sliders = unserialize($sliders);
		
		if (sizeof($sliders) > 0) {
			foreach ($sliders as $slug => $info) {
				$slide_data = unserialize(get_option('mslider_slide_'. $slug));
				
				// Check our plural name
				if (substr($slide_data['name'], strlen($slide_data['name']) - 1, 1) == "s") {
					$pluralName = $slide_data['name'];
				} else {
					$pluralName = $slide_data['name'] ."s";
				}
				
				$register_post = register_post_type('mslider_'. $slug,
													array(
														'labels' => array(
															'name' => __($pluralName),
															'singular_name' => __($slide_data['name']),
															'edit_item' => __('Edit '. $slide_data['name']),
															'add_new_item' => __('Add New Slide'),
															'new_item' => __('New '. $slide_data['name']),
															'view_item' => __('View '. $slide_data['name']),
															'search_items' => __('Search '. $pluralName),
															'not_found' => __('No '. $pluralName .' found'),
															'not_found_in_trash' => __('No '. $pluralName .' found in trash')
														),
														'public' => true,
														'has_archive' => false,
														'description' => $slide_data['description'],
														'exclude_from_search' => true,
														'supports' => array('title', 'editor', 'thumbnail'),
														'show_in_nav_menus' => false
													));
			}
		}
	}

	public static function init_slides()
	{
		$sliders = get_option('mslider_sliders', array());
		
		if (!is_array($sliders))
			$sliders = unserialize($sliders);
		
		if (sizeof($sliders) > 0) {
			foreach ($sliders as $slug => $info) {
				add_meta_box('slider_order-meta', 'Presentation Order', array('Multi_Slider_Slide', 'slider_order'), 'mslider_'. $slug, 'side', 'default');
				add_meta_box('slider_link-meta', 'Slide Link', array('Multi_Slider_Slide', 'slider_link'), 'mslider_'. $slug, 'normal', 'default');
				add_meta_box('slider_flash-file', 'Flash Slide', array('Multi_Slider_Slide', 'slider_flash'), 'mslider_'. $slug, 'normal', 'default');
			}
		}
	}

	public static function slider_order()
	{
		global $post;
		
		$custom = get_post_custom($post->ID);
		$slider_order = isset($custom['slider_order']) ? $custom['slider_order'][0] : '';
		?>
			<label>Slide Position:</label>
			<input name="slider_order" value="<?php echo $slider_order; ?>" />
		<?php
	}

	public static function slider_link()
	{
		global $post;
		
		$custom = get_post_custom($post->ID);
		$slider_link = isset($custom['slider_link']) ? $custom['slider_link'][0] : '';
		?>
			<label>URL for the slide:</label>
			<input name="slider_link" value="<?php echo $slider_link; ?>" size="75" />
		<?php
	}

	public static function slider_flash()
	{
		global $post;

		$slider_flash_url = get_post_meta($post->ID, '__slider_flash_url', true);
		
		$max_upload = (int)(ini_get('upload_max_filesize'));
		$max_post = (int)(ini_get('post_max_size'));
		$memory_limit = (int)(ini_get('memory_limit'));
		$upload_mb = min($max_upload, $max_post, $memory_limit);
		?>
	        <label for="slider_flash_url">Flash slide url: </label>
	        <input type="text" size="45" name="slider_flash_url" id="slider_flash_url" value="<?php echo $slider_flash_url; ?>" />
			<input type="file" name="slider_flash_file" id="slider_flash_file" />
	        <br />
	        Max upload size: <?php echo $upload_mb; ?>mb<br />
	        <?php if (!empty($_GET['slider_flash_error'])) { echo stripslashes(urldecode($_GET['slider_flash_error'])) .'<br /><br />'; } ?>
		<?php
	}

	public static function save_slide($post_id)
	{
		global $post;

		if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE)
			return;
		
		if (isset($_POST['post_type']) && strstr($_POST['post_type'], 'mslider_') !== false) {
			update_post_meta($post_id, 'slider_order', intval($_POST['slider_order']));
			update_post_meta($post_id, 'slider_link', esc_url($_POST['slider_link']));
		}

		// See if we have a file upload
		if (isset($_FILES) && isset($_FILES['slider_flash_file'])) {
			$slider_flash = $_FILES['slider_flash_file'];

			if (!empty($slider_flash['name'])) {
				require_once(ABSPATH .'wp-admin/includes/file.php');
				$stored = wp_handle_upload($slider_flash, array('test_form' => false));;
				$audio_path = $stored['file'];

				$slider_flash_url = $stored['url'];
			} else if (!empty($_POST['slider_flash_url'])) {
				$slider_flash_url = esc_url_raw($_POST['slider_flash_url']);
			}

			if (!empty($slider_flash_url) && !is_array($slider_flash_url)) {
				update_post_meta($post->ID, '__slider_flash_url', $slider_flash_url);
			}
		}
	}

	public static function show_slides($slug, $return_output = false)
	{
		$slug = sanitize_title_with_dashes($slug);
		
		// Pull up slider settings so we can check our order setting
		$slider_settings = unserialize(get_option('mslider_slide_'. $slug));

		$query_settings = array('post_type' => 'mslider_'. $slug, 'posts_per_page' => 10);

		if (isset($slider_settings['mslider_random_order']) && $slider_settings['mslider_random_order'] == true) {
			$query_settings['orderby'] = 'rand';
		} else {
			$query_settings['order'] = 'ASC';
			$query_settings['orderby'] = 'meta_value_num';
			$query_settings['meta_key'] = 'slider_order';
		}

		$slides = new WP_query($query_settings);
		
		$output = '';
		
		if ($slides->found_posts > 0) {
			if ($slider_settings['pause_on_hover']) {
				$pause = "true";
			} else {
				$pause = "false";
			}

			$output .= "<div class=\"cycle-slideshow mslider mslider_". $slug ." ". $slug ."\" data-cycle-slides=\"> div\" data-cycle-pause-on-hover=\"". $pause ."\" data-cycle-fx=\"". $slider_settings['transition'] ."\" data-cycle-speed=\"". $slider_settings['transition_speed'] ."\" data-cycle-timeout=\"". $slider_settings['delay_time'] ."\">\n";
			
			$counter = 0;
			while ($slides->have_posts()) {
				$slides->the_post();
				
				$counter++;
				
				$slideLink = get_post_custom_values('slider_link');
				$blogurl = get_bloginfo('template_url');
				
				$output .= "	<div id=\"slide_". $counter ."\" class=\"slide_". $slug ." mslider_container\">\n";
				
				$slider_flash_url = get_post_meta(get_the_ID(), '__slider_flash_url', true);

				$thumbnail = get_the_post_thumbnail(get_the_ID(), 'mslider_'. $slug);
				
				$content = get_the_content();
				$content = apply_filters('the_content', $content);
				$content = str_replace(']]>', ']]&gt;', $content);
				
				if (!empty($slider_flash_url)) {
					if (!empty($slideLink[0])) {
						$output .= "		<div class=\"mslider_flashLink\" style=\"width: ". $slider_settings['width'] ."px; height: ". $slider_settings['height'] ."px;\">\n";
						$output .= "			<div onclick=\"window.open('". $slideLink[0] ."', '_blank');\" style=\"cursor: pointer; position: absolute; top: 0; left: 0; width: ". $slider_settings['width'] ."px; height: ". $slider_settings['height'] ."px;\">\n";
						$output .= "				<object type=\"img/gif\">\n";
						$output .= "					<img src=\"". plugins_url("images/null.gif", __FILE__) ."\" width=\"100%\" height=\"100%\" />\n";
						$output .= "				</object>\n";
						$output .= "			</div>\n";
					}

					$output .= "			<object type=\"application/x-shockwave-flash\" data=\"". $slider_flash_url ."\" width=\"". $slider_settings['width'] ."\" height=\"". $slider_settings['height'] ."\">\n";
					$output .= "				<param name=\"movie\" value=\"". $slider_flash_url ."\" />\n";
					$output .= "				<param name=\"wmode\" value=\"transparent\" />\n";
					$output .= "				<param name=\"menu\" value=\"false\" />\n";
					$output .= "				<param name=\"quality\" value=\"best\" />\n";
					$output .= "				<param name=\"wmode\" value=\"Window\" />\n";
					$output .= "				<param name=\"loop\" value=\"false\" />\n";
					$output .= "			</object>\n";

					if (!empty($slideLink[0])) {
						$output .= "		</div>\n";
					}
				} else if (empty($thumbnail) && !empty($content)) {
					$output .= $content;
				} else if (!empty($thumbnail)) {
					if (!empty($slideLink[0])) {
						$output .= "		<a target=\"_blank\" href=\"". $slideLink[0] ."\">". $thumbnail ."</a>\n";
					} else {
						$output .= "		". $thumbnail ."\n";
					}

					$output .= $content;
				}
				
				$output .= "	</div>\n";
			}
			
			wp_reset_postdata();
			
			$output .= "</div>\n";
		}
		
		if ($return_output) {
			return $output;
		} else {
			echo $output;
		}
	}
}

?>