<?php
// WP_List_Table is not loaded automatically so we need to load it in our application
if( ! class_exists( 'Employee_List_Table' ) ) {
    require_once( 'EmployeeListTable.php' );
}

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
	
		$customTable = new Employee_List_Table();
		$customTable->setFranchiseOwner();
		$customTable->prepare_items();
		$this->_pageVars['customtable'] = $customTable;

		$this->_pageView = "admin/franchisees/index.php";
			
	}

}


