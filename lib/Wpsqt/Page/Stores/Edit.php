<?php
	/**
	 * Handles doing the Store edits
	 * 
	 * @author Iain Cambridge
	 * @copyright Fubra Limited 2010-2011, all rights reserved.
  	 * @license http://www.gnu.org/licenses/gpl.html GPL v3 
  	 * @package WPSQT
	 */ 

class Wpsqt_Page_Stores_Edit extends Wpsqt_Page {
	
	public function process(){
		global $wpdb;

		if (isset($_GET['action']) && $_GET['action'] == 'delete') { //DELETE
			// delete store clicked
		
			if ( $_SERVER['REQUEST_METHOD'] != "POST" ){
			
				$result = $wpdb->get_row( $wpdb->prepare("SELECT location,state FROM `".WPSQT_TABLE_STORES."` WHERE id = %d",array($_GET['id'])),ARRAY_A);

				$this->_pageVars['title'] = "Store";
				$this->_pageVars['description'] = "Store: ".$result['location'].", ".Wpsqt_System::getStateName($result['state']);
				$this->_pageView = "admin/store/delete.php";
				return;
			}
		
			Wpsqt_System::remove_store($_GET['id']);
	
			$this->_pageView ="admin/misc/redirect.php";					
			$this->_pageVars['redirectLocation'] = WPSQT_URL_STORES;	

		} else { // EDIT
			// select appropriate store row 
			$id = $_GET['id'];
			$sql = "SELECT id,location,state FROM `".WPSQT_TABLE_STORES."` WHERE id='".$id."'";
			$this->_pageVars['store'] = $wpdb->get_results($sql, ARRAY_A);		
				
			$this->_pageView = "admin/store/edit.php";
			$this->_doEdit();
		}
	}
	
	protected function _doEdit(){
	
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
			
			
				$this->_pageView ="admin/misc/redirect.php";	
				
				$this->_pageVars['redirectLocation'] = WPSQT_URL_STORES;			
			}
		
		}	
		
		$this->_pageVars['errorArray'] = $errors;
		
	}
	
	
}