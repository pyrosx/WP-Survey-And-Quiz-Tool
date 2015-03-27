<?php
require_once WPSQT_DIR.'lib/Wpsqt/Export.php';

	/**
	 *
	 *
	 * @author Iain Cambridge
	 * @copyright Fubra Limited 2010-2011, all rights reserved.
  	 * @license http://www.gnu.org/licenses/gpl.html GPL v3
  	 * @package WPSQT
	 */

class Wpsqt_Export_Csv extends Wpsqt_Export {

	private $csvLines = array();

	public $quizId;
	public $filename;

	public function output(){

		$csv = "";
		foreach ( $this->_data as $array ) {
			$csv .= implode(",",$array).PHP_EOL;
		}

		return $csv;
	}

	public function generate($id, $header = true) {
		global $wpdb;
		$quizName = $wpdb->get_var('SELECT name FROM '.WPSQT_TABLE_QUIZ_SURVEYS.' WHERE id = '.$id);
		$results = $wpdb->get_results('SELECT * FROM '.WPSQT_TABLE_RESULTS.' WHERE item_id = "'.$id.'"', ARRAY_A);

		$this->filename = "results-{$quizName}-".date("Ymd");

		if ($header) {
			$this->csvLines[] = 'Module Name, Name, Score, Total, Percentage, Pass/Fail, Date';
		}
		foreach( $results as $result ){
			$csvline = $quizName.",";
			$csvline .= $result['person_name'].',';
			if($result['total'] == 0) {$csvline .= ',,';} else {$csvline .= $result['score'].",".$result['total'].",";}
			if($result['total'] == 0) {$csvline .= ',';} else {$csvline .= $result['percentage']."%,";}
			if ($result['pass'] == 1) {$csvline .= "Pass,";} else {$csvline .= "Fail,";}
			if (!empty($result['datetaken'])) { $csvline .= date('d-m-y G:i:s',$result['datetaken']); };
			$this->csvLines[] = $csvline;
		}

		return $this->csvLines;
	}
	
	public function generateAll() {
		global $wpdb;
		

		$quizIds = $wpdb->get_results('SELECT id FROM '.WPSQT_TABLE_QUIZ_SURVEYS.' WHERE enabled = 1',ARRAY_A);
		
		foreach ($quizIds as $quiz) {
			$this->csvLines += $this->generate($quiz['id'],false);	
		}

		$this->filename = "results-ALL-".date("Ymd");

		return $this->csvLines;
	}



	public function generateStoreReport() {
		
		global $wpdb;
		
		$stores = $wpdb->get_results("SELECT id,location,state FROM ".WPSQT_TABLE_STORES." ORDER BY state, location", ARRAY_A);

		// need to convert state numbers to names here
		for($i=0;$i<count($stores);$i++) {
			$stores[$i]['state'] = Wpsqt_System::getStateName($stores[$i]['state']);
		}

		$this->csvLines[] = "State, Store Location, Franchisee, Employees, Email";
		
		foreach($stores as $store) {
			$this->csvLines[] = $store['state'].", ".$store['location'];
			
			// franchisees
			$sql = "SELECT DISTINCT user_email,display_name FROM ".WP_TABLE_USERS." u INNER JOIN ".WPSQT_TABLE_EMPLOYEES." e ON u.id = e.id_user WHERE e.id_store =".$store['id']." AND e.franchisee=1 ORDER BY u.user_login";
			$franchisees = $wpdb->get_results($sql, ARRAY_A);
			foreach($franchisees as $franchisee) {
				$this->csvLines[] = ',,'.$franchisee['display_name'].',,'.$franchisee['user_email'].'';
			}

			// employees
			$sql = "SELECT DISTINCT user_email,display_name FROM ".WP_TABLE_USERS." u INNER JOIN ".WPSQT_TABLE_EMPLOYEES." e ON u.id = e.id_user WHERE e.id_store =".$store['id']." AND e.franchisee=0 ORDER BY u.user_login";
			$users = $wpdb->get_results($sql, ARRAY_A);
			foreach($users as $user) {
				$this->csvLines[] = ',,,'.$user['display_name'].','.$user['user_email'];
			}

		}
		
		$this->filename = "stores-report-ALL-".date("Ymd");

		return $this->csvLines;
	}

	public function generateStoreResultsReport() {
		
		global $wpdb;
		
		$stores = $wpdb->get_results("SELECT id,location,state FROM ".WPSQT_TABLE_STORES." ORDER BY state, location", ARRAY_A);

		// need to convert state numbers to names here
		for($i=0;$i<count($stores);$i++) {
			$stores[$i]['state'] = Wpsqt_System::getStateName($stores[$i]['state']);
		}

		$this->csvLines[] = "State, Store Location, Average Result";
		
		foreach($stores as $store) {
			$this->csvLines[] = $store['state'].", ".$store['location'].", ".Wpsqt_System::getStoreCompletionRate($store['id']);
		}
		
		$this->filename = "stores-report-ALL-".date("Ymd");

		return $this->csvLines;
	}
	
	public function generateStoreUserResultsReport() {
		
		global $wpdb;
		
		$stores = $wpdb->get_results("SELECT id,location,state FROM ".WPSQT_TABLE_STORES." ORDER BY state, location", ARRAY_A);

		// need to convert state numbers to names here
		for($i=0;$i<count($stores);$i++) {
			$stores[$i]['state'] = Wpsqt_System::getStateName($stores[$i]['state']);
		}

		$this->csvLines[] = "State, Store Location, Store Completion (%), Staff Member, Staff Completion (%), Staff Completed Date";
		
		foreach($stores as $store) {
			$this->csvLines[] = $store['state'].", ".$store['location'].",".Wpsqt_System::getStoreCompletionRate($store['id']);
			
			// franchisees
			$sql = "SELECT DISTINCT e.franchisee, u.id, display_name FROM ".WP_TABLE_USERS." u INNER JOIN ".WPSQT_TABLE_EMPLOYEES." e ON u.id = e.id_user WHERE e.id_store =".$store['id']." ORDER BY e.franchisee DESC, u.user_login";
			$results = $wpdb->get_results($sql, ARRAY_A);
			foreach($results as $user) {
				
				$name = $user['display_name'];
				if ($user['franchisee'] == 1) $name .= " (Franchise Owner)";
				
				$compDate = Wpsqt_System::getEmployeeCompletedDate($user['id']);
				if ($compDate == 0) $compDate = "";
				else $compDate = date('d-m-Y',$compDate);
				
				$this->csvLines[] = ',,,'.$name.','.Wpsqt_System::getEmployeeCompletionRate($user['id']).','.$compDate;
			}

		}
		
		$this->filename = "stores-report-ALL-".date("Ymd");

		return $this->csvLines;
	}
	
	public function generateResultsReport($isFull = false) {
	
		global $wpdb;

		$sql = "SELECT DISTINCT user_email,display_name,u.id as id FROM ".WP_TABLE_USERS." u INNER JOIN ".WPSQT_TABLE_EMPLOYEES." e ON u.id = e.id_user ORDER BY u.user_login";
		$users = $wpdb->get_results($sql,ARRAY_A);
	
		$line = "Name,Email,Overall Completion,Date Complete/Last Activity";
		if ($isFull) {
			$line .=",Quiz,Best Result,Question Failed,Answer Given";
		}
		$this->csvLines[] = $line;
	
		$quizzes = $wpdb->get_results("SELECT id,name FROM ".WPSQT_TABLE_QUIZ_SURVEYS." WHERE enabled=1 ORDER BY id",ARRAY_A);
	
		foreach($users as $user) {
			$line = $user['display_name'].",".$user['user_email'].",";
			$percent = Wpsqt_System::getEmployeeCompletionRate($user['id'])*100;
			$line .= $percent."%,";

			$sql = "SELECT datetaken FROM ".WPSQT_TABLE_RESULTS." r INNER JOIN ".WPSQT_TABLE_QUIZ_SURVEYS." q ON r.item_id=q.id WHERE user_id=".$user['id']." AND q.enabled=1 ORDER BY datetaken DESC";
			$results = $wpdb->get_results($sql,ARRAY_A);

			if (count($results) > 0) {
				$lastdate = date('d-m-Y',$results[0]['datetaken']);
			} else {
				$lastdate = "n/a";
			}
			$line .= $lastdate;
			
			$this->csvLines[] = $line;
			
			if ($isFull && $percent > 0) {
				
				foreach($quizzes as $quiz) {
				
					$sql = "SELECT r.id,datetaken,percentage,sections 
						FROM ".WPSQT_TABLE_RESULTS." r 
						INNER JOIN ".WPSQT_TABLE_QUIZ_SURVEYS." q 
						ON r.item_id=q.id 
						WHERE q.enabled=1 AND r.item_id=".$quiz['id']." AND r.user_id=".$user['id']." 
						ORDER BY percentage
						LIMIT 1";
					$results = $wpdb->get_results($sql,ARRAY_A);
					
// 					var_dump($result);
				
					if (count($results) <= 0) {
						$this->csvLines[] = ",,,,".$quiz['name'].",No attempts";
					} else {
						$result = $results[0];
						$line = ",,,".date('d-m-Y',$result['datetaken']).",".str_replace(",","",$quiz['name']).",".$result['percentage']."%,";
						$this->csvLines[] = $line;

						if ($result['percentage']<100 && $result['percentage']>0) {
							// get failed questions
							$sections = unserialize($result['sections']);
						
							foreach($sections[0]['questions'] as $question) {
								$questionId = $question['id'];
								$answer = $sections[0]['answers'][$questionId];

								if($answer['mark'] != "correct") {
									//var_dump($question['name']);
									$line = ",,,,,,".str_replace(",","",$question['name']).",".str_replace(",","",$question['answers'][$answer['given'][0]]['text']);
									$this->csvLines[] = $line;
								}
							}
						}
					}
				}
			}
		}
	
		$this->filename = "results-report-ALL-".date("Ymd");
		return $this->csvLines;
	}
	
	
	
	public function saveFile() {
		$path = 'tmp/{$this->filename}.csv';
		file_put_contents(WPSQT_DIR.$path, implode($this->csvLines, "\r\n"));
		return $path;
	}

}