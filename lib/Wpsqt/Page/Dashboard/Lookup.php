<?php
// performs a look up for a specified stores table info, and returns the html data, so it can be lazy loaded

$parse_uri = explode( 'wp-content', $_SERVER['SCRIPT_FILENAME'] );
require_once( $parse_uri[0] . 'wp-load.php' );

//require_once WPSQT_DIR.'lib/Wpsqt/System.php';

if (strpos($_SERVER['HTTP_REFERER'],'admin.php') !== false) {
	// admin area/view
	echo Wpsqt_System::getStoreTableSection($_GET['id_store'], null);
} else {
	// user area - franchisee view
	echo Wpsqt_System::getStoreTableSection($_GET['id_store'], get_current_user_id());
}

//		global $wpdb;	
//		$this->_pageVars = array();
//		$this->_pageVars['output'] = Wpsqt_System::getStoreTableSection($_GET['id_store']);
//		$this->_pageView = 'admin/dashboard/lookup.php';


?>
