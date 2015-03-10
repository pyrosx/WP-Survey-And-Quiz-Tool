<?php
	/**
	 * Resets password for a user, and resends the Wordpress Invitation email
	 * 
	 * @author Iain Cambridge
	 * @copyright Fubra Limited 2010-2011, all rights reserved.
  	 * @license http://www.gnu.org/licenses/gpl.html GPL v3 
  	 * @package WPSQT
	 */ 

class Wpsqt_Page_Franchisees_Reinvite extends Wpsqt_Page {
	
	public function process(){
		global $wpdb;
		
		$user = get_user_by('id',$_GET['id_user']);
		
		$this->_pageVars['id_user'] = $_GET['id_user'];
		$this->_pageVars['user_display'] = $user->display_name;
		$this->_pageVars['user_email'] = $user->user_email;
		$this->_pageView = "admin/employees/reinvite.php";

		if ($_POST && $_POST['Reinvite']) {

			// confirm button pressed
			Wpsqt_System::reset_user_password($_GET['id_user']);

			$this->_pageView ="admin/misc/redirect.php";	
			$this->_pageVars['redirectLocation'] = WPSQT_URL_FRANCHISEES;	
		}			
	}
}