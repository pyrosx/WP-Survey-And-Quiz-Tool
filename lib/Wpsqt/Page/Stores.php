<?php

	/**
	 * Base page for User/Franchise/Store Management
	 * 
	 * @author Iain Cambridge
	 * @copyright Fubra Limited 2010-2011, all rights reserved.
  	 * @license http://www.gnu.org/licenses/gpl.html GPL v3 
  	 * @package WPSQT
	 */

class Wpsqt_Page_Stores extends Wpsqt_Page {

	public function process(){

		global $wpdb;

		$where = "";
		if (isset($_GET["state"])) {
			$where = " WHERE state='".$wpdb->escape($_GET["state"])."' ";
		}
				
		$sql = "SELECT id, location, state 
			FROM `".WPSQT_TABLE_STORES."`
			".$where."
			ORDER BY state, location";

		$this->_pageVars['storeList'] = $wpdb->get_results( $sql,ARRAY_A);
		
		$this->_pageView = "admin/store/index.php";
			
	}

}
