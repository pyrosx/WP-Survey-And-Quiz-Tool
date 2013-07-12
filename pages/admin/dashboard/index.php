<div class="wrap">

	<div id="icon-tools" class="icon32"></div>
	<h2>
		Online Training
	</h2>

	<h3>Overview</h3>
	
	<table id="overview">
		<tr>
			<td>Stores</td>
			<td><?php echo $numStores;?></td>
		</tr>
		<tr>
			<td>Stores without Franchisee</td>
			<td><?php echo $numStoresWithoutFranchisees;?></td>
		</tr>
		<tr>
			<td>Franchisees</td>
			<td><?php echo $numFranchisees;?></td>
		</tr>
		<tr>
			<td>Number of Users</td>
			<td><?php echo $numEmployees;?></td>
		</tr>
		<tr>
			<td>Average Store Completion Rate</td>
			<td><?php echo $overallCompRate;?></td>
		</tr>
	</table>

	<p>&nbsp;</p>

	<p><a href="<?php echo WPSQT_URL_MAIN; ?>&section=results&subsection=quiz&wpsqt-download">Export All Results as CSV</a>
	</p>

	<p>&nbsp;</p>
	<h3>Stores</h3>
	<div id="store_table">
	<?php echo Wpsqt_System::getStoreTable(); ?>	
	</div>
		
</div>