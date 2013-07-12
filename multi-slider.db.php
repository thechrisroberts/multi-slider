<?php
class Multi_Slider_DB
{
	public static function init()
	{
		// Make sure we have our database for click tracking
		register_activation_hook(__FILE__, array('Multi_Slider_DB', 'install_db'));
	}

	public static function install_db() {
		global $wpdb;
		
		$table_name = $wpdb->prefix . "mslider_tracking";
		
		$sql = "CREATE TABLE $table_name (
			slug varchar(20) NOT NULL,
			slide_clicked tinytext NOT NULL,
			visitor tinytext NOT NULL,
			click_time date NOT NULL
		);";
		
		require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
		dbDelta($sql);
	}
}
?>