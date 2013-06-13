<?php global $blog_id; ?>
<div class="wrap">

	<?php if ( isset($successMessage) ) {?>
		<div class='updated'><?php echo $successMessage; ?></div>
				
	<?php } ?>
	<div id="icon-tools" class="icon32"></div>
	<h2>
		Online Training - Edit Store
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
	
	
	<form method="POST" action="<?php echo esc_url($_SERVER['REQUEST_URI']); ?>" id="newstore_form">
		<input type="hidden" name="wpsqt_nonce" value="<?php echo WPSQT_NONCE_CURRENT; ?>" />
		<table>
		<!-- Location/Name -->
		<tr><td>Store Location/Name</td><td><input type="text" name="wpsqt_store_location" value="<?php echo $store[0]['location']?>"/></td></tr>
		<!-- State -->
		<tr><td>State</td><td><?php echo (Wpsqt_System::getStateDropdown("wpsqt_store_state", $store[0]['state'])); ?></td></tr>
			
		</table>
			
		<p class="submit">
			<input class="button-primary" type="submit" name="Save" value="Save" id="submitbutton" />
		</p>
		
	</form>
	
	
</div>	
