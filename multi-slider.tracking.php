<?php
class Multi_Slider_Tracking
{
	public static function init()
	{
		// Ajax handler to log clicks
		add_action('wp_ajax_trackMslider', array('Multi_Slider_Tracking', 'mslider_trackClick'));
		add_action('wp_ajax_nopriv_trackMslider', array('Multi_Slider_Tracking', 'mslider_trackClick'));
	}

	public static function mslider_trackClick()
	{
		global $wpdb;
		
		$mslider_slug = sanitize_text_field($_POST['slug']);
		$slide_clicked = sanitize_text_field($_POST['slide']);
		$visitorIp = $_SERVER['REMOTE_ADDR'];
		
		$wpdb->insert( 
			$wpdb->prefix .'mslider_tracking', 
			array( 
				'slug' => $mslider_slug, 
				'slide_clicked' => $slide_clicked,
				'visitor' => $visitorIp, 
				'click_time' => current_time('mysql', 1)
			), 
			array( 
				'%s', 
				'%s', 
				'%s', 
				'%s'
			) 
		);
		
		die();
	}

	public static function slide_tracking()
	{
		global $wpdb;
		
		?>
		<div class="wrap tradingpub_adtracking">
			<h2>Ad Tracking</h2>
			
			<?php
				// Handle our dates
				// Get the oldest date on record
				$ads_dateFormat = 'F d, Y';
				$ads_jsStringFormat = 'm/d/Y';
				
				if (isset($_GET['adDateRangeStart'])) {
					$ads_oldestJsString = sanitize_text_field($_GET['adDateRangeStart']);
					
					list($adMonth, $adDate, $adYear) = explode('/', $ads_oldestJsString);
					$ads_oldest_ts = mktime(0, 0, 0, $adMonth, $adDate, $adYear);
					$ads_oldest_sql = $adYear ."-". $adMonth ."-". $adDate;
					$ads_oldest = date($ads_dateFormat, $ads_oldest_ts);
				} else {
					$ads_oldestUnixDate = $wpdb->get_var("SELECT UNIX_TIMESTAMP(MIN(click_time)) FROM ". $wpdb->prefix . "mslider_tracking;");
					
					$ads_oldest = date($ads_dateFormat, $ads_oldestUnixDate);
					$ads_oldestJsString = date($ads_jsStringFormat, $ads_oldestUnixDate);
				}
				
				if (isset($_GET['adDateRangeEnd'])) {
					$ads_currentJsString = sanitize_text_field($_GET['adDateRangeEnd']);
					
					list($adMonth, $adDate, $adYear) = explode('/', $ads_currentJsString);
					$ads_current_ts = mktime(0, 0, 0, $adMonth, $adDate, $adYear);
					$ads_current_sql = $adYear ."-". $adMonth ."-". $adDate;
					$ads_current = date($ads_dateFormat, $ads_current_ts);
				} else {
					$ads_current = date($ads_dateFormat);
					$ads_currentJsString = date($ads_jsStringFormat);
				}
				
				// Get the various slides
				$queryVar = "SELECT DISTINCT slug, slide_clicked FROM ". $wpdb->prefix . "mslider_tracking";
				
				if (isset($ads_oldest_sql) && isset($ads_current_sql) && $ads_oldest_sql === $ads_current_sql) {
					$queryVar .= " WHERE click_time = '". $ads_current_sql ."'";
				} else {
					$separation = " WHERE";
					
					if (isset($ads_oldest_sql)) {
						$queryVar .= " WHERE click_time >= '". $ads_oldest_sql ."'";
						$separation = " AND";
					}
					
					if (isset($ads_current_sql)) {
						$queryVar .= $separation ." click_time <= '". $ads_current_sql ."'";
					}
				}
				
				$mslider_set = $wpdb->get_results($queryVar);
				
				if (sizeof($mslider_set) == 0 && !isset($_GET['adDateRangeStart'])) {
					echo '<h3>No clicks have been recorded.</h3>';
				} else {
					?>
					
					<h3>Viewing clicks recorded from <?php echo $ads_oldest; ?> until <?php echo $ads_current ?>.</h3>
					
					<script type="text/javascript">
						jQuery(document).ready(function() {
							jQuery('#adDateRangeStart').datepicker().datepicker('setDate', '<?php echo $ads_oldestJsString; ?>');
							jQuery('#adDateRangeEnd').datepicker().datepicker('setDate', '<?php echo $ads_currentJsString; ?>');
						});
					</script>
					
					Select new date range:
					<form method="get" action="<?php echo admin_url('admin.php'); ?>">
					<input type="hidden" name="page" value="mslider-tracking" />
					
					<label>From <input type="text" id="adDateRangeStart" name="adDateRangeStart"></label> <label>to <input type="text" id="adDateRangeEnd" name="adDateRangeEnd"></label> <input type="submit" value="Go" /><br />
					</form>
					
					<?php
					
					if (sizeof($mslider_set) == 0) {
						echo '<h3>No clicks have been recorded in this date range.</h3>';
					} else {
						foreach ($mslider_set as $mslider_clicks) {
							$mslider_slug = $mslider_clicks->slug;
							$mslider_click = $mslider_clicks->slide_clicked;
							
							$queryVar = "SELECT COUNT(*) FROM ". $wpdb->prefix . "mslider_tracking WHERE slide_clicked = '". $mslider_click ."'";
							
							if (isset($ads_oldest_sql) && isset($ads_current_sql) && $ads_oldest_sql === $ads_current_sql) {
								$queryVar .= " AND click_time = '". $ads_current_sql ."'";
							} else {
								if (isset($ads_oldest_sql)) {
									$queryVar .= " AND click_time >= '". $ads_oldest_sql ."'";
								}
								
								if (isset($ads_current_sql)) {
									$queryVar .= " AND click_time <= '". $ads_current_sql ."'";
								}
							}
							
							$queryVar .= ";";
							$click_count = $wpdb->get_var($queryVar);
							
							?>
							<div class="mslider_trackbox">
								<a href="<?php echo $mslider_click; ?>"><img style="max-width: 175px; max-height: 175px;" src="<?php echo $mslider_click; ?>" /></a><br />
								<?php echo $click_count; ?> clicks<br />
							</div>
							<?php
						}
					}
				}
			?>
		</div>
		<?php
	}
}

?>