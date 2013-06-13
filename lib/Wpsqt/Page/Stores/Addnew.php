<?php
	/**
	 * Handles doing the Franchisee inserting 
	 * 
	 * @author Iain Cambridge
	 * @copyright Fubra Limited 2010-2011, all rights reserved.
  	 * @license http://www.gnu.org/licenses/gpl.html GPL v3 
  	 * @package WPSQT
	 */ 

class Wpsqt_Page_Stores_Addnew extends Wpsqt_Page {
	
	public function process(){
		
		$this->_pageVars['state'] = "";
		if (isset($_GET["state"])) { $this->_pageVars['state'] = $_GET["state"]; }
		
		$this->_pageView = "admin/store/create.php";
		$this->_doInsert();
		
	}

	
	protected function _doInsert(){
	
		$errors = array();

		if ( $_SERVER['REQUEST_METHOD'] == "POST" ){
	
			if (!isset($_POST['wpsqt_store_location']) || empty($_POST['wpsqt_store_location'])) {
				array_push($errors,"Store Location must be entered!");
			}
			if (!isset($_POST['wpsqt_store_state']) || empty($_POST['wpsqt_store_state'])) {
				array_push($errors,"State must be chosen!");
			}
		
		
			if (empty($errors)) {
				
				// flag chosen user as Franchisee by adding user meta data			
				Wpsqt_System::add_store($_POST['wpsqt_store_location'],$_POST['wpsqt_store_state']);
			
			
//				$this->_pageView ="admin/misc/redirect.php";	
				$this->_pageVars['successMessage'] = "New Store (".$_POST['wpsqt_store_location'].", ".$_POST['wpsqt_store_state'].") added successfully";
//				$this->_pageVars['redirectLocation'] = WPSQT_URL_STORES."&section=addnew";			
			}
		
		}	
		
		$this->_pageVars['errorArray'] = $errors;
		
	}
	
	
}