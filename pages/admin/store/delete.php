<div class="wrap">

	<div id="icon-tools" class="icon32"></div>
	<h2>Online Training - Delete <?php echo $title; ?></h2>
			
	<form method="post" action="<?php echo esc_url($_SERVER['REQUEST_URI']); ?>">
		<input type="hidden" name="wpsqt_nonce" value="<?php echo WPSQT_NONCE_CURRENT; ?>" />
		<p>Are you sure you want to delete the <?php echo $description; ?></p>
		<p><input type="submit" name="confirm" value="Confirm" class='button-secondary' /></p>
	</form>
	
</div>
