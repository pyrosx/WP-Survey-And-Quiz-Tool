<?php

	/**
	 * Central class for the Create Read Update Delete
	 * of the quizzes and surveys.
	 *
	 *  -- ORIGINAL --
	 * @author Iain Cambridge
	 * @copyright All rights reserved 2010-2011 (c)
	 * @license http://www.gnu.org/licenses/gpl.html GPL v3 
	 * @package WP Survey and Quiz Tool
	 * @since 2.0
	 */ 

class Wpsqt_System {

	/**
	 * Fetches a quiz or survvey from the database. Then
	 * runs it through unserializeDetails to return a
	 * usable array.  
	 * 
	 * @param integer|string $id The ID or name for the quiz or survey in the database.
	 * @param string $type The type of item, generally quiz or survey.
	 * @return array
	 * @since 2.0
	 */
	
	public static function getItemDetails($id,$type){
		
		global $wpdb;
		
		if ( is_int($id) || ctype_digit($id) ){
			$quizRow = $wpdb->get_row( 
								$wpdb->prepare("SELECT * FROM ".WPSQT_TABLE_QUIZ_SURVEYS." WHERE id = %d", array($id) ), ARRAY_A
								);
		} else {
			$quizRow = $wpdb->get_row( 
								$wpdb->prepare("SELECT * FROM ".WPSQT_TABLE_QUIZ_SURVEYS." WHERE name = %s", array($id) ), ARRAY_A
								);
		}
		
		if ( empty($quizRow) ){
			return null;
		}
		
		$quizDetails = self::_unserializeDetails($quizRow,$type);
		
		return $quizDetails;
		
	}

	/**
	 * Fetchs all the items or all the items of a certain 
	 * type. And runs them through self::_unserializeDetails 
	 * to retrive usable arrays. 
	 * 
	 * @param string|boolean $type The type of items to fetch if boolean false then it will fetch all the items in the database.
	 * @return array
	 * @since 2.0
	 */
	public static function getAllItemDetails($type = false){
		
		global $wpdb;
	
		if ( empty($type) ){
			$sql = "SELECT wpsq.*,COUNT(wpar.id) as results
					FROM `".WPSQT_TABLE_QUIZ_SURVEYS."` as wpsq
					LEFT JOIN `".WPSQT_TABLE_RESULTS."` as wpar ON wpar.item_id = wpsq.id
					GROUP BY wpsq.id";
		} else {
			$sql = $wpdb->prepare("SELECT wpsq.*,COUNT(wpar.id) as results
									FROM `".WPSQT_TABLE_QUIZ_SURVEYS."` as wpsq
									LEFT JOIN `".WPSQT_TABLE_RESULTS."` as wpar ON wpar.item_id = wpsq.id
									WHERE wpsq.type = %s
									GROUP BY wpsq.id",
								  array($type) );
		}
		
		$quizRowList = $wpdb->get_results($sql, ARRAY_A); 
		
		$quizList = array();
		
		foreach ($quizRowList as $quizRow ){
			$quizList[] = self::_unserializeDetails($quizRow,$type);
		}
		
		return $quizList;
	}
	
	/**
	 * Updates the items database. Starts off with
	 * turnin $itemsDetails into an savable format.
	 * 
	 * @param array $itemDetails
	 * @param string $type
	 * @return integer
	 * @since 2.0
	 */
	public static function updateItemDetails($itemDetails,$type){
		
		global $wpdb;
		
		list($itemName,$itemId,$itemEnabled,$itemSettings) =  self::_serializeDetails($itemDetails,$type);
		return $wpdb->query( $wpdb->prepare("UPDATE `".WPSQT_TABLE_QUIZ_SURVEYS."` SET `name`=%s, `enabled`=%d, `settings`=%s WHERE `id`=%d", 
								array($itemName,$itemEnabled,$itemSettings,$itemId)) );
		
	}
		
	/**
	 * Inserts the item into the database. Runs them
	 * through self::_serializeDetails to turn $itemDetails 
	 * into a savable format. 
	 * 
	 * @param arrray $itemDetails
	 * @param string $type
	 * @since 2.0
	 */
	public static function insertItemDetails($itemDetails, $type){
	
		global $wpdb;
		
		list($itemName,$itemId,$itemEnabled,$itemSettings) = self::_serializeDetails($itemDetails,$type);
	
		$wpdb->query(
			$wpdb->prepare("INSERT INTO `".WPSQT_TABLE_QUIZ_SURVEYS."` (name,enabled,settings,type) VALUES (%s,%d,%s,%s)",
						   array($itemName,$itemEnabled,$itemSettings,$type)
			)
		);
		
		return $wpdb->insert_id;
	}
	
	/**
	 * Unseralizes the settings and returns an array with all the
	 * quiz details in a single dimensional array. Runs through 
	 * the wpsqt_fetch_{survey|quiz}_details filter before returning.
	 * 
	 * @param array $details The original SQL result/
	 * @param string $type The type of details, 'quiz' or 'survey'
	 * @return array The details unseralized.
	 * @since 2.0
	 */
	public static function _unserializeDetails($row, $type = false){
		
		$details = array(
						'id' => $row['id'],
						'name' => $row['name'],
						'enabled' => $row['enabled'] ? "yes" : "no",
						);
						
		if ( isset($row['results']) ){
			$details['results'] = $row['results'];	
		}		
							
		if ( !empty($row['settings']) 
			&& is_array($settings = unserialize($row['settings'])) ){
				$details = array_merge($details,$settings);
		}					
		$details['type'] = $type;
		if ( !is_bool($type) ){
			$details = apply_filters( 'wpsqt_fetch_'.$type.'_details' , $details );
		}
		
		return $details;
		
	}
	
	/**
	 * Serializes the detials for a quiz or survey so they
	 * can be saved into the database. Runs through 
	 * wpsqt_pre_save_{survey|quiz}_details filter before 
	 * serializing the array.
	 * 
	 * @return array Contains a numerical array containing the name, the ID then the settings value.
	 * @since 2.0
	 */
	
	public static function _serializeDetails($details,$type){
		
		if ( !is_bool($type) ){
			$details = apply_filters ( 'wpsqt_pre_save_'.$type.'_details' , $details );
		}
		
		$quizName = $details['name'];
		$quizId = (isset($details['id'])) ? $details['id'] : 0;
		$quizEnabled = $details['enabled'] == "yes" ? true : false;
		
		unset($details['type']);
		unset($details['id']);
		unset($details['name']);
		unset($details['enabled']);
		
		$quizSettings = serialize($details);
	
		return array($quizName,$quizId,$quizEnabled,$quizSettings);
	
	}
	
	/**
	 * Handles inserting the section 
	 *  
	 * @param integer $itemId
	 * @param string $sectionName
	 * @param integer $sectionCount
	 * @param string $sectionOrder
	 * @uses wpdb
	 * @since 2.0
	 */
	public static function insertSection($itemId, $sectionName, $sectionCount, $sectionOrder, $difficulty ){

		global $wpdb;
		
		$wpdb->query(
			$wpdb->prepare("INSERT INTO `".WPSQT_TABLE_SECTIONS."` (`item_id`,`name`,`limit`,`order`,`difficulty`) VALUES (%d,%s,%d,%s,%s)",
						   array($itemId,$sectionName,$sectionCount,$sectionOrder,$difficulty)	 )
					);
		
		do_action("wpsqt_insert_section",$itemId);
		
		return $wpdb->insert_id;
		
	}
	
	/**
	 * Updates the section with the id $sectionId.
	 * 
	 * @param integer $sectionId
	 * @param string $sectionName
	 * @param integer $sectionCount
	 * @param string $sectionOrder
	 */
	public static function updateSection($sectionId, $sectionName, $sectionCount, $sectionOrder, $difficulty ){
		
		global $wpdb;
				
		$wpdb->query(
			$wpdb->prepare("UPDATE `".WPSQT_TABLE_SECTIONS."` SET `name`=%s,`limit`=%s,`order`=%s,`difficulty`=%s WHERE `id` = %d",
						 	array($sectionName,$sectionCount,$sectionOrder,$difficulty,$sectionId) )
				);
		
		do_action("wpsqt_update_section",$sectionId);		
	}
	
	/**
	 * Delete the section with the id $sectionId.
	 * 
	 * @param integer $sectionId
	 */
	public static function deleteSection($sectionId){
		
		global $wpdb;
		
		$wpdb->query(
			$wpdb->prepare("DELETE FROM `".WPSQT_TABLE_SECTIONS."` WHERE `id` = %d", array($sectionId))
				);
		
		do_action("wpsqt_delete_section",$sectionId);		
	}
	
	/**
	 * Fetchs all the sections for an 
	 * 
	 * @param integer $itemId
	 * @since 2.0
	 */
	public static function fetchSections($itemId){
		
		global $wpdb;	
	
		$sections = $wpdb->get_results(
						$wpdb->prepare("SELECT * FROM `".WPSQT_TABLE_SECTIONS."` WHERE `item_id` = %d",
									    array($itemId)),ARRAY_A
					);
		
		if ( empty($sections) ){
			$sections = array(array('id' => false,'difficulty' => false,'order' => false,'name' => false,'limit' => false));
		}	
		
		$sections = apply_filters("wpsqt_fetch_sections",$sections);			
					
		return $sections;
		
	}
	
	/**
	 * Handles inserting new form items. 
	 * 
	 * @param integer $itemId The id for the quiz or survey.
	 * @param string $name The name/label for the form item.
	 * @param string $type The type of form item it will be.
	 * @param string $required If the form item is required for moving on to actually do the quiz or survey.
	 * @param string $validation The type if any of validation to be used of the form item.
	 * @since 2.0
	 */
	
	public static function insertFormItem($itemId,$name,$type,$required,$validation){
		
		global $wpdb;
		
		$wpdb->query(
				$wpdb->prepare("INSERT INTO `".WPSQT_TABLE_FORMS."` (item_id,name,type,required,validation) 
								VALUES (%d,%s,%s,%s,%s)", 
							    array($itemId,$name,$type,$required,$validation))
				);

		do_action("wpsqt_insert_form",$itemId);		
				
		return $wpdb->insert_id;
	} 
	
	/**
	 * Handles updating the form items.
	 * 
	 * @param integer $formItemId The id that related to the form field.
	 * @param string $name The name/label for the form field.
	 * @param string $type The type of input field.
	 * @param string $required Yes or no if the field is required.
	 * @param string $validation The type of validation to be applied to the field.
	 * @since 2.0
	 */
	public static function updateFormItem($formItemId,$name,$type,$required,$validation){

		global $wpdb;
		
		$wpdb->query(
				$wpdb->prepare("UPDATE `".WPSQT_TABLE_FORMS."` SET name=%s,type=%s,required=%s,validation=%s WHERE id = %d",
							   array($name,$type,$required,$validation,$formItemId))
				);
		do_action("wpsqt_update_form",$formItemId);
				
		return true;
	}
	
	/**
	 * Handles deleting a form item.
	 * 
	 * @param integer $formItemId The id that relateds to the form field id.
	 * @since 2.0
	 */
	public static function deleteFormItem( $formItemId ){
		
		global $wpdb;
		
		$wpdb->query(
				$wpdb->prepare("DELETE FROM `".WPSQT_TABLE_FORMS."` WHERE id = %d", array($formItemId))
				);
		do_action("wpsqt_delete_form",$formItemId);
				
		return true;
	}
	
	/**
	 * Returns an array of validators for the form.
	 * 
	 * @since 2.0
	 */
	
	public static function fetchValidators(){
		
		$validators = array("None","Text","Number","Email"); 
		
		$validators = apply_filters("wpsqt_validators",$validators);
		
		return $validators;
	}
	
	/**
	 * Turns the question array into a savable format. Starts off 
	 * by applying the wpsqt_pre_save_{quiz|survey}_question Then goes
	 * through a foreach loop assigning variables into the output 
	 * array then unsets the it the question array. Then serializes
	 * everything that is lef for the meta data.
	 * 
	 * <code>
	 * list($questionText,$questionType, $questionPoints,
	 * 		$questiionDifficulty,$questionSection,
	 * 		$questionAdditional,$questionMeta) = Wpsqt_System::seralizeQuestion($question,'quiz');
	 * </code>
	 * 
	 * @param array $question
	 * @param string $type
	 * @since 2.0
	 */
	public static function serializeQuestion( $question , $type ){
		
		$question = apply_filters("wpsqt_pre_save_".$type."_question",$question);
		$output = array();
		
		foreach( array("name", "type",
			 		   "difficulty", "section") as $index ){
			$output[] = $question[$index];
			unset($question[$index]);
		}
		unset($question['id']);
		unset($question['nonce']);
		$output[] = serialize($question);
				
		return $output;
		
	}
	
	/**
	 * Turns the savable question data into a unserialized unsable verison,
	 * starts off by cloning the question array then unsetting the meta 
	 * column then does array_merge on the cloned question array and the 
	 * unserialized type. Before returning the value it runs it through the
	 * wpsqt_fetch_save_{survey|quiz}_question filter.
	 * 
	 * @param array $question The raw question data which is to be unserialized.
	 * @param string $type To be used when the filter is applied.
	 * @since 2.0
	 */
	
	public static function unserializeQuestion( $rawQuestion, $type ){
		
		unset($rawQuestion['nonce']);
		$question = $rawQuestion;
		unset($question['meta']);
		$question = array_merge($question,unserialize($rawQuestion['meta']));
		
		return apply_filters("wpsqt_fetch_save_".$type."_question",$question);
		
	}
	
	/**
	 * Returns the questions types that are related to 
	 * quizzes. Runs the filter wpsqt_quiz_question_types.
	 * 
	 * @since 2.0 
	 */
	public static function getQuizQuestionTypes(){
		
		$questions = array('Multiple' => 'Multiple choice question with mulitple correct answers.', 
									  	  'Single' => 'Multiple choice question with a signle correct answer.',
							 			  'Free Text' => 'Question where the user types in the answer into a textarea.' );
		
		return apply_filters('wpsqt_quiz_question_types', $questions );
	} 
	
	/**
	 * Returns the questions types that are related to
	 * surveys. Runs the filter wpsqt_survey_question_types.
	 * 
	 * @since 2.0
	 */	
	public static function getSurveyQuestionTypes(){
		
		$questions = array('Multiple Choice' => 'Multiple choice question with mulitple correct answers.',
									  'Dropdown' => 'Multiple choice question with mulitple correct answers.',
									  'Likert' => '',
									  'Likert Matrix' => 'Displays a matrix of likert scales from 1 to 5',
									  'Free Text' => '');
		
		return apply_filters('wpsqt_survey_question_types', $questions );
		
	}
	
	public static function getPollQuestionTypes(){
		
		$questions = array('Single' => 'Multiple choice question with a signle correct answer.','Multiple' => 'Multiple choice question with mulitple correct answers.');
		
		return apply_filters('wpsqt_survey_question_types', $questions );
		
	}

	/**
	 * Deletes all results from a survey/poll
	 *
	 */
	public static function deleteAllResults($id) {
		global $wpdb;

		$wpdb->query($wpdb->prepare("DELETE FROM `".WPSQT_TABLE_SURVEY_CACHE."` WHERE item_id = %d",
							array($id)));
		$wpdb->query($wpdb->prepare("DELETE FROM `".WPSQT_TABLE_RESULTS."` WHERE item_id = %d",
							array($id)));
	}

	/**
	  *  Formats a quiz name into a usable form for a WP permalink slug
	  */
	public static function format_post_name($name) {
		return preg_replace('/[^a-z0-9]+/i', '_', $name);
	}
	
	
	/**
	State setup: We need the state name -> value to be used in common in multiple places
	
	One Array_A will rule them all!
	
	Originally planned to do tricky 'mod' tricks by storing sets of states as one int, 
	but it didn't end up that way, and now I dont really see the need to change the int values
	*/
	
	private static $StateValueArray = array(
		1 => 'ACT',
		2 => 'New South Wales',
		4 => 'Northern Territory',
		8 => 'Queensland',
		16 => 'South Australia',
		32 => 'Tasmania',
		64 => 'Victoria',
		128 => 'Western Australia'
	);
	
	public static function getStateName($id) {
		
		if (empty(self::$StateValueArray[$id])) {
			return false;
		}
		
		return self::$StateValueArray[$id];
	}

	private static $StateFlipArray;
	public static function getStateId($name) {		
	 	if (self::$StateFlipArray == null) {
	 		self::$StateFlipArray = array_flip(self::$StateValueArray);
	 	}
		return self::$StateFlipArray[$name];
	}

	public static function getStateArray() {
		$arr = self::$StateValueArray; // this should copy the array so it won't be modified accidentally?
		return $arr;
	}
	/**
	  *  Returns a standard "state" drop down box, with $name included
	  */
	public static function getStateDropdown($name, $selected = 0) {
		$out = '<select name="'.$name.'">';
	
		$out .= self::addOption("","",$selected);

/*
		$out .= Wpsqt_System::addOption("ACT","ACT",$selected);
		$out .= Wpsqt_System::addOption("New South Wales","New South Wales",$selected);
		$out .= Wpsqt_System::addOption("Northern Territory","Northern Territory",$selected);
		$out .= Wpsqt_System::addOption("Queensland","Queensland",$selected);
		$out .= Wpsqt_System::addOption("South Australia","South Australia",$selected);
		$out .= Wpsqt_System::addOption("Tasmania","Tasmania",$selected);
		$out .= Wpsqt_System::addOption("Victoria","Victoria",$selected);
		$out .= Wpsqt_System::addOption("Western Australia","Western Australia",$selected);
*/
		
		foreach ( self::$StateValueArray as $id => $val ) {
			$out .= self::addOption($id,$val,$selected);
		}
		
		$out .= '</select>';

		return $out;
	}
	

	public static function addOption($id,$val,$selected = "") {
		$out = '<option value="'.$id.'" ';
		if (strval($id) == strval($selected)) { $out .='selected ';}
		$out .= '>'.$val.'</option>';
		return $out;
	}

	
	/** Returns true if the current wordpress user is properly assigned to a store */
	public static function is_current_user_assigned() {
		global $wpdb;
		
		$userid = get_current_user_id();
		
		return ($wpdb->get_var("SELECT count(id) FROM `".WPSQT_TABLE_EMPLOYEES."` WHERE id_user=".$userid) > 0);
		
//		return true;
	}
	
	/** Adds a new Employee to the employees table
		Returns the id of the added entry, or 0 if there's a problem
	*/
	public static function add_employee($id_user, $id_store, $franchisee = false) {
		global $wpdb;	

		// check user exists
		$wp_user_id = get_user_by('id',$id_user);
		if (!$wp_user_id) {
			return 0;
		}

		//check we're not duplicating
		$sql = $wpdb->prepare("SELECT id, franchisee FROM `".WPSQT_TABLE_EMPLOYEES."` WHERE id_user=%d AND id_store=%d",array($id_user,$id_store));
		$res = $wpdb->get_row($sql,'ARRAY_A');
		if ($res['id'] != 0) {
			// user already assigned to this store
			// if attempting to upgrade franchisee status
			if ($franchisee && !$res['franchisee']) {
				$sql = "UPDATE `".WPSQT_TABLE_EMPLOYEES."` SET franchisee=TRUE WHERE id = ".$res['id'];
				$wpdb->query($sql);
			}
			return $res['id'];
		}
		
		
		$wpdb->query($wpdb->prepare(
			"INSERT INTO `".WPSQT_TABLE_EMPLOYEES."` (id_user,id_store,franchisee) VALUES (%d,%d,%d)",
			array($id_user,$id_store,$franchisee)
		));
		
		return $wpdb->insert_id;
	}
	public static function edit_employee($id,$id_user, $id_store) {
		global $wpdb;	
		$sql = $wpdb->prepare(
			"UPDATE `".WPSQT_TABLE_EMPLOYEES."` SET id_user=%d, id_store=%d WHERE id=%d",
			array($id_user,$id_store,$id)
			);
		
		return $wpdb->get_results($sql, ARRAY_A);
	}
	/** Adds a new Franchisee to the employees table
		Returns the id of the added entry
	*/
	public static function add_franchisee($id_user, $id_store) {
		return self::add_employee($id_user,$id_store,true);
	}

	
	/**
		Call from Franchisee section to remove an employee from the specified store 
	*/
	public static function remove_employee($id_user, $id_store = null) {
		global $wpdb;			
		if ($id_store != null) {
			$sql = $wpdb->prepare("DELETE FROM `".WPSQT_TABLE_EMPLOYEES."` WHERE id_user=%d AND id_store=%d",array($id_user,$id_store));
		} else {
			$sql = $wpdb->prepare("DELETE FROM `".WPSQT_TABLE_EMPLOYEES."` WHERE id_user=%d",array($id_user));
		}
		$wpdb->query($sql);
		self::_log("remove_employee - ".$id_user.", ".$id_store);
	}
	
	/**
		Call from Franchisee section to add a new employee to a store
			- checks email against wordpress users for existing - add's that employee if available
			- if not, creates a new employee and emails details to the specified address 
	*/
	public static function add_user($id_store,$new_name,$new_email,$franchisee = false) {
		global $wpdb;
		$id_user = 0;

		$user = get_user_by('email',$new_email);
		if ($user) {
			// user exists, add employee to store
			$id_user = $user->ID;
			self::_log(array("add user, already exists",$new_name,$new_email,"user id = ".$user->ID,"store = ".$id_store,"franchisee = ".strval($franchisee)));

		} else {
			// new user needs creating
			$random_password = wp_generate_password();
			$id_user = wp_insert_user( array (
				'user_pass' => $random_password,
				'user_name' => $new_email,
				'user_login' => $new_email,
				'user_email' => $new_email,
				'display_name' => $new_name
			) ) ;
			wp_new_user_notification($id_user,$random_password);
			self::_log(array("add user, new user created",$new_name,$new_email,"user id = ".$id_user,"store = ".$id_store,"franchisee = ".strval($franchisee)));
		}

		if ($franchisee) {
			return self::add_franchisee($id_user,$id_store);
		} else {
			return self::add_employee($id_user,$id_store);
		}		
	}
	

	public static function add_store($store, $state) {
		global $wpdb;	

		// make sure $state is valid
		if (!self::getStateName($state)) {
			self::_log("Invalid state, store add - ".$store.", ".$state);
			return false;
		}

		// check we're not duplicating
		if (!is_null($wpdb->get_var($wpdb->prepare('SELECT id FROM `'.WPSQT_TABLE_STORES.'` WHERE location=%s AND state=%s',array($store,$state))))) {
			self::_log("Dupe Store, not added - ".$store.", ".$state);
			return false;
		}

		$sql = $wpdb->prepare("INSERT INTO `".WPSQT_TABLE_STORES."` (location,state) VALUES (%s,%s)",array($store,$state));
		$wpdb->query($sql, ARRAY_A);
		if ($wpdb->insert_id) {
			self::_log("Store added - ".$store.", ".$state);
		}
		return $wpdb->insert_id;
	}
	public static function remove_store($id_store) {
		global $wpdb;
		$wpdb->query("DELETE FROM `".WPSQT_TABLE_STORES."` WHERE id=".$id_store);
	}

	public static function getUsersForSelect() {
		global $wpdb;	
		$sql = "SELECT id, display_name FROM `".WP_TABLE_USERS."` ORDER BY display_name";
		return $wpdb->get_results($sql,ARRAY_A);
	}

	public static function getStoresForSelect() {
		global $wpdb;	
		$sql = "SELECT id, location, state FROM `".WPSQT_TABLE_STORES."` ORDER BY state, location";		
		return $wpdb->get_results($sql,ARRAY_A);
	}
	
	public static function getQuizCount() {
		global $wpdb;
		$sql = "SELECT count(id) FROM `".WPSQT_TABLE_QUIZ_SURVEYS."` WHERE enabled=true";
		return intval($wpdb->get_var($sql));
	}
	
	public static function getEmployeeCount($id_store, $franchisee = false) {
		global $wpdb;	
		$sql = "SELECT count(id) FROM `".WPSQT_TABLE_EMPLOYEES."` WHERE id_store=".$id_store." AND franchisee = ".intval($franchisee);
		return intval($wpdb->get_var($sql));
	}
	public static function getFranchiseeCount($id_store) {
		return self::getEmployeeCount($id_store,true);
	}
	
	public static function getEmployeeCompletionRate($id_employee) {
		global $wpdb;			
		$sql = "SELECT count(id) FROM `".WPSQT_TABLE_RESULTS."`
				WHERE user_id=".$id_employee." AND pass = 1";
		$completions = intval($wpdb->get_var($sql));
		
		$total = self::getQuizCount();
		
		return floatval($completions / $total);
	}

	public static function getStoreCompletionRate($id_store, $includesFranchisees = false) {
		global $wpdb;			
		$sql = "SELECT id_user FROM `".WPSQT_TABLE_EMPLOYEES."` 
				WHERE id_store=".$id_store;
		if (!$includesFranchisees) {
			$sql.=" AND franchisee = 0 ";
		}
		$employees = $wpdb->get_results($sql,'ARRAY_A');
		
		$total = 0;
		$count = 0;
		foreach($employees as $employee) {
			$total += self::getEmployeeCompletionRate($employee['id_user']);
			$count++;
		}
		if ($count <= 0) return 0;
		return floatval($total/$count);
	}
	
	public static function getOverallCompletionRate() {
		global $wpdb;
		
		$stores = $wpdb->get_results("SELECT id FROM `".WPSQT_TABLE_STORES."`",'ARRAY_A');
		$count = 0;
		$total = 0;
		foreach($stores as $store) {
			$total += self::getStoreCompletionRate($store['id'],true);
			$count ++;
		}
		if ($count == 0) { return 0; }
		return floatval($total/$count);
	}
	
	public static function colorCompletionRate($comp) {
		if (is_float($comp) & $comp <= 1) {
			$comp = $comp*100;
		}
		$output = '<span style="color:';
		if ($comp <= 25) {
			$output .= 'red';
		} else if ($comp <= 50) {
			$output .= '#FF4500'; // orangeRed			
		} else if ($comp <= 75) {
			$output .= '#FFA500'; // orange	
		} else if ($comp < 100) {
			$output .= '#8fbf00'; // darkish greeny yellow
		} else {
			$output .= '#007f00'; // dark green
		}
		$output .= '">'.intval($comp).'%</span>';
		return $output;
	}
	
	
	
	public static function getStoreTable($id_user = null) {
		global $wpdb;
		$output = ""; // start output string
		
		// first some error/hacking checking
		// if id_user is null, we have to be inside the wp admin area
		if (is_null($id_user)) {
			if (!is_admin()) {
				return false;
			}
		} else {
			// but if it's set, id_user has to match logged in user
			if ($id_user != get_current_user_id()) {
				return false;
			}
		}
				
		if(!empty($_POST["franchisee_remove_user"])) {
			// jquery handles confirm... and it's already happened
			self::remove_employee($_POST["id_user"],$_POST["id_store"]);
			$output .= '<div class="alert-box success">User Removed</div>';
		} 
		if (!empty($_POST["franchisee_add_user"]) || !empty($_POST["add_franchisee"]) ) {
			// add new user clicked           OR       add franchisee clicked
			$franchisee = false;
			if (!empty($_POST["add_franchisee"])) {
				$franchisee = true;
			}
			if (self::add_user($_POST['id_store'],$_POST['new_name'],$_POST['new_email'],$franchisee) != 0) {							
				$output .= '<div class="alert-box success">User added. Invitation email sent.</div>';
			} else {
				$output .= '<div class="alert-box">An error occurred while adding user</div>';
			}
		}
		if (!empty($_POST["remove_store"])) {
			self::remove_store($_POST['id_store']);
			$output .= '<div class="alert-box success">Store Removed</div>';
		}
		if (!empty($_POST['send_reminder'])) {
			wpqst_reminder_email($_POST['id_user']);
			$output .= '<div class="alert-box success">Reminder sent</div>';
		}
		
		$new_store_display = "none";
		$new_store_button = "block";
		
		if (!empty($_POST["add_store"])) {
			$new_store_display = "block";
			$new_store_button = "none";		
			if (self::add_store($_POST['new_store'], $_POST['new_state']) != false) {
				$output .= '<div class="alert-box success">Store Added</div>';
			} else {
				$output .= '<div class="alert-box">An error occurred while adding Store</div>';
			}
		}
	
		// Stores - optionally, restricted to those that user is assigned as "franchisee" to
		$stores = array();
		$sql = "SELECT store.id, store.location, store.state 
				FROM  `".WPSQT_TABLE_STORES."` store ";
		if (!is_null($id_user)) {	
			$sql .="INNER JOIN `".WPSQT_TABLE_EMPLOYEES."` emp ON store.id = emp.id_store 
					WHERE emp.id_user = ".$id_user." AND emp.franchisee = 1 ";
		}
		$sql .=	"ORDER BY store.state, store.location";
		$stores = $wpdb->get_results($sql, 'ARRAY_A');
				
		$output .= '<table id="franchises"><thead><tr><th>Store</th>';
		if (is_null($id_user)) {
			$output .= '<th>Franchise Owners</th>';
			$colspan = 5;
		} else {
			$colspan = 4;
		}
		$output .= '<th>Employees</th><th>Completion</th><th></th></tr></thead><tbody>';
		
		foreach($stores as $store) {
		
			//make active section stay open after a POST/reload
			$users_style = "none";
			$users_button = "+";
			$new_user_display = "none";
			$new_user_button = "block";
			$new_franc_display = "none";
			$new_franc_button = "block";
			
			if (!empty($_POST['id_store']) && $_POST['id_store']==$store['id']) {
				$users_style = "table-row";
				$users_button = "-";
				if (!empty($_POST['new_name'])) {
					if (!empty($_POST["franchisee_add_user"])) {
						$new_user_display = "block";
						$new_user_button = "none";
					} else if (!empty($_POST["add_franchisee"])) {
						$new_franc_display = "block";
						$new_franc_button = "none";
					}
				}
			}
		
			$output .= "<tr>";
			$output .= '<td><a href="" class="display_user_table" id="store_'.$store['id'].'" >'.$store['location'].", ".self::getStateName($store['state'])."</a></td>";
			if (is_null($id_user)) {
				$output .= "<td>".self::getFranchiseeCount($store['id'])."</td>";
			}
			$output .= "<td>".self::getEmployeeCount($store['id'])."</td>";
			$output .= "<td>".self::colorCompletionRate(self::getStoreCompletionRate($store['id'],is_null($id_user)))."</td>";
			$output .= '<td><input type="submit" value="'.$users_button.'" class="display_user_table button tiny secondary" id="store_'.$store['id'].'" /></td>';
		
			$output .= "</tr>";						
			
			
			// list employees
			$sql1 = "SELECT user.id, user.display_name, user.user_email
					FROM `".WP_TABLE_USERS."` user
					INNER JOIN `".WPSQT_TABLE_EMPLOYEES."` emp on user.id = emp.id_user
					WHERE emp.id_store = ".$store['id']." AND emp.franchisee = ";
					
			$sql2= " ORDER BY user.display_name";
			
			$sql = $sql1."0".$sql2;

			$users = $wpdb->get_results($sql, 'ARRAY_A');

			// extra column to maintain alternate colouring and have users in matching colour...
			$output .= '<tr style="display:none;"><td></td></tr>';
			
			$output .= '<tr class="franchise_users" id="rowstore_'.$store['id'].'" style="display:'.$users_style.';"><td colspan='.$colspan.'>
						<table>';
						
			if (is_null($id_user)) {

				// remove Store Button
				$output .= '<thead><tr><td colspan='.$colspan.'>
					<form action="" method="POST">
						<input type="hidden" name="id_store" class="id_store" value="'.$store['id'].'"/>
						<input type="submit" value="Remove Store" name="remove_store" class="remove_store button tiny secondary"/>
					</form>
				</td></tr></thead>
				<tr><td></td></tr>';

				// display franchisees, and add franchisee option
				
				$sql = $sql1."1".$sql2;
				$franchisees = $wpdb->get_results($sql, 'ARRAY_A');
				
				$output .= '<thead><tr><th colspan='.$colspan.'><i>Franchise Owner(s)</i></th></tr></thead>';
				if(count($franchisees) > 0) {
					$output .= "<thead><tr><th>Name</th><th>Email</th><th>Completion</th><th></th></tr></thead><tbody>"; 
			
					foreach($franchisees as $user) {
						$output .= "<tr><td>".$user['display_name']."</td>";
						$output .= '<td><a href="mailto:'.$user['user_email'].'">'.$user['user_email']."</a></td>";
						$output .= "<td>";
						if (is_null($id_user)) {
							$output .= '<a href="'.WPSQT_URL_EMPLOYEES.'&subsection=results&id_user='.$user['id'].'">';
							$output .= self::colorCompletionRate(self::getEmployeeCompletionRate($user['id']));
							$output .= '</a></td><td>';
						} else {
							$output .= self::colorCompletionRate(self::getEmployeeCompletionRate($user['id']));
							$output .= "</td><td>";
							// Results button
							$output .= '<form action="'.home_url('/results/').'" method="POST">
											<input type="hidden" name="id_user" value="'.$user['id'].'"/>
											<input type="hidden" name="display_name" value="'.$user['display_name'].'"/>
											<input type="submit" value="Results" name="results" class="button tiny secondary"/>
										</form>';
						}
						// Reminder button
						$output .= '<form action="" method="POST">
										<input type="hidden" name="id_store" class="id_store" value="'.$store['id'].'"/>
										<input type="hidden" name="id_user" class="id_user" value="'.$user['id'].'"/>
										<input type="submit" value="Reminder" name="send_reminder" class="button tiny secondary"/>
									</form>';

						// Edit button
						$output .= '<form method="GET" action="'.admin_url('/user-edit.php').'">
							<input type="hidden" name="user_id" value="'.$user['id'].'">
							<input type="submit" value="Edit" class="button tiny secondary"/>
						</form>';

						// Remove button
						$output .= '<form action="" method="POST">
										<input type="hidden" name="id_store" class="id_store" value="'.$store['id'].'"/>
										<input type="hidden" name="id_user" class="id_user" value="'.$user['id'].'"/>
										<input type="submit" value="Remove" name="franchisee_remove_user" class="remove_user button tiny secondary"/>
									</form>';
						$output .= "</td></tr>";
					}
				} else {
					$output .= '<tr><td colspan='.$colspan.'><p>No franchise owners assigned yet</p></td></tr>';
				}

				$output .= '<tr><td colspan='.$colspan.'>
								<input type="submit" value="Add Franchise Owner" class="add_user button tiny secondary" id="fstore_'.$store['id'].'" style="display:'.$new_franc_button.'"/>
								<div class="add_user_area" id="add_fstore_'.$store['id'].'" style="display:'.$new_franc_display.'">
									<form action="" method="POST" class="add_user_form">
										<input type="hidden" name="id_store" class="id_store" value="'.$store['id'].'"/>
									
										<table>
										<thead><tr><td colspan=3>New Franchise Owner:</td></tr></thead>
										<tbody>
										<tr>
											<td>Full Name: <input type="text" name="new_name" required/></td>
											<td>Email: <input type="email" name="new_email" required/></td>
											<td><input type="submit" value="Add Franchise Owner" name="add_franchisee" class="button tiny secondary"/></td>
										</tr>
										</tbody></table>
									</form>
								</div>
							</td></tr></tbody>';					

				$output .= '<thead><tr><th colspan='.$colspan.'><i>Employees</i></th></tr></thead>';
			}
			
			if (count($users) > 0) {
				$output .= "<thead><tr><th>Name</th><th>Email</th><th>Completion</th><th></th></tr></thead><tbody>"; 
			
				foreach($users as $user) {
					$output .= "<tr><td>".$user['display_name']."</td>";
					$output .= '<td><a href="mailto:'.$user['user_email'].'">'.$user['user_email']."</a></td>";

					$output .= "<td>";
					if (is_null($id_user)) {
						$output .= '<a href="'.WPSQT_URL_EMPLOYEES.'&subsection=results&id_user='.$user['id'].'">';
						$output .= self::colorCompletionRate(self::getEmployeeCompletionRate($user['id']));
						$output .= '</a></td><td>';
					} else {
						$output .= self::colorCompletionRate(self::getEmployeeCompletionRate($user['id']));
						$output .= "</td><td>";
						// Results button
						$output .= '<form action="'.home_url('/results/').'" method="POST">
										<input type="hidden" name="id_user" value="'.$user['id'].'"/>
										<input type="hidden" name="display_name" value="'.$user['display_name'].'"/>
										<input type="submit" value="Results" name="results" class="button tiny secondary"/>
									</form>';
					}
					// Reminder button
					$output .= '<form action="" method="POST">
									<input type="hidden" name="id_store" class="id_store" value="'.$store['id'].'"/>
									<input type="hidden" name="id_user" class="id_user" value="'.$user['id'].'"/>
									<input type="submit" value="Send Reminder" name="send_reminder" class="button tiny secondary"/>
								</form>';

					// Edit button
					if (is_null($id_user)) {
						$output .= '<form method="GET" action="'.admin_url('/user-edit.php').'">
							<input type="hidden" name="user_id" value="'.$user['id'].'">
							<input type="submit" value="Edit" class="button tiny secondary"/>
						</form>';
					}
					// Remove button
					$output .= '<form action="" method="POST">
									<input type="hidden" name="id_store" class="id_store" value="'.$store['id'].'"/>
									<input type="hidden" name="id_user" class="id_user" value="'.$user['id'].'"/>
									<input type="submit" value="Remove" name="franchisee_remove_user" class="remove_user button tiny secondary"/>
								</form>';
					$output .= "</td></tr>";
				}
			} else {
				$output .= '<tr><td colspan='.$colspan.'><p>No employees assigned yet</p></td></tr>';
			}

			$output .= '<tr><td colspan='.$colspan.'>
							<input type="submit" value="Add Employee" class="add_user button tiny secondary" id="store_'.$store['id'].'" style="display:'.$new_user_button.'"/>
							<div class="add_user_area" id="add_store_'.$store['id'].'" style="display:'.$new_user_display.'">
								<form action="" method="POST" class="add_user_form">
									<input type="hidden" name="id_store" class="id_store" value="'.$store['id'].'"/>
									
									<table>
									<thead><tr><td colspan=2>New Employee Details: </td></tr></thead>
									<tbody>
									<tr>
										<td>Full Name: <input type="text" name="new_name" required/></td>
										<td>Email: <input type="email" name="new_email" required/></td>
									</tr>
									<tr>
										<td colspan=2>
										<p>New users will added to the system, and will receive a welcome email with a temporary password. Users already in the system will be assigned to this store.</p>
										<center><input type="submit" value="Add Employee" name="franchisee_add_user" class="button tiny secondary"/></center>
										</td>
									</tr>
									</tbody></table>
								</form>
							</div>
						</td></tr>'; 
						
			$output .="</tbody></table>";
		}
		
		if (is_null($id_user)) {
			// add store section
			$output .= '<tr><td colspan='.$colspan.'>
							<input type="submit" value="Add Store" class="add_user button tiny secondary" id="store_new" style="display:'.$new_store_button.'"/>
							<div class="add_user_area" id="add_store_new" style="display:'.$new_store_display.'">
								<form action="" method="POST">
									
									<table>
									<thead><tr><td colspan=3>New Store:</td></tr></thead>
									<tbody>
									<tr>
										<td>Name: <input type="text" name="new_store" required/></td>
										<td>State:'.self::getStateDropdown("new_state").'</td>			
										<td><input type="submit" value="Add Store" name="add_store" class="button tiny secondary"/></td>
									</tr>
									</tbody></table>
								</form>
							</div>
						</td></tr>';
		}
		
		$output .="</tbody></table>";	


		
		return $output;		
	}
	
	public static function getResultsTable($userid) {
		global $wpdb;

		$sql = 'SELECT * FROM `'.WPSQT_TABLE_QUIZ_SURVEYS.'` WHERE enabled = 1';
		$modules = $wpdb->get_results($sql,'ARRAY_A');
				
		$output = '<div id="user_results_table"><table>
						<thead><tr><th>Module</th><th>Best Mark</th><th>Attempts</th><th>Last Attempt</th></tr></thead>
						<tbody>
		';
		foreach($modules as $module) {
			// TODO remove *
			$sql = 'SELECT * FROM `'.WPSQT_TABLE_RESULTS.'` WHERE item_id='.$module['id'].' AND user_id='.$userid.' ORDER BY datetaken';
			$results = $wpdb->get_results($sql,'ARRAY_A');
			
			$bestmark = 0;
			$lastdate = "n/a";
			$count = 0;
			if ($results) {				
				foreach($results as $r) {
					if ($bestmark < $r['percentage']) $bestmark = $r['percentage'];
				}
				$lastdate = date('d-m-Y',$results[0]["datetaken"]);
				
				$count = count($results);
			}
		
			$output .= '<tr>
							<td>'.$module["name"].'</td>
							<td>'.Wpsqt_System::colorCompletionRate($bestmark).'</td>';
			if ($count > 1 ) {
				$output .= '<td><a href="" class="open_results" id="div_'.$module['id'].'">'.$count.'</a></td>';
			} else {
				$output .= '<td>'.$count.'</td>';
			}
			$output .= '	<td>'.$lastdate.'</td>
						</tr>';

			if ($count > 1) {
				// sub-table for attempts if more than 1	
				$output .= '<tr style="display:none;"></tr><tr class="results_div" id="results_div_'.$module['id'].'" style="display:none;"><td colspan=5>
					
						<table><thead><tr><th>Date Taken</th><th>Mark</th><th>Time Spent</th></tr></thead>
						<tbody>';
				foreach($results as $res) {
					$output .= '<tr>
								<td>'.date('d-m-Y',$res["datetaken"]).'</td>
								<td>'.Wpsqt_System::colorCompletionRate($res['percentage']).'</td>
								<td>'.sprintf( "%01.2d:%02.2d", floor( $res['timetaken'] / 60 ), $res['timetaken'] % 60 ).'</td>
								</tr>';
				}
				$output .= '</tbody></table>
					
				</td></tr>';
			}
		}
		

		$output .= "</tbody></table></div>";
		
		return $output;
	}
	
	public function _log($x) {
		
		error_log("----------");
		error_log(wp_get_current_user()->user_email);
		if (is_array($x) || is_object($x)) {
			error_log(print_r($x,true));
		} else {
			error_log($x);
		}
	}
}

