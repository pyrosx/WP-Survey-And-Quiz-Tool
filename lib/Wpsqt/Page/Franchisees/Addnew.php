<?php
	/**
	 * Handles doing the Franchisee inserting 
	 * 
	 * @author Iain Cambridge
	 * @copyright Fubra Limited 2010-2011, all rights reserved.
  	 * @license http://www.gnu.org/licenses/gpl.html GPL v3 
  	 * @package WPSQT
	 */ 

class Wpsqt_Page_Franchisees_Addnew extends Wpsqt_Page {
	
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

		$this->_pageView = "admin/franchisees/create.php";
		$this->_doInsert();
		
	}

	
	protected function _doInsert(){
		
		$errors = array();


		if ( $_SERVER['REQUEST_METHOD'] == "POST" ){
	
			if (!isset($_POST['wpqst_franchisee_user']) || empty($_POST['wpqst_franchisee_user'])) {
				array_push($errors,"User must be selected");
			}
			if (!isset($_POST['wpqst_franchisee_store']) || empty($_POST['wpqst_franchisee_store'])) {
				array_push($errors,"Store must be selected");
			}
		
		
			if (empty($errors)) {	
				// flag chosen user as Franchisee by adding user meta data			
				Wpsqt_System::add_franchisee($_POST['wpqst_franchisee_user'],$_POST['wpqst_franchisee_store']);
						
				$this->_pageVars['successMessage'] = "New Franchisee added successfully";
			}
					
		}	
		
		$this->_pageVars['errorArray'] = $errors;
		
	}
	
	
}