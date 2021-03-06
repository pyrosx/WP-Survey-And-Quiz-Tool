<?php
if (!function_exists('wp_new_user_notification')) {
	function wp_new_user_notification($user_id, $plaintext_pass = '') {
		$user = get_userdata( $user_id );

		$user_login = stripslashes($user->user_login);
		$user_email = stripslashes($user->user_email);

		// The blogname option is escaped with esc_html on the way into the database in sanitize_option
		// we want to reverse this for the plain text arena of emails.
		$blogname = wp_specialchars_decode(get_option('blogname'), ENT_QUOTES);

		/* removed admin notification... spammy! 
		$message  = sprintf(__('New user registration on your site %s:'), $blogname) . "\r\n\r\n";
		$message .= sprintf(__('Username: %s'), $user_login) . "\r\n\r\n";
		$message .= sprintf(__('E-mail: %s'), $user_email) . "\r\n";

		@wp_mail(get_option('admin_email'), sprintf(__('HI! [%s] New User Registration'), $blogname), $message);
		*/
		
		if ( empty($plaintext_pass) )
			return;

		$message = "Welcome to ".$blogname."\r\n
\r\nPlease find below your login details\r\n";

		$message .= sprintf(__('Username: %s'), $user_email) . "\r\n";
		$message .= sprintf(__('Password: %s'), $plaintext_pass) . "\r\n";
		$message .= "\r\nPlease visit ". home_url() . " to start your training.\r\n";

		wp_mail($user_email, sprintf(__('Welcome to %s'), $blogname), $message);

	}
}
	function wpqst_reminder_email($user_id) {
		$user = get_userdata( $user_id );
		$user_login = stripslashes($user->user_login);
		$user_email = stripslashes($user->user_email);
		$blogname = wp_specialchars_decode(get_option('blogname'), ENT_QUOTES);

		$subject = $blogname." Reminder";

		// TODO make this better!
		$message = "Please complete your training ASAP\r\n
". home_url() . "\r\n
\r\n
In case you've misplaced your username:\r\n";
		// TODO you must complete this blah blah blah
		$message .= sprintf(__('Username: %s'), $user_email) . "\r\n";

		wp_mail($user_email, $subject, $message);
		Wpsqt_System::_log("Standard reminder email, to:".$user_email);
	
	}

	function wpqst_email($user_id, $subject, $message) {
		$user = get_userdata( $user_id );
		$user_login = stripslashes($user->user_login);
		$user_email = stripslashes($user->user_email);
		$blogname = wp_specialchars_decode(get_option('blogname'), ENT_QUOTES);

		// TODO make this better!
		$message .= "\r\n
". home_url() . "\r\n
\r\n
In case you've misplaced your username:\r\n";
		// TODO you must complete this blah blah blah
		$message .= sprintf(__('Username: %s'), $user_email) . "\r\n";

		wp_mail($user_email, $subject, $message);
		Wpsqt_System::_log("Custom email, to:".$user_email);

	}



?>