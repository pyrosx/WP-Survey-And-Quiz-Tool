<?php global $blog_id; ?>
<div class="wrap">

	<?php if ( isset($successMessage) ) {?>
		<div class='updated'><?php echo $successMessage; ?></div>
				
	<?php } ?>
	<div id="icon-tools" class="icon32"></div>
	<h2>
		Online Training - Send Reminder Email
	</h2>
				
	<?php if ( isset($errorArray) && !empty($errorArray) ) { ?>
	<div class="error">
		<ol class="error">
			<?php foreach($errorArray as $error ){ ?>
				<li><?php echo $error; ?></li>
			<?php } ?>
		</ol>
	</div>
	<?php } ?>

	<form method="POST" action="<?php echo esc_url($_SERVER['REQUEST_URI']); ?>" id="reinvite_form">

		<h4>Click the button below to:</h4>
		<p>Send a reminder email to <br/>
		<b><i><?php echo $user_display;?></i></b> <br/>
		to the email address <br/>
		<b><i><?php echo $user_email?></i></b>
		</p>

		<p class="submit">
			<input class="button-primary" type="submit" name="Reinvite" value="Reset Password and Resend Email" id="submitbutton" />
		</p>
		
	</form>
	
	
</div>	
