<?php
	/**
	 * Handles doing the Franchisee inserting 
	 * 
	 * @author Iain Cambridge
	 * @copyright Fubra Limited 2010-2011, all rights reserved.
  	 * @license http://www.gnu.org/licenses/gpl.html GPL v3 
  	 * @package WPSQT
	 */ 

class Wpsqt_Page_Employees_Certificate extends Wpsqt_Page {
	
	public function process(){
		global $wpdb;
		
		// get data for select boxes
		$this->_pageVars['users'] = Wpsqt_System::getUsersForSelect();
		$this->_pageVars['stores'] = Wpsqt_System::getStoresForSelect();

		if (isset($_GET['id_store'])){
			$this->_pageVars['id_user'] = "";
			$this->_pageVars['id_store'] = $_GET['id_store'];
		} else if (isset($_GET['id_user'])) {
			$this->_pageVars['id_user'] = $_GET['id_user'];
			$this->_pageVars['id_store'] = "";
		} else {
			$this->_pageVars['id_user'] = "";
			$this->_pageVars['id_store'] = "";	
		}

		$this->_pageView = "admin/employees/create.php";
		
	}

	
}