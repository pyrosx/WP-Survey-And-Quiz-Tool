<div class="wrap">
	
	<div id="icon-tools" class="icon32"></div>
	<h2>WP Survey And Quiz Tool - Employee Management
	
	</h2>	

	<div id="nav">
		<h2 class="nav-tab-wrapper">
			<a class="nav-tab" href="<?php echo WPSQT_URL_STORES; ?>">Stores</a>
			<a class="nav-tab" href="<?php echo WPSQT_URL_FRANCHISEES; ?>">Franchisees</a>
			<a class="nav-tab nav-tab-active" href="<?php echo WPSQT_URL_EMPLOYEES; ?>">Employees</a>
		</h2>
	</div>

	<h2><a href="<?php echo WPSQT_URL_EMPLOYEES; ?>&section=addnew" class="button add-new-h2">
		Add Employee
	</a></h2>

	<?php	
	if (isset($_GET["state"]) || isset($_GET["location"])) {
	?>
	<h3>Filter: 
	<?php
		if (isset($_GET["state"])) { ?>
			State = <?php echo $_GET["state"] ?>
			
		<?php 
		} else if (isset($_GET["location"])) { ?>
			Store = <?php echo $_GET["location"] ?>
			
		<?php
		} ?>
		 - <a href="<?php echo WPSQT_URL_FRANCHISEES; ?>">Clear</a></h3>
	<?php
	} ?>


	<table class="widefat post">
		<thead>
			<tr>
				<th>Name</th>
				<th>Store</th>
				<th>State</th>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<th>Name</th>
				<th>Store</th>
				<th>State</th>
			</tr>
		</tfoot>
		<tbody>
			
			<?php
			if ( empty($franchiseeList) ){
				?>
				<tr>
					<td colspan="3"><div style="text-align: center;"><?php _e('No Employees entered yet!','wp-survey-and-quiz-tool')?></div></td>
				</tr>
				<?php 
			}
			else {
				foreach ( $franchiseeList as $franchisee ) { ?>
			<tr>
				<td>
					<?php echo $franchisee['name']; ?>
					<div class="row-actions">
						<span class="edit"><a href="<?php echo WPSQT_URL_EMPLOYEES; ?>&section=edit&id=<?php echo $franchisee['id']; ?>">Edit</a> | </span>
						<span class="edit"><a href="<?php echo WPSQT_URL_EMPLOYEES; ?>&section=edit&action=delete&id=<?php echo $franchisee['id']; ?>">Delete</a></span>
					</div>
				</td>
				<td><a href="<?php echo WPSQT_URL_EMPLOYEES; ?>&location=<?php echo $franchisee['location']; ?>"><?php echo $franchisee['location']; ?></a></td>
				<td><a href="<?php echo WPSQT_URL_EMPLOYEES; ?>&state=<?php echo $franchisee['state']; ?>"><?php echo $franchisee['state']; ?></a></td>
			</tr>
			<?php } 
				}?>
		</tbody>
	</table>
	
	<h2><a href="<?php echo WPSQT_URL_EMPLOYEES; ?>&section=addnew" class="button add-new-h2">
		Add Employee
	</a></h2>

	
</div>
	

