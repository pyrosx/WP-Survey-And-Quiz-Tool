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

class Wpsqt_Page_Employees extends Wpsqt_Page {

	public function process(){
	
		$customTable = new Employee_List_Table();
		if (isset($_GET['inactive']) && $_GET['inactive'] == 'true') {
			// inactive employees table	
			$customTable->prepare_items(false, true);
		} else {
			// normal employee table
			$customTable->prepare_items(false);
		}
		$this->_pageVars['customtable'] = $customTable;

		$this->_pageView = "admin/employees/index.php";
			
	}

}


