<?php
	/**
	 * Handles doing the Store edits
	 * 
	 * @author Iain Cambridge
	 * @copyright Fubra Limited 2010-2011, all rights reserved.
  	 * @license http://www.gnu.org/licenses/gpl.html GPL v3 
  	 * @package WPSQT
	 */ 

class Wpsqt_Page_Employees_Edit extends Wpsqt_Page {
	
	public function process(){
		global $wpdb;

		if (isset($_GET['action']) && $_GET['action'] == 'remove') { // REMOVE (employee from store)
			// remove clicked
		
			if ( $_SERVER['REQUEST_METHOD'] == "POST" ){

				// POST means confirm button has been pressed
				$wpdb->query($wpdb->prepare("DELETE FROM `".WPSQT_TABLE_EMPLOYEES."` WHERE id = %d", array($_GET['id'])));		

				$this->_pageView ="admin/misc/redirect.php";	
				$this->_pageVars['redirectLocation'] = WPSQT_URL_EMPLOYEES;	

			} else {
				// show confirmation screen
							
				$emp = $wpdb->get_row( $wpdb->prepare("SELECT id_store, id_user FROM `".WPSQT_TABLE_EMPLOYEES."` WHERE id = %d",array($_GET['id'])),ARRAY_A);

				$user = get_user_by('id',$emp['id_user']);
		
				$result = $wpdb->get_row( $wpdb->prepare("SELECT location,state FROM `".WPSQT_TABLE_STORES."` WHERE id = %d",array($emp['id_store'])),ARRAY_A);
				
				$this->_pageVars['title'] = "Employee from Store";
				$this->_pageVars['description'] = "employee:<br/><h3>".$user->display_name."</h3><br/> from Store:<br/><h3>".$result['location'].", ".Wpsqt_System::getStateName($result['state'])."</h3>";
															
				$this->_pageView = "admin/employees/remove.php";
				return;
			}
		
		} else if (isset($_GET['action']) && $_GET['action'] == 'delete') { // DELETE wordpress user 
			
			if ( $_SERVER['REQUEST_METHOD'] == "POST" ){
				// POST... confirm has been pressed

				// remove all store links
				Wpsqt_System::remove_employee($_GET['id_user']);
				// now delete wordpress user
				wp_delete_user($_GET['id_user']);
		
				$this->_pageView ="admin/misc/redirect.php";	
				$this->_pageVars['redirectLocation'] = WPSQT_URL_EMPLOYEES;	
			
			} else {
				// confirmation screen
				$user = get_user_by('id',$_GET['id_user']);

				$this->_pageVars['title'] = "Employee";
				$this->_pageVars['description'] = 'employee:<br/><h3 style="color:red">'.$user->display_name.'</h3><p>This action will permanently remove this user and their results, and cannot be undone.</p>';// add franchisee name and location											
				$this->_pageView = "admin/store/delete.php";
				return;
			}
		
		} else { // EDIT
			// get data for select boxes
			$this->_pageVars['users'] = Wpsqt_System::getUsersForSelect();
			$this->_pageVars['stores'] = Wpsqt_System::getStoresForSelect();

			$id = $_GET['id'];
			// get details for this franchisee link, so selected keyword can be added in the right place
			$sql = "SELECT id_user,id_store FROM `".WPSQT_TABLE_EMPLOYEES."` WHERE id='".$id."'";
			$results = $wpdb->get_results($sql, ARRAY_A);		
		
			$this->_pageVars['id_user'] = $results[0]['id_user'];
			$this->_pageVars['id_store'] = $results[0]['id_store'];
			
			$this->_pageView = "admin/employees/edit.php";
			$this->_doEdit();
		}
	}
	
	protected function _doEdit(){
	
		$errors = array();	

		if ( $_SERVER['REQUEST_METHOD'] == "POST" ){

			if (!isset($_POST['wpqst_franchisee_user']) || empty($_POST['wpqst_franchisee_user'])) {
				array_push($errors,"User must be selected");
			}
			if (!isset($_POST['wpqst_franchisee_store']) || empty($_POST['wpqst_franchisee_store'])) {
				array_push($errors,"Store must be selected");
			}
		
		
			if (empty($errors)) {
				
				Wpsqt_System::edit_employee($_GET['id'],$_POST['wpqst_franchisee_user'],$_POST['wpqst_franchisee_store']);
			
				$this->_pageView ="admin/misc/redirect.php";	
				$this->_pageVars['redirectLocation'] = WPSQT_URL_EMPLOYEES;			
			}
		
		}	
		
		$this->_pageVars['errorArray'] = $errors;
		
	}
	
	
}