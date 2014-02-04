<?php

class Wpsqt_Page_Dashboard_Reportstores extends Wpsqt_Export {
	public function output(){
		global $wpdb;
		$this->_pageVars = array();

		$stores = $wpdb->get_results("SELECT DISTINCT id,location,state FROM ".WPSQT_TABLE_STORES, ARRAY_A);

		// need to convert state numbers to names here
		for($i=0;$i<count($stores);$i++) {
			$stores[$i]['state'] = Wpsqt_System::getStateName($stores[$i]['state']);
		}

		//var_dump($stores);

		$fileout = "Store Location, State, Franchisee, Employees".PHP_EOL;
		
		foreach($stores as $store) {
			$fileout .= $store['location'].", ".$store['state'].PHP_EOL;
				// franchisees
				// employees
		
		}

		
		header("Content-disposition: attachment; filename=test.csv");
		header("Content-Type: text/csv");

		echo $fileout;

	}
}
