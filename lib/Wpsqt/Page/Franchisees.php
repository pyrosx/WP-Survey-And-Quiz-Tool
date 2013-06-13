<?php

	/**
	 * Base page for User/Franchise/Store Management
	 * 
	 * @author Iain Cambridge
	 * @copyright Fubra Limited 2010-2011, all rights reserved.
  	 * @license http://www.gnu.org/licenses/gpl.html GPL v3 
  	 * @package WPSQT
	 */

class Wpsqt_Page_Franchisees extends Wpsqt_Page {

	public function process(){

		global $wpdb;

		$extraWhere = "";
		
		if (isset($_GET["location"])) {
			$extraWhere = " AND store.location = '".$wpdb->escape($_GET["location"])."'";
		} else if (isset($_GET["state"])) {
			$extraWhere = " AND store.state = '".$wpdb->escape($_GET["state"])."'";
		} 
		

		$sql = "SELECT emp.id, user.display_name As name, store.location, store.state 
			FROM `".WPSQT_TABLE_EMPLOYEES."` emp
			INNER JOIN `".WP_TABLE_USERS."` user ON (emp.id_user = user.id)
			INNER JOIN `".WPSQT_TABLE_STORES."` store ON (emp.id_store = store.id)
			WHERE emp.franchisee = TRUE
			".$extraWhere."
			ORDER BY store.state, store.location";
			
		
		$this->_pageVars['franchiseeList'] = $wpdb->get_results( $sql,ARRAY_A);
		
		$this->_pageView = "admin/franchisees/index.php";
			
	}

}
