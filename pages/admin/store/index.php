<div class="wrap">
	
	<div id="icon-tools" class="icon32"></div>
	<h2>WP Survey And Quiz Tool - Store Management</h2>	

	<div id="nav">
		<h2 class="nav-tab-wrapper">
			<a class="nav-tab nav-tab-active" href="<?php echo WPSQT_URL_STORES; ?>">Stores</a>
			<a class="nav-tab" href="<?php echo WPSQT_URL_FRANCHISEES; ?>">Franchisees</a>
			<a class="nav-tab" href="<?php echo WPSQT_URL_EMPLOYEES; ?>">Employees</a>
		</h2>
	</div>


	<form method="post">
		<h2>
		<a href="<?php echo WPSQT_URL_STORES; ?>&section=addnew<?php if (isset($_GET["state"])) {echo "&state=".$_GET["state"];}?>" class="button add-new-h2">
		Add Store</a>
	
		<input type="hidden" name="page" value="custom_list_table" />
		<?php $customtable->search_box('Search', 'search_id'); ?>
	</h2>
	<?php
	$customtable->display();
	?>
	</form>


	
	<h2><a href="<?php echo WPSQT_URL_STORES; ?>&section=addnew" class="button add-new-h2">
		Add Store
	</a></h2>

	
</div>
	

