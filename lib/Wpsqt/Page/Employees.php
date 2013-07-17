<?php

	/**
	 * Base page for User/Franchise/Store Management
	 * 
	 * @author Iain Cambridge
	 * @copyright Fubra Limited 2010-2011, all rights reserved.
  	 * @license http://www.gnu.org/licenses/gpl.html GPL v3 
  	 * @package WPSQT
	 */

class Wpsqt_Page_Employees extends Wpsqt_Page {

	public function process(){

		global $wpdb;

		$extraWhere = "";
		
		if (isset($_GET["location"])) {
			$extraWhere = " AND store.location = '".$wpdb->escape($_GET["location"])."'";
		} else if (isset($_GET["state"])) {
			$stateId = Wpsqt_System::getStateId($_GET["state"]);
			$extraWhere = " AND store.state = '".$stateId."'";
		} 
		

		$sql = "SELECT emp.id AS id, user.id AS id_user, user.display_name AS name, store.location, store.state 
			FROM `".WPSQT_TABLE_EMPLOYEES."` emp
			INNER JOIN `".WP_TABLE_USERS."` user ON (emp.id_user = user.id)
			INNER JOIN `".WPSQT_TABLE_STORES."` store ON (emp.id_store = store.id)
			WHERE emp.franchisee = FALSE
			".$extraWhere."
			ORDER BY store.state, store.location";

		$res = $wpdb->get_results( $sql,ARRAY_A);

		// need to convert state numbers to names here
		for($i=0;$i<count($res);$i++) {
			$res[$i]['state'] = Wpsqt_System::getStateName($res[$i]['state']);
			$res[$i]['completion'] = Wpsqt_System::colorCompletionRate(Wpsqt_System::getEmployeeCompletionRate($res[$i]['id_user']));
		}
		$this->_pageVars['franchiseeList'] = $res;

		$this->_pageView = "admin/employees/index.php";
			
	}

}
