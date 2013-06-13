<div class="wrap">
	
	<div id="icon-tools" class="icon32"></div>
	<h2>WP Survey And Quiz Tool - Store Management
	
	</h2>	

	<div id="nav">
		<h2 class="nav-tab-wrapper">
			<a class="nav-tab nav-tab-active" href="<?php echo WPSQT_URL_STORES; ?>">Stores</a>
			<a class="nav-tab" href="<?php echo WPSQT_URL_FRANCHISEES; ?>">Franchisees</a>
			<a class="nav-tab" href="<?php echo WPSQT_URL_EMPLOYEES; ?>">Employees</a>
		</h2>
	</div>


	<h2><a href="<?php echo WPSQT_URL_STORES; ?>&section=addnew<?php if (isset($_GET["state"])) {echo "&state=".$_GET["state"];}?>" class="button add-new-h2">
		Add Store
	</a></h2>
	
	<?php
	if (isset($_GET["state"])) {
		?>
		<h3>Filter: State = <?php echo $_GET["state"] ?> - <a href="<?php echo WPSQT_URL_STORES; ?>">Clear</a>
		</h3>
		<?php
	}
	?>

	<table class="widefat post">
		<thead>
			<tr>
				<th>Name/Location</th>
				<th>State</th>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<th>Name/Location</th>
				<th>State</th>
			</tr>
		</tfoot>
		<tbody>
			
		<?php if ( empty($storeList) ){ ?>
			<tr>
				<td colspan="2"><div style="text-align: center;"><?php _e('No Stores entered yet!','wp-survey-and-quiz-tool')?></div></td>
			</tr>
		<?php }	else {
			foreach ( $storeList as $store ) { ?>
			<tr>
				<td>
					<?php echo $store['location']; ?>
					<div class="row-actions">
						<span class="edit"><a href="<?php echo WPSQT_URL_STORES; ?>&section=edit&id=<?php echo $store['id']; ?>">Edit</a> | </span>
						<span class="edit"><a href="<?php echo WPSQT_URL_FRANCHISEES; ?>&section=addnew&id_store=<?php echo $store['id']; ?>">Add Franchisee</a> | </span>
						<span class="edit"><a href="<?php echo WPSQT_URL_EMPLOYEES; ?>&section=addnew&id_store=<?php echo $store['id']; ?>">Add Users</a> | </span>
						<span class="edit"><a href="<?php echo WPSQT_URL_EMPLOYEES; ?>&location=<?php echo $store['location']; ?>">Show Users</a> | </span>
						<span class="edit"><a href="<?php echo WPSQT_URL_STORES; ?>&section=edit&action=delete&id=<?php echo $store['id']; ?>">Delete</a></span>
					</div>
				</td>
				<td><a href="<?php echo WPSQT_URL_STORES; ?>&state=<?php echo $store['state']; ?>"><?php echo $store['state']; ?></a></td>
			</tr>
			<?php } 
				}?>
		</tbody>
	</table>
	
	<h2><a href="<?php echo WPSQT_URL_STORES; ?>&section=addnew" class="button add-new-h2">
		Add Store
	</a></h2>

	
</div>
	

