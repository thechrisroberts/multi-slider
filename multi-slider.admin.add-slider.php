<?php
class Multi_Slider_Admin_Add
{
	public static function add_slider_panel()
	{
		$mslider_errors = "";
		$mslider_passed_slug = "";
		$mslider_sliderAction = isset($_REQUEST['multi_slider_action']) ? sanitize_title($_REQUEST['multi_slider_action']) : 'add';
		$mslider_sliders = get_option('mslider_sliders', array());
		
		if (!is_array($mslider_sliders)) {
			$mslider_sliders = unserialize($mslider_sliders);
		}
		
		$mslider_slider_settings = array(
										 'name' => '',
										 'slug' => '',
										 'description' => '',
										 'width' => 300,
										 'height' => 200,
										 'transition' => 'fade',
	 									 'transition_speed' => 1000,
										 'delay_time' => 8000,
										 'mslider_random_order' => false,
										 'pause_on_hover' => false,
										 'track_clicks' => false
										);
		
		if (isset($_REQUEST['mslider_original_slug']) || isset($_REQUEST['mslider_slug'])) {
			$mslider_passed_slug = isset($_REQUEST['mslider_original_slug']) ? sanitize_title_with_dashes($_REQUEST['mslider_original_slug']) : sanitize_title_with_dashes($_REQUEST['mslider_slug']);
			$mslider_slider_settings = unserialize(get_option('mslider_slide_'. $mslider_passed_slug));
		}
		
		// Process settings
		if (!empty($_POST) && check_admin_referer('multi_slider_add_settings', 'multi_slider_nonce')) {
			// Load and sanitize values
			$mslider_slider_settings['name'] = isset($_POST['mslider_name']) ? sanitize_text_field($_POST['mslider_name']) : '';
			$mslider_slider_settings['slug'] = isset($_POST['mslider_slug']) ? sanitize_title_with_dashes($_POST['mslider_slug']) : '';
			$mslider_slider_settings['description'] = isset($_POST['mslider_description']) ? $_POST['mslider_description'] : '';
			$mslider_slider_settings['width'] = isset($_POST['mslider_width']) ? intval($_POST['mslider_width']) : '';
			$mslider_slider_settings['height'] = isset($_POST['mslider_height']) ? intval($_POST['mslider_height']) : '';
			$mslider_slider_settings['transition'] = isset($_POST['mslider_transition']) ? sanitize_text_field($_POST['mslider_transition']) : '';
			$mslider_slider_settings['transition_speed'] = isset($_POST['mslider_transition_speed']) ? intval($_POST['mslider_transition_speed']) : '';
			$mslider_slider_settings['delay_time'] = isset($_POST['mslider_delay_time']) ? intval($_POST['mslider_delay_time']) : '';
			$mslider_slider_settings['mslider_random_order'] = isset($_POST['mslider_random_order']) ? intval($_POST['mslider_random_order']) : '';
			$mslider_slider_settings['pause_on_hover'] = isset($_POST['mslider_pause_on_hover']) ? intval($_POST['mslider_pause_on_hover']) : '';
			$mslider_slider_settings['track_clicks'] = isset($_POST['mslider_track_clicks']) ? intval($_POST['mslider_track_clicks']) : '';
			
			/* Check our values */
			
			// Check the slug
			if (strlen($mslider_slider_settings['slug']) > 12)
				$mslider_errors .= 'Slug must be 12 characters or less.<br />';
				
			if ($mslider_slider_settings['slug'] !== sanitize_title($_POST['mslider_slug']))
				$mslider_errors .= 'Slug contains illegal characters. Only letters, numbers, hyphens, and underscores permitted.<br />';
			
			if (strlen($mslider_slider_settings['slug']) < 4)
				$mslider_errors .= 'Slug must be at least four characters long.<br />';
			
			// Make sure the slug is unique
			if ($mslider_sliderAction === 'add' || $mslider_slider_settings['slug'] !== $mslider_passed_slug) {
				if (array_key_exists($mslider_slider_settings['slug'], $mslider_sliders))
					$mslider_errors .= 'Slug name already in use. Please choose another.<br />';
			}
			
			// Check the name. If empty, use slug.
			if (empty($mslider_slider_settings['name']))
				$mslider_slider_settings['name'] = $mslider_slider_settings['slug'];
			
			// Make sure all required ints are specified and > 0
			if ($mslider_slider_settings['width'] < 1)
				$mslider_errors .= 'Slider size width must be a positive value.<br />';
			
			if ($mslider_slider_settings['height'] < 1)
				$mslider_errors .= 'Slider size height must be a positive value.<br />';
			
			if ($mslider_slider_settings['transition_speed'] < 1)
				$mslider_errors .= 'Transition speed must be a positive value.<br />';
			
			if ($mslider_slider_settings['delay_time'] < 1)
				$mslider_errors .= 'Delay time must be a positive value.<br />';
			
			// If no errors, save the slide
			if ($mslider_errors == "") {
				// Are we renaming?
				if ($mslider_sliderAction === 'edit' && $mslider_slider_settings['slug'] !== $mslider_passed_slug) {
					// Remove the old slider slug
					delete_option('mslider_slide_'. $mslider_passed_slug);
					
					// Rename custom post type
					global $wpdb;
					$mslider_update = $wpdb->update('wp_posts', 
													array('post_type' => $mslider_slider_settings['slug']), 
													array('post_type' => $mslider_passed_slug), 
													array('%s'), 
													array('%s')
													);
					
					if ($mslider_update === false) {
						$mslider_errors .= 'An unknown problem occurred when trying to rename existing slides.<br />';
						return;
					}
				}
				
				// Load values into our array and store it
				update_option('mslider_slide_'. $mslider_slider_settings['slug'], serialize($mslider_slider_settings));
				
				// Clear outdated values from the slider array
				unset($mslider_sliders[$mslider_passed_slug]);
				$mslider_sliders[$mslider_slider_settings['slug']]['name'] = $mslider_slider_settings['name'];
				$mslider_sliders[$mslider_slider_settings['slug']]['description'] = $mslider_slider_settings['description'];
				
				update_option('mslider_sliders', serialize($mslider_sliders));
				echo '<div class="updated"><p><strong>Slider <i>'. $mslider_slider_settings['name'] .'</i> has been '. $mslider_sliderAction .'ed.</strong></p></div>';
			}
		}
		
		self::add_form($mslider_sliderAction, $mslider_passed_slug, $mslider_slider_settings, $mslider_errors);
	}

	public static function add_form($mslider_sliderAction, $mslider_passed_slug, $mslider_slider_settings, $mslider_errors)
	{
		?>
		
		<script type="text/javascript">
			jQuery(document).ready(function() {
				jQuery('#mslider_transition').val('<?php echo $mslider_slider_settings['transition']; ?>');
				
				jQuery('#mslider_delete').submit(function() {
					return confirm('Warning: Are you sure you want to delete this slider? It will be removed, along with any slides you have added. This cannot be undone.');
				});
			});
		</script>
		
		<div class="wrap">
			<h2><?php echo ucfirst($mslider_sliderAction); ?> Slider</h2>
			
			<div class="msliderErrors"><?php echo $mslider_errors; ?></div>
			
			<form method="post" action="<?php echo admin_url('admin.php') .'?page=mslider-add-slider'; ?>">
				<?php
				wp_nonce_field('multi_slider_add_settings', 'multi_slider_nonce');
				?>
				
				<input type="hidden" name="multi_slider_action" value="<?php echo $mslider_sliderAction; ?>" />
				<input type="hidden" name="mslider_original_slug" value="<?php echo $mslider_passed_slug; ?>" />
				
				<div class="msliderOptionLabel">Slider Options</div>
				
				<div class="msliderOptions">
					<label for="mslider_name">Name</label> <input type="text" size="30" name="mslider_name" id="mslider_name" value="<?php echo $mslider_slider_settings['name']; ?>" /><br /><br />
					
					<label for="mslider_slug">Slug</label> <input type="text" size="30" name="mslider_slug" id="mslider_slug" value="<?php echo $mslider_slider_settings['slug']; ?>" /><br /><br />
					
					<label for="mslider_description">Description</label> <textarea name="mslider_description" id="mslider_description" rows="6" cols="40"><?php echo $mslider_slider_settings['description']; ?></textarea><br /><br />
					
					<label for="mslider_width">Slider size</label> <input type="text" size="3" name="mslider_width" id="mslider_width" placeholder="width" value="<?php echo $mslider_slider_settings['width']; ?>" /> X <input type="text" size="3" name="mslider_height" id="mslider_height" placeholder="height" value="<?php echo $mslider_slider_settings['height']; ?>" /> (in pixels)<br /><br />
					
					<label for="mslider_transition">Transition</label>
					<select name="mslider_transition" id="mslider_transition">
						<option value="fade">fade</option>
						<option value="fadeout">fadeout</option>
						<option value="scrollHorz">scrollHorz</option>
						<option value="none">none</option>
					</select><br /><br />
					
					<label for="mslider_transition_speed">Transition Speed</label> <input type="text" size="5" name="mslider_transition_speed" id="mslider_transition_speed" value="<?php echo $mslider_slider_settings['transition_speed']; ?>" /> (in milliseconds)<br /><br />
					
					<label for="mslider_delay_time">Delay Time</label> <input type="text" size="5" name="mslider_delay_time" id="mslider_delay_time" value="<?php echo $mslider_slider_settings['delay_time']; ?>" /> (in milliseconds)<br /><br />
					
					<label for="mslider_random_order">Randomize</label> <input type="checkbox" name="mslider_random_order" id="mslider_random_order" value="1" <?php if ($mslider_slider_settings['mslider_random_order']) { echo 'checked="checked"'; }; ?> /> (Will ignore sort order)<br /><br />

					<label for="mslider_pause_on_hover">Pause on hover</label> <input type="checkbox" name="mslider_pause_on_hover" id="mslider_pause_on_hover" value="1" <?php if ($mslider_slider_settings['pause_on_hover']) { echo 'checked="checked"'; }; ?> /><br /><br />
					
					<label for="mslider_track_clicks">Track clicks</label> <input type="checkbox" name="mslider_track_clicks" id="mslider_track_clicks" value="1" <?php if ($mslider_slider_settings['track_clicks']) { echo 'checked="checked"'; }; ?> /><br /><br />
					
					<?php submit_button(); ?>
				</div>
			</form>
			
			<form method="post" id="mslider_delete" action="<?php echo admin_url('admin.php'); ?>">
				<?php
				wp_nonce_field('multi_slider_delete_slider', 'multi_slider_nonce');
				?>
				
				<input type="hidden" name="action" value="mslider_delete_slider" />
				<input type="hidden" name="mslider_slug" value="<?php echo $mslider_passed_slug; ?>" />
				
				<input type="submit" name="mslider_delete" class="button button-primary" value="Delete Slider" />
			</form>
		</div>
		
		<?php
	}
}
?>