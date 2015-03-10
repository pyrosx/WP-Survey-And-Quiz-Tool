<?php
	/**
	 * Handles doing the Franchisee inserting 
	 * 
	 * @author Iain Cambridge
	 * @copyright Fubra Limited 2010-2011, all rights reserved.
  	 * @license http://www.gnu.org/licenses/gpl.html GPL v3 
  	 * @package WPSQT
	 */ 

class Wpsqt_Page_Employees_Addnew extends Wpsqt_Page {
	
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
		$this->_doInsert();
		
	}

	
	protected function _doInsert(){
		
		$errors = array();


		if ( $_SERVER['REQUEST_METHOD'] == "POST" ){
	
			if (!isset($_POST['wpqst_franchisee_store']) || empty($_POST['wpqst_franchisee_store'])) {
				array_push($errors,"Store must be selected");
			}
			if ($_POST['wpqst_franchisee_user'] == -1) {
			
				if (!isset($_POST['user_name']) || empty($_POST['user_name'])) {
					array_push($errors,"User's name must be entered");
				}		
				if (!isset($_POST['user_email']) || empty($_POST['user_name'])) {
					array_push($errors,"User's email address must be entered");
				}		
			}
						
			if (empty($errors)) {
				
				if ($_POST['wpqst_franchisee_user'] == -1) {
					$result = Wpsqt_System::add_user($_POST['wpqst_franchisee_store'],$_POST['user_name'],$_POST['user_email'],false);
					if ($result['success']) {
						$this->_pageVars['successMessage'] = $result['message'];
					} else {
						array_push($errors,$result['message']);
					}
				} else {
					if(Wpsqt_System::add_employee($_POST['wpqst_franchisee_user'],$_POST['wpqst_franchisee_store']) == 0) {
						array_push($errors,"The specified user could not be added to the store");
					} else {
						$this->_pageVars['successMessage'] = "Existing User added to Store";
					}
				}	
						
			}
					
		}	
		
		$this->_pageVars['errorArray'] = $errors;
		
	}
	
	
}