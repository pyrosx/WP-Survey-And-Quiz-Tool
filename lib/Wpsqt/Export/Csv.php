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

	public function saveFile() {
		$path = 'tmp/{$this->filename}.csv';
		file_put_contents(WPSQT_DIR.$path, implode($this->csvLines, "\r\n"));
		return $path;
	}

}