<?php
require_once WPSQT_DIR.'lib/Wpsqt/Form.php';
require_once WPSQT_DIR.'lib/Wpsqt/Tokens.php';

	/**
	 * Handles building the create/edit quiz form.
	 *
	 * @author Iain Cambridge
	 * @copyright Fubra Limited 2010-2011, All rights reserved.
  	 * @license http://www.gnu.org/licenses/gpl.html GPL v3
  	 * @package WPSQT
	 */

class Wpsqt_Form_Quiz extends Wpsqt_Form {

	public function __construct( array $options = array() ){

		global $blog_id;

		if ( empty($options) ){
			$options = array('name' => false,
							'enabled' => true,
							'permalink' => 0,
							'notificaton_type' => false,
							'limit_one' => false,
							'limit_one_wp' => false,
							'limit_one_cookie' => false,
							'save_resume' => true,
							'question_numbers' => true,
							'timer' => '0',
							'pass_mark' => '100',
							'show_progress_bar' => false,
							'automark_whenfreetext' => 'no',
							'finish_display' => false,			
							'contact' => false,
							'use_wp' => true,
							'email_template' => false,
							'store_results' => 'yes',
							'notification_email' => false,
							'send_user' => 'no',
							'finish_message' => false,
							'pass_finish' => false,
							'pass_finish_message' => false,
							'fail_review' => false);
		}

		$this->addOption("wpsqt_name", "Name", "text", $options['name'], "What you would like the quiz to be called." )
			 ->addOption("wpsqt_enabled", "Enabled", "yesno", $options['enabled'], "Is the module available to users?")
			 ->addOptionA("wpsqt_permalink", "Permalink", "text", $options['permalink'], "ID of the linked WP Page" )
			 ->addOptionA("wpsqt_limit_one", "Limit to one submission per IP","yesno", $options['limit_one'], "Limit the quiz to one submission per IP.")
			 ->addOptionA("wpsqt_limit_one_wp", "Limit to one submission per WP user","yesno",  $options['limit_one_wp'], "Limit the quiz to one submission per WP user. You must have the Use WP Details option below set to yes.")
			 ->addOptionA("wpsqt_limit_one_cookie", "Limit to one submission per computer (using cookies)", "yesno",  $options['limit_one_cookie'], "Limit the quiz to one submission per computer/browser")
			 ->addOptionA("wpsqt_save_resume", "Allow save/resume","yesno",  $options['save_resume'], "Allow save and resume for this quiz?")
			 ->addOptionA("wpsqt_question_numbers", "Display question numbers?","yesno",  $options['question_numbers'], "Select whether you want the numerical question numbers to be displayed next to the text of the question.")
			 ->addOptionA("wpsqt_timer", "Timer value for the quiz","text",  $options['timer'], "Enter the countdown timer value in minutes for the quiz. <b>Enter 0 for no timer</b>")
			 ->addOptionA("wpsqt_pass_mark", "Pass mark", "text",  $options['pass_mark'], "What is the pass mark of this quiz (percentage)?")
			 ->addOptionA("wpsqt_show_progress_bar", "Show progress bar", "yesno",  $options['show_progress_bar'], "Shows a progress bar based on which section the user is on")
			 ->addOptionA("wpsqt_automark_whenfreetxt", "Auto mark when freetext questions", "select",  $options['automark_whenfreetext'], "If the quiz contains free text questions then this option will have the behaviour:<br /><strong>No</strong> - Do not attempt to mark the quiz<br /><strong>Yes - include</strong> - Mark all questions except and mark free texts as incorrect<br /><strong>Yes - exclude</strong> - Mark all questions except free text questions and ignore them from the total count.",array('no','yes - include freetext', 'yes - exclude freetext') )
			 ->addOptionA("wpsqt_finish_display", "Finish Display",'select', $options['finish_display'], "What should be displayed on the finishing of the quiz.", array("Quiz Review","Finish message","Both"))
			 ->addOptionA("wpsqt_send_user", "Send notification email to user as well", "yesno",  $options["send_user"], "Should we send a notification email to the user who took the quiz. You must enable the 'use wordpress details' option below and the use must be logged in for this to work. This is due to a bug in the 'take contact details' option." )
			 ->addOptionA("wpsqt_contact", "Take contact details", "yesno", $options['contact'] ,"This will show a form for users to enter their contact details before proceeding.")
			 ->addOptionA("wpsqt_use_wp", "Use WordPress user details", "yesno", $options['use_wp'], "This will allow you to have the quiz to use the details of the user if they are signed in. If enabled the contact form will not be shown if enabled.")
			 ->addOptionA("wpsqt_notificaton_type", "Complete Notification", "select", $options['notificaton_type'] , "Send a notification email on of completion the quiz by a user.",array('none','instant','instant 100% correct','instant 75% correct','instant 50% correct') )
			 ->addOptionA("wpsqt_email_template", "Custom Email Template", "textarea", $options['email_template'], "The template of the email sent on notification. <strong>If empty the default one will be sent.</strong> <a href=\"#template_tokens\">Click Here</a> to see the tokens that can be used.", array(), false)
			 ->addOptionA("wpsqt_store_results", "Save Results", "yesno",  $options['store_results'], "If the quiz results should be saved.")
			 ->addOptionA("wpsqt_notification_email", "Notification Email", "text",  $options['notification_email'], "The email address which is to be notified when the quiz is completed. Emails can be seperated by a comma. <strong>Will override plugin wide option.</strong>", array(), false )
			 ->addOptionA("wpsqt_finish_message", "Finish Message", "textarea",  $options['finish_message'], "The message to display when the user has successfully finished the quiz. <strong>If empty the default one will be displayed.</strong> <a href=\"#template_tokens\">Click Here</a> to see the tokens that can be used.", array(), false)
			 ->addOptionA("wpsqt_pass_finish", "Different finish message for pass", "yesno", $options['pass_finish'], "Display a different finish message if the user passes?")
			 ->addOptionA("wpsqt_pass_finish_message", "Finish message for pass", "textarea",  $options['pass_finish_message'], "The message to display when the user has passed the quiz. <a href=\"#template_tokens\">Click Here</a> to see the tokens that can be used.", array(), false);

		if ( array_key_exists('id', $options) ){
			$this->addOption("wpsqt_custom_directory", "Custom Directory Location", "static",  WPSQT_DIR."/pages/custom/".$blog_id."/quiz-".$options['id'] ,false,array(),false);
		}

		$this->options = $options;
		apply_filters("wpsqt_form_quiz",$this);

	}

}
