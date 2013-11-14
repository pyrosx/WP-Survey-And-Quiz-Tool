<?php

class Wpsqt_Page_Dashboard extends Wpsqt_Page {

	public function process(){
		global $wpdb;
		
		$this->_pageVars = array();

		if(isset($_GET['resetFailId'])) {
			// reset fail status has been clicked
			$res = $wpdb->get_results($wpdb->prepare(
			"SELECT id FROM `".WPSQT_TABLE_RESULTS."` WHERE user_id = %d AND item_id = %d AND status != 'rejected' ORDER BY datetaken",
			$_GET['resetFailId'],$_GET['resetFailQuiz']),'ARRAY_A');
			
			$wpdb->query("UPDATE `".WPSQT_TABLE_RESULTS."` SET status='rejected' WHERE id = ".$res[0]['id']);

		}


		$sql = "SELECT count(id) FROM ".WPSQT_TABLE_STORES;
		$this->_pageVars['numStores'] = $wpdb->get_var($sql);

		$sql = "SELECT count(DISTINCT id_user) FROM ".WPSQT_TABLE_EMPLOYEES." WHERE franchisee = ";		
		$this->_pageVars['numFranchisees'] = $wpdb->get_var($sql."1");
		$this->_pageVars['numEmployees'] = $wpdb->get_var($sql."0");
		
		$sql = "SELECT count(s.id) FROM `".WPSQT_TABLE_STORES."` s
				LEFT JOIN ( SELECT * FROM `".WPSQT_TABLE_EMPLOYEES."` WHERE franchisee=true ) e ON e.id_store = s.id
				WHERE e.id is null";
				
		$this->_pageVars['numStoresWithoutFranchisees'] = $wpdb->get_var($sql);

		$this->_pageVars['overallCompRate'] = Wpsqt_System::colorCompletionRate( Wpsqt_System::getOverallCompletionRate() );


		$query = $wpdb->get_results(
		"SELECT u.ID, u.user_email, r.item_id, r.pass, r.status FROM `".WPSQT_TABLE_RESULTS."` r
		RIGHT JOIN `".WP_TABLE_USERS."` u ON r.user_id = u.ID", 'ARRAY_A');		
		
		$users = array();
		$fails = 0;
		$failDetails = array();
		
		foreach($query as $res) {
			$failed = false;
			if(!is_null($res['item_id'])) {
				$users[$res['ID']][$res['item_id']]['attempts']++;
				if ($res['pass'] == 0 && $res['status'] != 'rejected') {
					$users[$res['ID']][$res['item_id']]['fails']++;
					if (!$failed && $users[$res['ID']][$res['item_id']]['fails'] >= 5) {
						$failed = true;
					}			
				}
				
				if ($failed) {
					$failDetail['id'] = $res['ID'];
					$failDetail['email'] = $res['user_email'];
					$failDetail['attempts'] = $users[$res['ID']][$res['item_id']]['attempts'];

					$res2 = $wpdb->get_results("SELECT * FROM `".WPSQT_TABLE_EMPLOYEES."` e INNER JOIN `".WPSQT_TABLE_STORES."` s ON s.id = e.id_store WHERE e.id_user = ".$res['ID'],'ARRAY_A');
					$failDetail['store'] = "";
					foreach($res2 as $store) {
					$failDetail['store'] .= $store['location'].", ";
					}
					$failDetail['store'] = substr($failDetail['store'], 0, -2); // chop off last ", "

					$failDetail['id_quiz'] = $res['item_id'];
					$failDetail['quiz'] = $wpdb->get_var("SELECT name FROM `".WPSQT_TABLE_QUIZ_SURVEYS."` WHERE id = ".$res['item_id']);

					$fails++;
					$failDetails[] = $failDetail;

				}
			}
		}
		
		
//		print("<pre>");
//		var_dump($query);
//		var_dump($failDetails);
//		var_dump($users);
//		print("</pre>");
		
		$this->_pageVars['alertOverFailLimit'] = $fails;
		$this->_pageVars['failDetails'] = $failDetails;
/*
		$this->_pageVars['alertEmpNotStarted'] = $notStarted;
		$this->_pageVars['alertLocNoComplete'] = 0;
		$this->_pageVars['alertLocNoStarts'] = 0;
*/
		$this->_pageView = 'admin/dashboard/index.php';
	}

}

?>