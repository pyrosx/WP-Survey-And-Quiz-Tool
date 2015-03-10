<?php

	/**
	 * Base page for User/Franchise/Store Management
	 * 
	 * @author Iain Cambridge
	 * @copyright Fubra Limited 2010-2011, all rights reserved.
  	 * @license http://www.gnu.org/licenses/gpl.html GPL v3 
  	 * @package WPSQT
	 */

class Wpsqt_Page_Employees_Results extends Wpsqt_Page {

	public function process(){

		$user = get_userdata($_GET['id_user']);
		$this->_pageVars['resultsTable'] = Wpsqt_System::getResultsTable($_GET['id_user']);
		$this->_pageVars['username'] = $user->display_name;
		$this->_pageVars['email'] = $user->user_email;
		
		$this->_pageView = "admin/employees/results.php";
			
	}

}
