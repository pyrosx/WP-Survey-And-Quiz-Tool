<?php

/**
 * Core class for handling functionality requried throught the plugin.
 *
 * @author Iain Cambridge
 * @copyright Fubra Limited 2010-2011 (c)
 * @license http://www.gnu.org/licenses/gpl.html GPL v3
 */

class Wpsqt_Core {

	protected $_pages = array();

	/**
	 * Adds the generic hooks that are required throughout
	 * the plugin.
	 *
	 * @since 2.0
	 */

	public function __construct(){

		$this->_addPage(WPSQT_PAGE_DASHBOARD, "Online Training", "Online Training", "wpsqt-manage", "Dashboard")
		->_addPage(WPSQT_PAGE_MAIN, "Modules", "Modules", "wpsqt-manage", "Main", WPSQT_PAGE_DASHBOARD)
		->_addPage(WPSQT_PAGE_STORES, 'Stores', 'Stores', 'wpsqt-manage', 'Stores', WPSQT_PAGE_DASHBOARD)
		->_addPage(WPSQT_PAGE_FRANCHISEES, 'Franchisees', 'Franchisees', 'wpsqt-manage', 'Franchisees', WPSQT_PAGE_DASHBOARD)
		->_addPage(WPSQT_PAGE_EMPLOYEES, 'Employees', 'Employees', 'wpsqt-manage', 'Employees', WPSQT_PAGE_DASHBOARD)
//		->_addPage(WPSQT_PAGE_OPTIONS, "Options", "Options", "wpsqt-manage", "Options", WPSQT_PAGE_DASHBOARD)
//		->_addPage(WPSQT_PAGE_MAINTENANCE, 'Maintenance', 'Maintenance', 'wpsqt-manage', 'Maintenance', WPSQT_PAGE_DASHBOARD)
;

		add_action("init",array($this, "create_nonce" ) );

		add_shortcode( 'wpsqt_quiz' , array($this, 'shortcode_quiz') );
		add_shortcode( 'wpsqt_survey' , array($this, 'shortcode_survey') );
		add_shortcode( 'wpsqt' , array($this, 'shortcode') );
		add_shortcode( 'wpsqt_results', array($this, 'shortcode_results') );
		add_shortcode( 'wpsqt_survey_results', array($this, 'shortcode_survey_results') );
		add_shortcode( 'wpsqt_info' , array($this, 'shortcode_info') );
		
		add_shortcode( 'wpsqt_franchisee_tools' , array($this, 'shortcode_franchisee') );

		add_action('init', array($this,"init"));
		add_action('admin_bar_menu', array($this,"adminbar"),999);
		add_action( 'init' , array($this,"enqueue_files"));

		// action to run a function when a user is deleted from wordpress
		add_action('deleted_user',array($this,'remove_employee'));

		// Register the top scores widget
		require_once WPSQT_DIR.'lib/Wpsqt/Widget.php';
		add_action( 'widgets_init', create_function('', 'return register_widget("Wpsqt_Top_Widget");') );
	}


	/**
	 * Quick, easy and neat way to add new pages to
	 * the plugin without having to edit multiple files
	 * in multiple places.
	 *
	 * @param string $id the page identifier
	 * @param string $title The menu title
	 * @param string $pageTitle The Page title.
	 * @param string $cap The capaiblity required to access the menu item.
	 * @param string $module The Module that relates to the class that holds the logic for the page.
	 * @param string|null $parent Parent identifier, if null it is a parent.
	 * @since 2.0
	 * @return Wpsqt_Core
	 */

	protected function _addPage($id,$title,$pageTitle,$cap,$module,$parent = null){

		$this->_pages[$id] = array("title" => $title,
			"page_title" => $pageTitle,
			"capability" => $cap,
			"module" => $module,
			"parent" => $parent);

		return $this;

	}

	/**
	 * Hook to allow people to extend the plugin
	 * using filter to interact with the object.
	 *
	 * @since 2.0
	 */

	public function init(){

		apply_filters("wpsqt_init",$this);

		if ( isset($_SESSION['wpsqt']) ) {
			unset($_SESSION['wpsqt']['current_message']);
		}

		load_plugin_textdomain('wp-survey-and-quiz-tool', false, basename(WPSQT_DIR).'/lang/');
	}

	/**
	 * Adds the WPSQT Menu items to the admin bar.
	 * Because we're cool like that.
	 *
	 * @param WP_Admin_bar $wp_admin_bar
	 */

	public function adminbar( $wp_admin_bar) {

		if ( current_user_can("manage_options") ) {
			foreach ( $this->_pages as $pagevar => $page ){
				$wp_admin_bar->add_menu( array( 
					'title' => $page['title'],
					'href' => admin_url('admin.php?page='.$pagevar), 
					'id' => $pagevar, 
					'parent' => $page['parent']));
			}

		}
	}

	/**
	 * Creates the current nonce and checks to see if a nonce field
	 * has been sent and if so if it is valid.
	 *
	 * @since 2.0
	 */

	public function create_nonce(){

		if ( isset($_REQUEST["wpsqt_nonce"]) ){
			$validNonce = wp_verify_nonce($_REQUEST["wpsqt_nonce"],'wpsqt_nonce');
		} else {
			$validNonce = false;
		}

		define( "WPSQT_NONCE_VALID" , $validNonce );
		define( "WPSQT_NONCE_CURRENT" , wp_create_nonce('wpsqt_nonce') );

	}

	/**
	 * Checks to see if their is a valid nonce if not
	 * then calls wp_die();
	 *
	 * @since 2.0
	 */

	public static function validNonce(){

		if ( WPSQT_NONCE_VALID != true ){
			wp_die("Invalid nonce field, either your session has timed out or someone has tried to trick you with a cross site request.");
		}

	}

	/**
	 * Checks to see if the a custom page view exists
	 * and if so it uses that. Checks the directory of
	 * the quiz or survey's custom pages. If no file
	 * exists checks the shared custom directory else
	 * it returns the location of the default page view.
	 *
	 * Note: All plugin page views can be replaced using
	 * this functionality.
	 *
	 * @param string $file the location of the page view file.
	 * @since 2.0
	 * @return string the location of the page view.
	 */

	public static function pageView($file){

		global $blog_id;

		$quizPath = ( isset($_SESSION['wpsqt']['item_id'])
			&& ctype_digit($_SESSION['wpsqt']['item_id']) ) ?
			$blog_id.'/'.$_SESSION['wpsqt']['current_type'].'-'.$_SESSION['wpsqt']['item_id'].'/' : '';

		if ( file_exists(WPSQT_DIR.'pages/custom/'.$quizPath.$file) ){
			return WPSQT_DIR.'pages/custom/'.$quizPath.$file;
		} elseif (file_exists(WPSQT_DIR.'pages/custom/'.$blog_id.'/shared/'.$file)) {
			return WPSQT_DIR.'pages/custom/'.$blog_id.'/shared/'.$file;
		}
		return WPSQT_DIR.'pages/'.$file;

	}

	/**
	 * Gets the number of page based eon the number
	 * of items there are and the number of items
	 * per page.
	 *
	 * @param integer $numberOfItems the total number of items.
	 * @param integer $itemsPerPage the number of items we want on a page.
	 * @return integer Returns the number of pages required to display $numberOfItems with $itemsPerPages per page
	 * @since 2.0
	 */

	public static function getPaginationCount($numberOfItems,$itemsPerPage){

		if ( $numberOfItems > 0 ){
			$numberOfPages = intval( $numberOfItems / $itemsPerPage );

			if ( $numberOfItems % $itemsPerPage ){
				$numberOfPages++;
			}
		} else {
			$numberOfPages = 0;
		}

		return $numberOfPages;
	}

	/**
	 * Generates a usable Uri for adding new get variables. Allows
	 * for easy page links. Excludes certain get variables from the
	 * uri if they are present in the $exclude array.
	 *
	 * @param array $exclude the items which we want to exclude from the uri.
	 * @since 2.0
	 */

	public static function generateUri( array $exclude = array() ){

		$returnString = $_SERVER['PHP_SELF'].'?';
		if ( !empty($_GET) ){
			foreach ( $_GET as $varName => $varValue ){
				if ( !in_array($varName, $exclude) ){
					$returnString .= $varName.'='.$varValue.'&';
				}
			}
		}

		return $returnString;
	}

	/**
	 * Generates the pagination links. Shows 5 links in total. Two on either side
	 * if possible.
	 *
	 * @return $returnString the html with the links.
	 * @since 2.0
	 */

	public static function getPaginationLinks($currentPage,$numberOfPages){

		$returnString = '';
		$pageUri = self::generateUri( array('pageno') );

		for($i = 1; $i <= $numberOfPages; $i ++) {
			if ($i == $currentPage) {
				$returnString .= ' <a href="'.$pageUri.'pageno='.$i.'" class="page-numbers current">'.$i.'</a>';
			} else {
				$returnString .= ' <a href="'.$pageUri.'pageno='.$i.'" class="page-numbers">'.$i.'</a>';
			}
		}

		return $returnString;
	}

	/**
	 * Returns the integer value of $_GET['pageno'] while ensuring
	 * that it is set and is a number. Otherwise returns 1.
	 *
	 * @since 2.0
	 */

	public static function getCurrentPageNumber(){

		if ( isset($_GET['pageno']) && ctype_digit($_GET['pageno']) ){
			$pageNumber = (int)$_GET['pageno'];
		}
		else{
			$pageNumber = 1;
		}

		return $pageNumber;

	}

	/**
	 * Saves the state of the current quiz to the database
	 *
	 * @param integer $currentStep the key of the section currently on
	 */
	public static function saveCurrentState($currentStep) {
		global $wpdb;


		$quizName = $_SESSION["wpsqt"]["current_id"];
		$quizId = $_SESSION['wpsqt'][$quizName]['details']['id'];
		/*
		// Get all the given answers for all previous sections and stick in an array
		$answersToSave = array();
		foreach ($_SESSION['wpsqt'][$quizName]['sections'] as $key => $section) {
			if (isset($section['answers'])) {
				$answersToSave[$key] = $section['answers'];
			}
		}
		 */

		// Generate uid
		$chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
		$uid = substr(str_shuffle($chars), 0, 20);

		unset($_POST['wpsqt-save-state']);

		$wpdb->insert(WPSQT_TABLE_QUIZ_STATE, array(
			'uid' => $uid,
			'answers' => serialize($_SESSION['wpsqt']),
			'post' => serialize($_POST),
			'quiz_id' => $quizId,
			'current_section' => $currentStep - 1
		));

		// Use JS to store the cookie because headers are almost 100%
		// going to be sent already
?>
<script type="text/javascript">
	function setCookie(c_name,value,exdays) {
		var exdate=new Date();
		exdate.setDate(exdate.getDate() + exdays);
		var c_value=escape(value) + ((exdays==null) ? "" : "; expires="+exdate.toUTCString());
		document.cookie=c_name + "=" + c_value;
	}
	setCookie('wpsqt_<?php echo $quizId; ?>_state', '<?php echo $uid; ?>', '365');
</script>
<?php

		return true;
	}

	/**
	 * Returns the possible locations of class files like Wpsqt_Page and
	 * Wpsqt_Question child classes.
	 *
	 * @param string $className The name of the class that is to be included.
	 * @since 2.0
	 */

	public static function getObject($className , $fatal = true){

		$possibleLocations = array();
		$possibleLocations[] = WPSQT_DIR.'lib/';
		$possibleLocations = apply_filters('wpsqt_plugin_locations',$possibleLocations);

		foreach ( $possibleLocations as $locus ){

			$location = $locus.str_replace(" ", "_", str_replace("_","/",$className)).".php";

			if ( file_exists($location) ){
				require_once $location;
				break;
			}
		}

		if ( !class_exists($className) ){
			if ( $fatal === true ){
				wp_die("No such ".$className." class");
			} else {
				return false;
			}
		}

		$object = new $className();

		return $object;
	}


	public function shortcode_survey( $atts ){

		if ( empty($atts) ){
			return;
		}

		extract( shortcode_atts( array(
					'name' => false
				), $atts) );

		return $this->_shortcode($name, 'survey');

	}

	public function shortcode_quiz( $atts ) {

		if ( empty($atts) ){
			return;
		}

		extract( shortcode_atts( array(
					'name' => false
				), $atts) );

		return $this->_shortcode($name, 'quiz');

	}


	/**
	 * Method for new shortcode that will allow for type handler option.
	 *
	 * @param array $atts
	 * @since 2.2.2
	 */
	public function shortcode($atts) {
		if (empty($atts)) {
			return;
		}

		extract( shortcode_atts( array(
					'id' => false,
					'type' => false
				), $atts) );

		return $this->_shortcode($id, $type);
	}

	/**
	 * DRY method to show return the quizzes and surveys in the correct location.
	 *
	 * @param string $identifer The name or numerical id of the quiz/survey
	 * @param string $type If it is a quiz or a survey.
	 * @since 2.2.2
	 */
	protected function _shortcode($id,$type)	{

		if (isset($_POST['wpsqt_name']) && $_POST['wpsqt_name'] != $id) {
			/* translators: %1$s will be replaced with the quiz name, please leave the HTML in tact */
			printf(__('Another quiz on this page has been started and two quizzes cannot in progress at the same time. In order to start %1$s please <a href="%2$s">click here</a>.'), $identifer, $_SERVER['PHP_SELF']);
			return;
		}

		// Shortcodes only ever work for logged in, assigned user
		if ( is_user_logged_in() ) {
			if ( Wpsqt_System::is_current_user_assigned() ) {
				ob_start();

				require_once WPSQT_DIR.'lib/Wpsqt/Shortcode.php';
				$objShortcode = new Wpsqt_Shortcode($id, $type);
				$objShortcode->display();

				$content = ob_get_contents();
				ob_end_clean();

				return $content;
			} else {
				"<p>Please contact your manager or franchise owner to properly assign your user account to a store</p>";
			}
		} else {
			return "<p>Please login to access training materials</p>";
		}
	}

	public function shortcode_results() {

		$userid = 0;
		$output = "";
		
		if (!empty($_POST['id_user'])) {
			$output .= "<h3>".$_POST['display_name']."</h3>";
			$output .= Wpsqt_System::getResultsTable($_POST['id_user']);
		} else if ( is_user_logged_in() ) {
			$output .= "<h3>Your Results</h3>";
			$output .= Wpsqt_System::getResultsTable(wp_get_current_user()->ID);
		} else {
			$output = "<p>You must be logged in to view your results</p>";
		}
		
		return $output;
	}
	
	
	/*
	Gives an info panel about current module status, with links to quizzes.
	Also shows Certificate link if training has been completed 
	*/
	public function shortcode_info( $atts ) {
		global $wpdb;
								
		if ( is_user_logged_in() ) {
			if ( Wpsqt_System::is_current_user_assigned() ) {
		
				// for each quiz
				$output .= "<h4>My Training Progress</h4>";
				$output .= '<table id="wpsqt_info"><thead><tr><th>Module</th><th>Completion</th></tr></thead><tbody>';
			
				$sql = "SELECT id FROM `".WPSQT_TABLE_QUIZ_SURVEYS."` WHERE enabled = true";
				$quizzes = $wpdb->get_results($sql, 'ARRAY_A');
			
				$completed = true;
				$completed_date = "";
			
				foreach($quizzes as $q) {
					$id = $q['id'];
					$quiz = Wpsqt_System::getItemDetails($id,'quiz');
					$link = Wpsqt_System::format_post_name($quiz['name']);
					$output .= '<tr><td><a href="'.$link.'">'.$quiz['name']."</a></td><td>";
				
					$sql = "SELECT pass,percentage,datetaken FROM ".WPSQT_TABLE_RESULTS." WHERE item_id = '".$quiz['id']."' AND user_id = '".wp_get_current_user()->ID."' ORDER BY percentage DESC LIMIT 1";
					$results = $wpdb->get_results($sql, 'ARRAY_A');
					if (count($results)) {
						if ($results[0]['pass'] != 1 ) {					
							$completed = false;
						}
						$output .= Wpsqt_System::colorCompletionRate($results[0]['percentage']);
					} else {
						$output .= "Not Attempted";
						$completed = false;
					}
					$output .= "</td></tr>";
					
				}
				$output .= "</tbody></table>";
				
			
				if ($completed) {

				// pdf certificate
				// if 100% completed

					$display_name = wp_get_current_user()->user_firstname .' '.wp_get_current_user()->user_lastname;
					if ($display_name == '') {
						$display_name = wp_get_current_user()->display_name;
					}

					$output .= '<form method="POST" action="'.plugins_url('pdf.php',WPSQT_FILE).'">';
					$output .= '<input type="hidden" name="completed_date" value="'.$completed_date.'"/>';
					$output .= '<input type="hidden" name="display_name" value="'.$display_name.'"/>';
					$output .= '<input type="submit" value="Training Completed! Click here to download Your Certificate"/>';
					$output .= '</form>';
											
				}
			} else {
				// valid user, but not assigned to any store - basically, deactivated
				$output = "<p>Your user profile has not been assigned to any Stores - please ask your Manager or Franchise Owner to fix this for you.</p>";
			}				
		} else {
			$output = "<p>Please login to access training materials. If you do not have login details, please contact your Manager or Franchise Owner</p>";
		}
		return $output;	
			
	}

	public function shortcode_survey_results( $atts ) {
		ob_start();
		ob_clean();
		global $wpdb;
		extract( shortcode_atts( array(
					'name' => false
		), $atts) );
		if ($name == false) {
			echo 'No survey name was supplied.';
		} else {
			echo 'Results for '.$name;

			// Get the ID
			$surveyId = $wpdb->get_row("SELECT `id` FROM `".WPSQT_TABLE_QUIZ_SURVEYS."` WHERE `name` = '".$name."'", ARRAY_A);
			$surveyId = (int) $surveyId['id'];

			// Just reuse the same page view that the admin thing uses
			require_once WPSQT_DIR.'/lib/Wpsqt/Page.php';
			require_once WPSQT_DIR.'/lib/Wpsqt/Page/Main/Results/Poll.php';
			Wpsqt_Page_Main_Results_Poll::displayResults($surveyId);
		}
		$contents = ob_get_contents();
		ob_end_clean();
		return $contents;
	}
	
	
	/*
	Checks to see if a User is assigned as a Franchisee for any stores
		if so, Displays helpful info and tools 
	*/
	public function shortcode_franchisee() {
		global $wpdb;
		
		//is user Franchisee?
		if ( is_user_logged_in() ) {
			$id_user = wp_get_current_user()->ID;

			// check if user is a franchisee
			$sql = "SELECT count(id) FROM `".WPSQT_TABLE_EMPLOYEES."` 
					WHERE id_user = ".$id_user." AND franchisee = 1";			

			if ($wpdb->get_var($sql) > 0) {
				$output = "<h4>Franchise Management</h4>";
				$output.= Wpsqt_System::getStoreTable($id_user);		
				return $output;
			}
		}
	}

	public function enqueue_files() {
		wp_enqueue_script("jquery");
		wp_enqueue_script('site',plugins_url('/js/site.js', WPSQT_FILE));
		wp_enqueue_style("wpsqt-main",plugins_url('/css/main.css',WPSQT_FILE));
	}
	
	function remove_employee($id_user) {
		Wpsqt_System::remove_employee($id_user);
	}

}
