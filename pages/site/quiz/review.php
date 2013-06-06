
	<p><font size="+3"><?php _e('Total Points', 'wp-survey-and-quiz-tool'); echo ': '.$_SESSION['wpsqt']['current_score']; ?></font></p>

<?php
    //if passed, link home
	if ($_SESSION['wpsqt']['pass']) {
		echo '<p>Section Passed! <a href='.site_url().'>Home</a></p>';		
	} else {
	// else, link back to 
		echo '<p>This section has not been completed satisfactorily. <a href='.get_page_link().'>Try Again?</a></p>';
	}
?>
