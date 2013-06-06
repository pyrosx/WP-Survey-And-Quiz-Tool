<?php
	/**
	 * Handles doing the inserting 
	 * 
	 * @author Iain Cambridge
	 * @copyright Fubra Limited 2010-2011, all rights reserved.
  	 * @license http://www.gnu.org/licenses/gpl.html GPL v3 
  	 * @package WPSQT
	 */ 

abstract class Wpsqt_Page_Main_Addnew extends Wpsqt_Page {
	
	/**
	 * Handles doing quiz and survey insertions into 
	 * the database. Starts of creating the form object
	 * using either Wpsqt_Form_Quiz or Wpsqt_Form_Survey
	 * then it moves on to check and see if 
	 * 
	 * 
	 * @since 2.0
	 */
	
	protected function _doInsert(){
		
		$className = "Wpsqt_Form_".ucfirst($this->_subsection);
		$objForm = new $className();
		$this->_pageVars = array('objForm' => $objForm,'objTokens' => Wpsqt_Tokens::getTokenObject() );
		
		if ( $_SERVER['REQUEST_METHOD'] == "POST" ){
			
			$errorMessages = $objForm->getMessages($_POST);
			
			$details = $_POST;
			unset($details['wpsqt_nonce']);
			
			if ( empty($errorMessages) ){
				
				// before DB insert, create new wordpress page (content is blank because we don't know quiz ID yet
				$quizName=$details['wpsqt_name'];
				$post = array(
					'post_author' => get_current_user_id(),
					'post_content' => "",
					'post_name' => Wpsqt_System::format_post_name($quizName),
					'post_title' => $quizName,
					'post_status' => 'publish',
					'post_type' => 'page',
				);
				// store the ID of the created page
				$details['wpsqt_permalink'] = wp_insert_post($post);
				$permalink = $details['wpsqt_permalink'];
				
				$details = Wpsqt_Form::getSavableArray($details);
				
				$this->_pageVars['id'] = Wpsqt_System::insertItemDetails($details, strtolower($this->_subsection));
				
				// update wordpress page now that we know ID
				$shortcode = '[wpsqt type="quiz" id="'.$this->_pageVars['id'].'"]';
				$post = array(
					'ID' => $permalink,
					'post_content' => $shortcode,
				);
				wp_update_post($post);				

				do_action('wpsqt_'.strtolower($this->_subsection).'_addnew');
				
				$this->_pageView ="admin/misc/redirect.php";	
				
				$this->_pageVars['redirectLocation'] = WPSQT_URL_MAIN."&section=sections&subsection=".
													   strtolower($this->_subsection)."&id=".$this->_pageVars['id'].
													   "&new=1";
			} else {
				$objForm->setValues($details);
				$this->_pageVars['errorArray'] = $errorMessages;
			}
			
		}
		
	}
	
	
}