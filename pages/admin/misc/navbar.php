<?php
function wpsqt_is_section($section) {
	if ($section == 'results') {
		return ( isset($_GET['section']) && $_GET['section'] == 'results' && $_GET['subsection'] != 'total');
	} else if ($section == 'total') {
		return ( isset($_GET['section']) && $_GET['section'] == 'results' && $_GET['subsection'] == 'total');
	} else {
		return ( isset($_GET['section']) && $_GET['section'] == $section);
	}
}

if ( isset($_GET['id']) ){
	global $wpdb;
	
	$quizName = $wpdb->get_var(
					$wpdb->prepare("SELECT name FROM `".WPSQT_TABLE_QUIZ_SURVEYS."` WHERE id = %s", array($_GET['id']))
							 );
	$quizType = $wpdb->get_var(
					$wpdb->prepare("SELECT type FROM `".WPSQT_TABLE_QUIZ_SURVEYS."` WHERE id = %s", array($_GET['id']))
							 );



	if ($quizType == 'survey' && $_GET['subsection'] == 'total'){
		$subsection = 'survey';
	} else {
		$subsection = $_GET['subsection'];
	}
	
	?>
	<div>
		<ul class="subsubsub">
			<li><strong><?php echo $quizName; ?> :</strong></li> 
			<?php if (isset($subsection)) { ?>
				<li><a href="<?php echo WPSQT_URL_MAIN; ?>&section=edit&subsection=<?php echo urlencode($subsection); ?>&id=<?php echo urlencode($_GET["id"]); ?>"<?php if ( wpsqt_is_section('edit') ) { ?> class="current"<?php }?>>Edit</a> | </li> 
				<li><a href="<?php echo WPSQT_URL_MAIN; ?>&section=sections&subsection=<?php echo urlencode($subsection); ?>&id=<?php echo urlencode($_GET["id"]); ?>"<?php if ( wpsqt_is_section('sections') ) { ?> class="current"<?php }?>>Sections</a> | </li> 
				<li><a href="<?php echo WPSQT_URL_MAIN; ?>&section=questions&subsection=<?php echo urlencode($subsection); ?>&id=<?php echo urlencode($_GET["id"]); ?>"<?php if ( wpsqt_is_section('questions') ) { ?> class="current"<?php }?>>Questions</a> | </li>  
<!--				<li><a href="<?php echo WPSQT_URL_MAIN; ?>&section=form&subsection=<?php echo urlencode($subsection); ?>&id=<?php echo urlencode($_GET["id"]); ?>"<?php if ( wpsqt_is_section('form') ) { ?> class="current"<?php }?>>Form</a> | </li> -->
				<li><a href="<?php echo WPSQT_URL_MAIN; ?>&section=results&subsection=<?php echo urlencode($subsection); ?>&id=<?php echo urlencode($_GET["id"]); ?>"<?php if ( wpsqt_is_section('results') ) { ?> class="current"<?php }?>>Results</a></li> 
				<?php if ($quizType == 'survey') { ?>
					| <li><a href="<?php echo WPSQT_URL_MAIN; ?>&section=results&subsection=total&id=<?php echo urlencode($_GET["id"]); ?>"<?php if ( wpsqt_is_section('total') ) { ?> class="current"<?php }?>>Total Results</a></li> 
				<?php } ?>
			<?php } ?>
			<li style="padding-left: 30px;">Shortcode: <pre style="display: inline;">[wpsqt id="<?php echo $_GET["id"]; ?>" type="<?php echo $quizType; ?>"]</pre></li>
		</ul>

		<div style="clear:both;"></div>
	</div>
	<?php 						 
}?>
