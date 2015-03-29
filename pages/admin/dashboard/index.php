<div class="wrap">

	<div id="icon-tools" class="icon32"></div>
	<h2>
		Online Training - Dashboard
	</h2>

<?php
if ($alertOverFailLimit > 0) {

?>
	<div class="dashboard-box" id="alerts">
	<h3>Alerts</h3>
	
	<table class="overview">
		<tr>
			<td>Users locked out of Quiz (more than 5 failures)</td>
			<td><a href="" id="failDetailsLink"><?php echo $alertOverFailLimit;?></a></td>
		</tr>
<!--
		<tr>
			<td>Users not started</td>
			<td><?php //echo $alertEmpNotStarted;?></td>
		</tr>
		<tr>
			<td>Locations with No Passes</td>
			<td><?php //echo $alertLocNoComplete;?></td>
		</tr>
		<tr>
			<td>Locations with No Starts</td>
			<td><?php //echo $alertLocNoStarts;?></td>
		</tr>
-->		
	</table>

	<div class="store_table" id="failDetails">

		<p><em>If a user fails a quiz 5 times, they will be blocked from attempting it again, and will be listed here. <br/>
		The link(s) below can be used to allow one more attempt.</em></p>
		<table>
			<thead><th>User</th><th>Store(s)</th><th>Quiz</th><th>Attempts</th><th></th></thead>
			<?php foreach($failDetails as $detail) { ?>
			<tr>
				<td><?php echo $detail['email']?></td>
				<td><?php echo $detail['store']?></td>
				<td><?php echo $detail['quiz']?></td>
				<td><?php echo $detail['attempts']?></td>
				<td><a href="<?php echo WPSQT_URL_DASHBOARD; ?>&resetFailId=<?php echo $detail['id']?>&resetFailQuiz=<?php echo $detail['id_quiz']?>">Authorise another Attempt</a>
			</tr>
			<?php } ?>
		</table>
	</div>


	</div>
<?php } ?>


	<p class="clear">&nbsp;</p>


	<div class="dashboard-box">
	<h3>Overview</h3>
	
	<table class="overview">
		<tr>
			<td>Stores</td>
			<td><?php echo $numStores;?></td>
		</tr>
		<tr>
			<td>Locations without Franchise Owner</td>
			<td><?php echo $numStoresWithoutFranchisees;?></td>
		</tr>
		<tr>
			<td>Franchise Owners</td>
			<td><?php echo $numFranchisees;?></td>
		</tr>
		<tr>
			<td>Employees</td>
			<td><?php echo $numEmployees;?></td>
		</tr>
		<tr>
			<td>Average Store Completion Rate</td>
			<td><?php echo $overallCompRate;?></td>
		</tr>
	</table>
	</div>
	
	<div class="dashboard-box">
		<h3>Reports</h3>
<!-- 		<p><a href="<?php echo WPSQT_URL_MAIN; ?>&section=results&subsection=quiz&wpsqt-download">Export All Results as CSV</a></p> -->
		<p><a href="<?php echo WPSQT_URL_MAIN; ?>&section=report&subsection=stores&wpsqt-download">Stores &amp; Staff List</a></p>
		<p><a href="<?php echo WPSQT_URL_MAIN; ?>&section=report&subsection=storeResults&wpsqt-download">Results by Store</a></p>
		<blockquote><blockquote>
			<p><a id="stateReportsClick" href="#" onClick="displayStates()">... by State</a></p>
			<div id="stateReports" style="display:none">
				<p><a href="<?php echo WPSQT_URL_MAIN; ?>&section=report&subsection=storeResultsByState&state=1&wpsqt-download">ACT</a></p>	
				<p><a href="<?php echo WPSQT_URL_MAIN; ?>&section=report&subsection=storeResultsByState&state=2&wpsqt-download">NSW</a></p>	
				<p><a href="<?php echo WPSQT_URL_MAIN; ?>&section=report&subsection=storeResultsByState&state=4&wpsqt-download">NT</a></p>	
				<p><a href="<?php echo WPSQT_URL_MAIN; ?>&section=report&subsection=storeResultsByState&state=8&wpsqt-download">Qld</a></p>	
				<p><a href="<?php echo WPSQT_URL_MAIN; ?>&section=report&subsection=storeResultsByState&state=16&wpsqt-download">SA</a></p>	
				<p><a href="<?php echo WPSQT_URL_MAIN; ?>&section=report&subsection=storeResultsByState&state=32&wpsqt-download">Tas</a></p>	
				<p><a href="<?php echo WPSQT_URL_MAIN; ?>&section=report&subsection=storeResultsByState&state=64&wpsqt-download">Vic</a></p>	
				<p><a href="<?php echo WPSQT_URL_MAIN; ?>&section=report&subsection=storeResultsByState&state=128&wpsqt-download">WA</a></p>	
				</div>
			</blockquote></blockquote>
		
		<p><a href="<?php echo WPSQT_URL_MAIN; ?>&section=report&subsection=storeUserResults&wpsqt-download">Results by Store with Employees</a></p>
		<blockquote><blockquote>
			<p><a id="stateUserReportsClick" href="#" onClick="displayUserStates()">... by State</a></p>
			<div id="stateUserReports" style="display:none">
				<p><a href="<?php echo WPSQT_URL_MAIN; ?>&section=report&subsection=storeUserResultsByState&state=1&wpsqt-download">ACT</a></p>	
				<p><a href="<?php echo WPSQT_URL_MAIN; ?>&section=report&subsection=storeUserResultsByState&state=2&wpsqt-download">NSW</a></p>	
				<p><a href="<?php echo WPSQT_URL_MAIN; ?>&section=report&subsection=storeUserResultsByState&state=4&wpsqt-download">NT</a></p>	
				<p><a href="<?php echo WPSQT_URL_MAIN; ?>&section=report&subsection=storeUserResultsByState&state=8&wpsqt-download">Qld</a></p>	
				<p><a href="<?php echo WPSQT_URL_MAIN; ?>&section=report&subsection=storeUserResultsByState&state=16&wpsqt-download">SA</a></p>	
				<p><a href="<?php echo WPSQT_URL_MAIN; ?>&section=report&subsection=storeUserResultsByState&state=32&wpsqt-download">Tas</a></p>	
				<p><a href="<?php echo WPSQT_URL_MAIN; ?>&section=report&subsection=storeUserResultsByState&state=64&wpsqt-download">Vic</a></p>	
				<p><a href="<?php echo WPSQT_URL_MAIN; ?>&section=report&subsection=storeUserResultsByState&state=128&wpsqt-download">WA</a></p>	
				</div>
			</blockquote></blockquote>

<!--		<p><a href="<?php echo WPSQT_URL_MAIN; ?>&section=report&subsection=results&wpsqt-download">Results - Overview</a></p>
		<p><a href="<?php echo WPSQT_URL_MAIN; ?>&section=report&subsection=results&full&wpsqt-download">Results - Full</a></p>
-->
	</div>
	<div class="dashboard-box">
		<h3>Tools</h3>
		<p><a href="<?php echo WPSQT_URL_DASHBOARD; ?>&subsection=bulkemail">Emails &amp; Reminders</a></p>
	</div>
		

	<p>&nbsp;</p>

	<p class="clear">&nbsp;</p>

	<h4><a href="<?php echo WPSQT_URL_LOCATIONS;?>">Go to Location Management - Store Table</a></h4>

		
</div>

<script>
function displayStates($) {
	jQuery('#stateReportsClick').toggle();
	jQuery('#stateReports').toggle();
	return false;
}
function displayUserStates($) {
	jQuery('#stateUserReportsClick').toggle();
	jQuery('#stateUserReports').toggle();
	return false;
}</script>