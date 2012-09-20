<?php

require_once WPSQT_DIR.'lib/Wpsqt/Page/Main/Results.php';

class Wpsqt_Page_Main_Results_Quiz extends Wpsqt_Page_Main_Results {
	
	public function init(){
		if (isset($_GET['export']) && $_GET['export'] == 'csv') {
			require_once WPSQT_DIR.'lib/Wpsqt/Export/Csv.php';
			$csvExporter = new Wpsqt_Export_Csv;

			$csvExporter->quizId = $_GET['id'];
			$csvExporter->generate($_GET['id']);
			$path = $csvExporter->saveFile();

			echo '<iframe src='.plugins_url($path, WPSQT_FILE).' style="display:none;"></iframe>';
			$this->redirect(WPSQT_URL_MAIN.'&section=results&subsection=quiz&id='.$_GET['id']);
		} else {
			$this->_pageView = 'admin/results/index.php';
		}
	}	
	
}

?>