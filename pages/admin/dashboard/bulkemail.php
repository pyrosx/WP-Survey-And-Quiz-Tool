<div class="wrap">

	<div id="icon-tools" class="icon32"></div>
	<h2>
		Online Training - Bulk Email
	</h2>

	<div id="bulkemail">
		<form id="bulkemailform" method="POST" action="">
		<div class="section">
			<p><strong>To:</strong></p>
			<p><input type="radio" name="toradio" class="toradio" value="unfinished" id="tounfinished" jqcount="<?php echo $countUnfinished ?>" checked></input> <label for="tounfinished">Employees who have Not Finished - <em>(<?php echo $countUnfinished ?>)</em></label></p>
			<p><input type="radio" name="toradio" class="toradio" value="unstarted" id="tounstarted" jqcount="<?php echo $countUnstarted ?>"></input> <label for="tounstarted">Employees who have Not Started - <em>(<?php echo $countUnstarted ?>)</em></label></p>
			<p><input type="radio" name="toradio" class="toradio" value="all" id="toall" jqcount="<?php echo $countAll ?>"></input> <label for="toall">All Registered Users - <em>(<?php echo $countAll ?>)</em></label></p>
			<p><input type="radio" name="toradio" class="toradio" value="franchisees" id="tofranchisees" jqcount="<?php echo $countFranchisees ?>"></input> <label for="tofranchisees">Franchise Owners Only - <em>(<?php echo $countFranchisees ?>)</em></label></p>
		</div>
		<div class="section">
			<p>	<strong>Content:</strong></p>

			<p>	<input type="radio" name="bodyradio" value="standard" id="bodystandard" checked/><label for="bodystandard" id="bodystandardlabel"> Standard Reminder</label></p>
			<p>	<strong>OR</strong></p>

			<p>	<input type="radio" name="bodyradio" value="custom" id="bodycustom"/> <label for="bodycustom" id="bodycustomlabel">Custom</label></p>
			<div class="section" id="customsection">
				<p><label for="customsubject"> Subject:<label></p>
				<input type="text" name="subject" class="custominput" id="customsubject" required/>

				<p><label for="custombody">Body:</label></p>
				<textarea class="custominput" name="body" id="custombody" required></textarea>
			</div>
		</div>
		<div class="section">
			<p>	<input type="submit" class="button" value="Send" id="bulkemailsend"/> </p>
		</div>
		</form>
	</div>	
</div>