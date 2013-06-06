<div class="wrap">
	
	<div id="icon-tools" class="icon32"></div>
	<h2>WP Survey And Quiz Tool - Franchise Management</h2>	
	
	<!---
	tabs
	<div id="nav">
		<h2 class="nav-tab-wrapper">
			<a class="nav-tab nav-tab-active" href="<?php echo WPSQT_URL_MAINENTANCE; ?>">Status</a>
			<a class="nav-tab" href="<?php echo WPSQT_URL_MAINENTANCE; ?>&section=backup">Backup</a>
			<a class="nav-tab" href="<?php echo WPSQT_URL_MAINENTANCE; ?>&section=uninstall">Uninstall</a>
			<a class="nav-tab" href="<?php echo WPSQT_URL_MAINENTANCE; ?>&section=upgrade">Upgrade</a>
			<a class="nav-tab" href="<?php echo WPSQT_URL_MAINENTANCE; ?>&section=debug">Debug</a>
		</h2>
	</div>
	-->

	<!-- 
	nav, pagination
	<div class="tablenav">
		<div class="alignleft">
			<a href="<?php echo esc_url($_SERVER['REQUEST_URI']); ?>&action=create" class="button-secondary" title="Add New Quiz"><?php _e('Add New Quiz','wp-survey-and-quiz-tool'); ?></a>
		</div>
	
		<div class="tablenav-pages">
		   <?php echo Wpsqt_Core::getPaginationLinks($currentPage, $numberOfPages); ?>
		</div>
	</div>
	-->
	
	<table class="widefat">
		<thead>
			<tr>
				<th>Username</th>
				<th>Email</th>
				<th>Store</th>
				<th>State</th>
				<th>Edit</th>
				<th>Delete</th>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<th>Username</th>
				<th>Email</th>
				<th>Store</th>
				<th>State</th>
				<th>Edit</th>
				<th>Delete</th>
			</tr>
		</tfoot>
		<tbody>
			
			<?php
			if ( empty($francList) ){
				?>
				<tr>
					<td colspan="2"><div style="text-align: center;"><?php _e('No Franchisees entered yet!','wp-survey-and-quiz-tool')?></div></td>
				</tr>
				<?php 
			}
			else {
				foreach ( $quizList as $quiz ) { ?>
			<tr>
				<td><?php echo $franc['name']; ?></td>
				<td><?php echo $franc['email']; ?></td>
				<td><?php echo $franc['store']; ?></td>
				<td><?php echo $franc['store']; ?></td>
				<td><a href="<?php echo $_SERVER['REQUEST_URI']; ?>&action=edit&quizid=<?php echo $franc['id']; ?>" class="button-secondary" title="Edit Franchisee">Edit</a></td>
				<td><a href="<?php echo $_SERVER['REQUEST_URI']; ?>&action=delete&quizid=<?php echo $franc['id']; ?>" class="button-secondary" title="Delete Franchisee">Delete</a></td>
			</tr>
			<?php } 
				}?>
		</tbody>
	</table>
	
</div>
	

