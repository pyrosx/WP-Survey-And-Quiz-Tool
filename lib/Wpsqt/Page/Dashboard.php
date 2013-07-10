<?php

class Wpsqt_Page_Dashboard extends Wpsqt_Page {

	public function process(){
		global $wpdb;
		
		$this->_pageVars = array();

		$sql = "SELECT count(id) FROM ".WPSQT_TABLE_STORES;
		$this->_pageVars['numStores'] = $wpdb->get_var($sql);

		$sql = "SELECT count(DISTINCT id_user) FROM ".WPSQT_TABLE_EMPLOYEES." WHERE franchisee = 1";
		$this->_pageVars['numFranchisees'] = $wpdb->get_var($sql);

		$sql = "SELECT count(DISTINCT id_user) FROM ".WPSQT_TABLE_EMPLOYEES." WHERE franchisee = 0";
		$this->_pageVars['numEmployees'] = $wpdb->get_var($sql);
		
		$sql = "SELECT s.id FROM `".WPSQT_TABLE_STORES."` s
				LEFT JOIN ( SELECT * FROM `".WPSQT_TABLE_EMPLOYEES."` WHERE franchisee=true ) e ON e.id_store = s.id
				WHERE e.id is null";
				
		$this->_pageVars['numStoresWithoutFranchisees'] = count($wpdb->get_results($sql));

		$this->_pageView = 'admin/dashboard/index.php';
	}

}

?>