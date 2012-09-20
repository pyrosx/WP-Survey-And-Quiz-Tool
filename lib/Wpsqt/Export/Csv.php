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
		
	public function output(){
		
		$csv = "";
		foreach ( $this->_data as $array ) {
			$csv .= implode(",",$array).PHP_EOL;
		}
		
		return $csv;	
	}		

	public function generate($id) {
		global $wpdb;

		$results = $wpdb->get_results('SELECT * FROM '.WPSQT_TABLE_RESULTS.' WHERE item_id = "'.$id.'"', ARRAY_A);

		/*foreach( $results as $result ){ 
			$csvline =  $result['id'].","; 
			if($result['total'] == 0) {$csvline .= ',,';} else {$csvline .= $result['score'].",".$result['total'].",";}
			if($result['total'] == 0) {$csvline .= ',';} else {$csvline .= $result['percentage']."%,";}
			if ($result['pass'] == 1) {$csvline .= "Pass,";} else {$csvline .= "Fail,";}
			$csvline .= ucfirst($result['status']).","; 
			if (!empty($result['datetaken'])) { $csvline .= date('d-m-y G:i:s',$result['datetaken']); }; 
			$csvlines[] = $csvline;
		} */

	}
		
}