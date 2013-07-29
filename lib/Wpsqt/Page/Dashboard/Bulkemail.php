<?php

class Wpsqt_Page_Dashboard_Bulkemail extends Wpsqt_Page {
	public function process(){
		global $wpdb;
		
		$this->_pageVars = array();

		$all = $wpdb->get_col("SELECT DISTINCT id_user FROM ".WPSQT_TABLE_EMPLOYEES);
		$this->_pageVars['countAll'] = count($all);
		
		$franchisees = $wpdb->get_col("SELECT DISTINCT id_user FROM ".WPSQT_TABLE_EMPLOYEES." WHERE franchisee = 1");
		$this->_pageVars['countFranchisees'] = count($franchisees);
		
		
		$unfinished = array();
		$unstarted = array();
											
		foreach($all as $userid) {
			$completion = Wpsqt_System::getEmployeeCompletionRate($userid);
			if ($completion < 1) {
				$unfinished[] = $userid;
								
				if ($completion == 0) {
					$unstarted[] = $userid;
				}
			}
		}
		$this->_pageVars['countUnfinished'] = count($unfinished);
		$this->_pageVars['countUnstarted'] = count($unstarted);

	
		if ($_POST) {
			// button pressed... jquery confirmation also passed
			$recipients = array();

			switch($_POST['toradio']) {
				case "all" :
					$recipients = $all;
					break;
				case "franchisees" :
					$recipients = $franchisees;
					break;
				case "unstarted" :
					$recipients = $unstarted;
					break;
				case "unfinished" :
					$recipients = $unfinished;
					break;
			}
			
			if ($_POST['bodyradio'] == "standard") {
				foreach($recipients as $userid) {
					wpqst_reminder_email($userid);
				}
			} else { // "custom"
				foreach($recipients as $userid) {
					wpqst_email($userid, $_POST["subject"], $_POST["body"]);
				}
			}
							
		}

		$this->_pageView = 'admin/dashboard/bulkemail.php';
	}
}
