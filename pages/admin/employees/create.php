<?php global $blog_id; ?>
<div class="wrap">

	<?php if ( isset($successMessage) ) {?>
		<div class='updated'><?php echo $successMessage; ?></div>
				
	<?php } ?>
	<div id="icon-tools" class="icon32"></div>
	<h2>
		Online Training - Add Employee
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
	<form method="POST" action="<?php echo esc_url($_SERVER['REQUEST_URI']); ?>" id="newfranchisee_form">
		<input type="hidden" name="wpsqt_nonce" value="<?php echo WPSQT_NONCE_CURRENT; ?>" />
		<table>
			<!-- User - select box (OR brand new user) -->
			<tr><td>User</td>
				<td><select name="wpqst_franchisee_user"  onchange="getval(this);">
					<?php 
					echo Wpsqt_System::addOption(-1,"Add New User...");
					foreach($users as $user) {
						echo Wpsqt_System::addOption($user['id'],$user['display_name'],$id_user);
					}?>
				</select>
			</td></tr>

			<tr><td>&nbsp;</td>
				<td>
					<table id="new_user" style="display:<?php echo $id_user ? 'none' : 'table';?>">			
					<tr><td>User name</td>
					<td>
						<input type="text" name="user_name" value=""/>
					</td>
					</tr>
					<tr><td>Email</td>
					<td>
						<input type="email" name="user_email" value=""/>
					</td>
					</tr></table>
				</td>
			</tr>

			<!-- Store - select box (OR new store) -->
			<tr><td>Store</td>
			<td>
				<select name="wpqst_franchisee_store">
					<?php 
					echo Wpsqt_System::addOption("",""); 
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

<script type="text/javascript">
    function getval(sel) {
    	if(sel.value==-1) {
    		// create new user selected
		   //alert(sel.value);
		   document.getElementById('new_user').style.display = 'table';
       } else {
		   document.getElementById('new_user').style.display = 'none';
       }
    }
</script>