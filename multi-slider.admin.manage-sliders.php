<?php
class Multi_Slider_Admin_Manage
{
	public static function manage_sliders_panel()
	{
		$mslider_slug = isset($_REQUEST['mslider_slug']) ? sanitize_title_with_dashes($_REQUEST['mslider_slug']) : '';
		$mslider_action = isset($_REQUEST['mslider_action']) ? sanitize_title($_REQUEST['mslider_action']) : '';
		$mslider_sliders = get_option('mslider_sliders', array());
		
		if (!is_array($mslider_sliders))
			$mslider_sliders = unserialize($mslider_sliders);
		
		if ($mslider_action == 'delete') {
			echo '<div class="updated"><p><strong>Slider <i>'. $mslider_slug .'</i> has been deleted.</strong></p></div>';
		}
		?>
		
		<div class="wrap">
			<h2>Manage Sliders</h2>
			
			<div class="msliderOptions">
				<?php
				
				if (sizeof($mslider_sliders) == 0) {
					$mslider_add_url = admin_url('admin.php') .'?page=mslider-add-slider';
					echo 'No sliders have been defined. Why not <a href="'. $mslider_add_url .'">add one</a>?<br />';
				} else {
					echo '<div class="msliderOptionLabel">The following sliders have been defined</div>';
					
					foreach($mslider_sliders as $mslider_slider => $mslider_data) {
						echo '<li><a href="admin.php?page=mslider-add-slider&mslider_slug='. urlencode($mslider_slider) .'&multi_slider_action=edit">'. $mslider_data['name'] .'</a>: '. $mslider_data['description'] .'</li>';
					}
				}
				?>
			</div>
		</div>
		<?php
	}
}
?>