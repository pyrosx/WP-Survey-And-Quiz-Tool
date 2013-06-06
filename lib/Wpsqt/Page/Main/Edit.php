<?php

	/**
	 * Handles the actual updating of the quiz or survey.
	 * 
	 * @author Iain Cambridge
	 * @copyright Fubra Limited 2011, all rights reserved.
  	 * @license http://www.gnu.org/licenses/gpl.html GPL v3 
  	 * @package WPSQT
	 */

abstract class Wpsqt_Page_Main_Edit extends Wpsqt_Page {
	
	/**
	 * Handles the doing updating of quiz and surveys.
	 * Firstly it gets the quiz or survey details using
	 * either getQuizDetails or getSurveyDetails. Then 
	 * creates the page form using either Wpsqt_Form_Quiz
	 * or Wpsqt_Form_Survey. Then it checks to see if it's
	 * a post request if so it then checks to see if it 
	 * assigns $_POST as the details for the quiz or survey.
	 * At which point it does a validation call to see if
	 * there are any error messages if not it does an update
	 * call using either Wpsqt_System::updateQuizDetails or
	 * Wpsqt_System::updateSurveyDetails. 
	 * 
	 * Uses $this->_subsection to find out if it's to use
	 * Quiz or Survey functions.
	 * 
	 * @since 2.0
	 */
	protected function _doUpdate(){
		
		$this->_pageView = "admin/quiz/create.php";
		$details = Wpsqt_Form::getInsertableArray( 
							Wpsqt_System::getItemDetails($_GET['id'],strtolower($this->_subsection)) 
						);		
		$className ="Wpsqt_Form_".ucfirst($this->_subsection);				
		$objForm = new $className();
		$this->_pageVars = array('objForm' => $objForm,
								 'objTokens' => Wpsqt_Tokens::getTokenObject() );
		
		if ( $_SERVER['REQUEST_METHOD'] == "POST" && !isset($_POST['new-page']) ){
			
			$errorMessages = $objForm->getMessages($_POST);
			$details = $_POST;
			$details['wpsqt_id'] = $_GET['id'];
			unset($details['wpsqt_nonce']);
			
			if ( empty($errorMessages) ) {
			
				// check to see if quiz name has changed
				$newName = wp_strip_all_tags($details['wpsqt_name']);
				global $wpdb;
				$oldName = $wpdb->get_row( 
								$wpdb->prepare("SELECT name FROM ".WPSQT_TABLE_QUIZ_SURVEYS." WHERE id = %d", array($details['wpsqt_id']) ), ARRAY_A
								);
			
				if ($newName != $oldName['name']) {
					//WP page title will need updating
					$post = array(
						'ID' => $details['wpsqt_permalink'],
						'post_title' => $newName,
						'post_name' => Wpsqt_System::format_post_name($newName),
					);
					wp_update_post($post);
					
				}
			
				Wpsqt_System::updateItemDetails(
									Wpsqt_Form::getSavableArray($details),$_GET['subsection']
								);
				do_action('wpsqt_'.strtolower($this->_subsection).'_edit');
				$this->_pageVars['successMessage'] = ucfirst($this->_subsection)." updated!";
			} else {
				$this->_pageVars['errorArray'] = $errorMessages;
			}
			
		} 
		
		$objForm->setValues($details);
	}
	
}