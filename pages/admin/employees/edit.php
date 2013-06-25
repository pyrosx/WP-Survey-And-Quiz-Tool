<?php global $blog_id; ?>
<div class="wrap">

	<?php if ( isset($successMessage) ) {?>
		<div class='updated'><?php echo $successMessage; ?></div>
				
	<?php } ?>
	<div id="icon-tools" class="icon32"></div>
	<h2>
		Online Training - Edit Employee
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
	
	
	<form method="POST" action="<?php echo esc_url($_SERVER['REQUEST_URI']); ?>" id="editfranchisee_form">
		<input type="hidden" name="wpsqt_nonce" value="<?php echo WPSQT_NONCE_CURRENT; ?>" />
		<table>
			<!-- User - select box (OR brand new user) -->
			<tr><td>User</td>
			<td>
				<select name="wpqst_franchisee_user">		
					echo Wpsqt_System::addOption("",""); 		
					<?php 
					foreach($users as $user) {
						echo Wpsqt_System::addOption($user['id'],$user['display_name'],$id_user);
					}?>
				</select>
			</td>
			</tr>
			<!-- Store - select box (OR new store) -->
			<tr><td>Store</td>
			<td>
				<select name="wpqst_franchisee_store">
					echo Wpsqt_System::addOption("",""); 
					<?php 
					foreach($stores as $store) {
						echo Wpsqt_System::addOption($store['id'],$store['location'].", ".Wpsqt_System::getStateName($store['state']),$id_store);
					}?>
				</select>
			</td>
			</tr>
			
		</table>	
		
		<p class="submit">
			<input class="button-primary" type="submit" name="Save" value="Save" id="submitbutton" />
		</p>
	</form>
	
	
</div>	
