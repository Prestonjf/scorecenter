<?php
/**
 * Tournament Score Center (TSC) - Tournament scoring web application.
 * Copyright (C) 2019  Preston Frazier
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see http://www.gnu.org/licenses/.
 *    
 * @package: Tournament Score Center (TSC) - Tournament scoring web application.
 * @version: 1.19.1, 01.13.2019 
 * @author: Preston Frazier http://scorecenter.prestonsproductions.com/index.php 
 * @license: http://www.gnu.org/licenses/gpl-3.0.en.html GPLv3
 */
    
		
	session_start(); 
	include_once('score_center_objects.php');
	include_once('functions/global_functions.php');
	include_once('functions/self_schedule_functions.php');
	include_once('functions/constants.php');
	include_once('logon_check.php');
	require_once 'login.php';
	
	// Security Level Check
	include_once('role_check.php');
	checkUserRole(1);
?>
<!DOCTYPE html>
<html lang="en">
  <head>
	<?php include_once('functions/head_tags.php'); ?>
	<?php include_once('functions/pagination.php'); ?>
	
	<?php $selfSchedule = unserialize($_SESSION["selfSchedule"]); 
		$disabled = false; 
	?>
	
  <script type="text/javascript">
  //$(document).ready(function() { });

	function addTeamPeriod(element, tournTeamId, scheduleEventPeriodId) {
			xmlhttp = new XMLHttpRequest();
			xmlhttp.onreadystatechange = function() {
				if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
					if (xmlhttp.responseText.trim() == 'error1') {
						 displayError('<?php echo ERROR_SELF_SCHEDULE_Add_TEAM_1; ?>');	
					}
					else if (xmlhttp.responseText.trim() == 'error2') {
						displayError('<?php echo ERROR_SELF_SCHEDULE_Add_TEAM_2; ?>');
					}
					else {
						document.getElementById('periodSchedulerDiv').innerHTML = xmlhttp.responseText;
						//displaySuccess('<?php echo SUCCESS_SELF_SCHEDULE_PERIODS_DELETED; ?>');
					}					
				}
			}	
	        xmlhttp.open("GET","controller.php?command=addTeamEventPeriod&tournTeamId="+tournTeamId+"&scheduleEventPeriodId="+scheduleEventPeriodId, true);
	        xmlhttp.send();
	}
	
	function removeTeamPeriod(element, tournTeamId, scheduleEventPeriodId, scheduleTeamId) {
		if (confirm('Are you sure you want to remove this team from this event period?')) {
			xmlhttp = new XMLHttpRequest();
			xmlhttp.onreadystatechange = function() {
				if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
					if (xmlhttp.responseText.trim() == 'error1') {
						// displayError('<?php echo ERROR_SELF_SCHEDULE_EVENT_PERIODS_DELETE; ?>');	
					}
					else {
						document.getElementById('periodSchedulerDiv').innerHTML = xmlhttp.responseText;
						//displaySuccess('<?php echo SUCCESS_SELF_SCHEDULE_PERIODS_DELETED; ?>');
					}					
				}
			}	
	        xmlhttp.open("GET","controller.php?command=removeTeamEventPeriod&tournTeamId="+tournTeamId+"&scheduleEventPeriodId="+ scheduleEventPeriodId+"&scheduleTeamId="+scheduleTeamId, true);
	        xmlhttp.send();
	    }
	}
	
	function reloadPeriodScheduler(mode,scheduleEventPeriodId) {
		$.ajax({
			type     : "GET",
		    cache    : false,
		    url      : "controller.php?command=refreshSelfSchedule&mode="+mode+"&scheduleEventPeriodId="+scheduleEventPeriodId,
		    data     : $(this).serialize(),
		    success  : function(data) {	
			    //var d = data.split("<?php echo STRING_SPLIT_TOKEN;?>");
			    $('#periodSchedulerDiv').html(data);
		    }
		});
	}


	setTimeout(refreshTimer, 7200000);
	setInterval(refreshTimer, 5000);
	
	function refreshTimer() {
		reloadPeriodScheduler('scheduler', '<?php echo $selfSchedule->currentPeriodId; ?>');
	}


  
  </script>
    <style>
  
  
  </style>
  </head>
  
  <body>
  <?php include_once 'navbar.php'; ?>
  
  	<form action="controller.php" method="GET" id="selfSchedulePeriodForm">
     <div class="container">	     
     
      <div id="errors" class="alert alert-danger" role="alert" style="display: none;"></div>
      <div id="messages" class="alert alert-success" role="alert" style="display: none;"></div>
      
     <div id="periodSchedulerDiv"> 
    <?php
		echo getPeriodScheduler();

	?>
     </div>

	<div style="float: left; width: 100%;">
	<br /><br />
			 <button type="submit" class="btn btn-xs btn-primary" name="cancelSelfSchedulePeriod">Cancel</button>
      <hr>
	<?php include_once 'footer.php'; ?>
	</div>
    </div><!--/.container-->
    </form>
      
      
    <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <script src="js/jquery-1.11.3.js"></script>
    <!-- Include all compiled plugins (below), or include individual files as needed -->
    <script src="js/bootstrap.min.js"></script>
    
    <?php 
    	if ($_SESSION['selfScheduleSaveSuccess'] != null and $_SESSION['selfScheduleSaveSuccess'] == '1') { ?>
    	<script type="text/javascript">displaySuccess('<?php echo SUCCESS_SELF_SCHEDULE_SAVED; ?>');</script>
    <?php $_SESSION['selfScheduleSaveSuccess'] = null; } ?>
    

  </body>
</html>