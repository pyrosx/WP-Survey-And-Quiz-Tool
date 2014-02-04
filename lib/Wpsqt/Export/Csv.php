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

				$q = 0;
				
				$sql = "SELECT r.id,datetaken,item_id,percentage,sections FROM ".WPSQT_TABLE_RESULTS." r INNER JOIN ".WPSQT_TABLE_QUIZ_SURVEYS." q ON r.item_id=q.id WHERE q.enabled=1 AND user_id=".$user['id']." ORDER BY item_id, percentage ASC";
				$results = $wpdb->get_results($sql,ARRAY_A);
				
				for ($i = 0;$i<count($results);$i++) {
					if ($i==count($results)-1 || $results[$i]['item_id'] != $results[$i+1]['item_id']) {
						// next result is a different quiz id => this is the top result for this quiz

						while ($quizzes[$q]['id'] != $results[$i]['item_id']) {
							// $quizzes $q was skipped, so add "not attempted" line;
							$this->csvLines[] = ",,,,".$quizzes[$q]['name'].",No attempts";
							$q++;
						}
						
						
						
						$line = ",,,".date('d-m-Y',$results[$i]['datetaken']).",".$quizzes[$q]['name'].",".$results[$i]['percentage']."%,";
						$this->csvLines[] = $line;
						if ($results[$i]['percentage']<100 && $results[$i]['percentage']>0) {
							// get failed questions
							$sections = unserialize($results[$i]['sections']);
							
							foreach($sections[0]['questions'] as $question) {
								$questionId = $question['id'];
								$answer = $sections[0]['answers'][$questionId];

								if($answer['mark'] != "correct") {
									//var_dump($question['name']);
									$line = ",,,,,,".$question['name'].",".$question['answers'][$answer['given'][0]]['text'];
									$this->csvLines[] = $line;
								}
							}
						}
						
						// done with this question id now... 
						$q++;

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