<?php
/**
 * Tournament Score Center (TSC) - Tournament scoring web application.
 * Copyright (C) 2016  Preston Frazier
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
 * @version: 1.16.3, 12.07.2016 
 * @author: Preston Frazier http://scorecenter.prestonsproductions.com/index.php 
 * @license: http://www.gnu.org/licenses/gpl-3.0.en.html GPLv3
 */
    
		
	session_start(); 
	include_once('score_center_objects.php');
	include_once('functions/self_schedule_functions.php');
	include_once('functions/global_functions.php');
	include_once('functions/constants.php');
	include_once('logon_check.php');
	require_once 'login.php';
	
	// Security Level Check
	include_once('role_check.php');
	checkUserRole(4);
?>
<!DOCTYPE html>
<html lang="en">
  <head>
	<?php include_once('libs/head_tags.php'); ?>
	<?php include_once('libs/pagination.php'); ?>
	
  <script type="text/javascript">
  //$(document).ready(function() { });
	
	// validate save
	function validate() {
		if ($('#tournamentStartTime').val() == '' || $('#tournamentEndTime').val() == '' || $('#selfScheduleOpen').val() == '') {
			 displayError('<?php echo ERROR_SELF_SCHEDULE_1; ?>');
			return false;
		}
		var disabled = $('#selfScheduleSettingsForm').find(':input:disabled').removeAttr('disabled');
		return true;
	}
	
	function addTimePeriod() {
		var valid = true;
		
		if ($('#periodNumber').val() == '' || $('#periodStartTime').val() == '' || $('#periodEndTime').val() == '' || $('#periodInterval').val() == '')
			valid = false;
		if (valid) {
			var disabled = $('#selfScheduleSettingsForm').find(':input:disabled').removeAttr('disabled');
			xmlhttp = new XMLHttpRequest();
			xmlhttp.onreadystatechange = function() {
				if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
					disabled.attr('disabled','disabled');
					if (xmlhttp.responseText.trim() == 'error1' || xmlhttp.responseText.trim() == 'error2') {
					// Error			
					}
					else {
						$('#periodTableDiv').html(xmlhttp.responseText);
						$('#periodNumber').val('');
						$('#periodStartTime').val('');
						$('#periodEndTime').val('');
						$('#periodInterval').val('');
						reloadPeriodEvents();
					}					
				}
			}	
			
	        xmlhttp.open("GET","controller.php?command=addPeriod&"+$('#selfScheduleSettingsForm').serialize(),true);
	        xmlhttp.send();
        } else {
	        displayError('<?php echo ERROR_SELF_SCHEDULE_PERIOD_ADD; ?>');
        }
	}
	
	function deleteTimePeriod(element, row) {
		if (confirm('Are you sure you want to delete this period?')) {
			xmlhttp = new XMLHttpRequest();
			var disabled = $('#selfScheduleSettingsForm').find(':input:disabled').removeAttr('disabled');
			xmlhttp.onreadystatechange = function() {
				if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
					disabled.attr('disabled','disabled');
					if (xmlhttp.responseText.trim() == 'error1' || xmlhttp.responseText.trim() == 'error2') {
					// Error			
					}
					else {
						document.getElementById('periodTableDiv').innerHTML = xmlhttp.responseText;
						//displaySuccess('<?php echo SUCCESS_SELF_SCHEDULE_PERIOD_DELETED; ?>');
					}					
				}
			}	
	        xmlhttp.open("GET","controller.php?command=deletePeriod&schedulePeriodId="+element.value+"&periodRow="+row+"&"+$('#selfScheduleSettingsForm').serialize(),true);
	        xmlhttp.send();
        } 
	}
	
	function addNewEventPeriod(element, id, periodListSize) {
		var valid = true;	
		var flag = 0;
		if ($('#allDayEventFlag'+id).prop('checked') == true) {
			flag = 1;
			if ($('#periodLength'+id).val() == '' || $('#periodLength'+id).val() == '0' || $('#periodInterval'+id).val() == '' || $('#periodInterval'+id).val() == '0' || $('#teamLimit'+id).val() == '' || $('#teamLimit'+id).val() == '0' || $('#eventStartTime'+id).val() == null || $('#eventStartTime'+id).val() == '')
				valid = false;
			if (periodListSize != null && periodListSize > 1) {
				valid = false;
			}
		}
		else {
			if ($('#selectedPeriod'+id).val() == '' || $('#periodTeamLimit'+id).val() == '')
				valid = false;
		}
		if (valid) {
			xmlhttp = new XMLHttpRequest();
			var disabled = $('#selfScheduleSettingsForm').find(':input:disabled').removeAttr('disabled');
			xmlhttp.onreadystatechange = function() {
			if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
				disabled.attr('disabled','disabled');
				if (xmlhttp.responseText.trim() == 'error1' || xmlhttp.responseText.trim() == 'error2') {
					displayError('<?php echo ERROR_SELF_SCHEDULE_EVENT_PERIOD_ADD; ?>');					
				}
				else {
					document.getElementById('eventPeriodsTableDiv').innerHTML = xmlhttp.responseText;
					
				}					
			}
			}	
		       xmlhttp.open("GET","controller.php?command=addEventPeriod&addPeriodNumber="+$('#selectedPeriod'+id).val()+"&addPeriodTeamLimit="+$('#periodTeamLimit'+id).val()+"&eventRow="+id+"&aPeriodLength="+$('#periodLength'+id).val()+"&aPeriodInterval="+$('#periodInterval'+id).val()+"&aTeamLimit="+$('#teamLimit'+id).val()+"&aEventStartTime="+$('#eventStartTime'+id).val()+"&allDayEventFlag="+flag+"&"+$('#selfScheduleSettingsForm').serialize(),true);
		       xmlhttp.send();	
	    } else {
	        displayError('<?php echo ERROR_SELF_SCHEDULE_PERIOD_ADD; ?>');
        }	
	}
	
	function reloadPeriodEvents() {
		xmlhttp = new XMLHttpRequest();
		xmlhttp.onreadystatechange = function() {
		if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
			if (xmlhttp.responseText.trim() == 'error1' || xmlhttp.responseText.trim() == 'error2') {
				// Error			
			}
			else {
				$('#eventPeriodsTableDiv').html(xmlhttp.responseText);
			}					
		}
		}	
	       xmlhttp.open("GET","controller.php?command=reloadEventPeriods",true);
	       xmlhttp.send();	
	}
	
	function deleteEventPrd(element, eventRow, periodRow) {
		if (confirm('Are you sure you want to delete this event period?')) {
			xmlhttp = new XMLHttpRequest();
			var disabled = $('#selfScheduleSettingsForm').find(':input:disabled').removeAttr('disabled');
			xmlhttp.onreadystatechange = function() {
				if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
					disabled.attr('disabled','disabled');
					if (xmlhttp.responseText.trim() == 'error1') {
						 displayError('<?php echo ERROR_SELF_SCHEDULE_EVENT_PERIOD_DELETE; ?>');	
					}
					else {
						document.getElementById('eventPeriodsTableDiv').innerHTML = xmlhttp.responseText;
						//displaySuccess('<?php echo SUCCESS_SELF_SCHEDULE_PERIOD_DELETED; ?>');
					}					
				}
			}	
	        xmlhttp.open("GET","controller.php?command=deleteEventPeriod&scheduleEventPeriodId="+element.value+"&eventRow="+eventRow+"&periodRow="+periodRow+"&"+$('#selfScheduleSettingsForm').serialize(),true);
	        xmlhttp.send();
        } 
	}
	
	function deleteAllEventPeriods(element, eventRow) {
		if (confirm('Are you sure you want to delete all periods for this event?')) {
			xmlhttp = new XMLHttpRequest();
			var disabled = $('#selfScheduleSettingsForm').find(':input:disabled').removeAttr('disabled');
			xmlhttp.onreadystatechange = function() {
				if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
					disabled.attr('disabled','disabled');
					if (xmlhttp.responseText.trim() == 'error1') {
						 displayError('<?php echo ERROR_SELF_SCHEDULE_EVENT_PERIODS_DELETE; ?>');	
					}
					else {
						document.getElementById('eventPeriodsTableDiv').innerHTML = xmlhttp.responseText;
						displaySuccess('<?php echo SUCCESS_SELF_SCHEDULE_PERIODS_DELETED; ?>');
					}					
				}
			}	
	        xmlhttp.open("GET","controller.php?command=deleteAllEventPrds&eventRowId="+eventRow+"&"+$('#selfScheduleSettingsForm').serialize(),true);
	        xmlhttp.send();
        } 
	}
	
	function allDayEventChecked(row, periodListSize) {
		if (periodListSize == null || periodListSize < 1) {
			if ($('#allDayEventFlag'+row).prop('checked') == true) {
				$('#eventStartTime'+row).prop('disabled', false);
				$('#periodLength'+row).prop('disabled', false);	
				$('#periodInterval'+row).prop('disabled', false);	
				$('#teamLimit'+row).prop('disabled', false);	
				
				$('#selectedPeriod'+row).prop('disabled', true); $('#selectedPeriod'+row).val('');	
				$('#periodTeamLimit'+row).prop('disabled', true); $('#periodTeamLimit'+row).val('');
			}
			else {
				$('#eventStartTime'+row).prop('disabled', true);
				$('#periodLength'+row).prop('disabled', true);	$('#periodLength'+row).val('0');
				$('#periodInterval'+row).prop('disabled', true); $('#periodInterval'+row).val('0');
				$('#teamLimit'+row).prop('disabled', true);	$('#teamLimit'+row).val('0');
				
				$('#selectedPeriod'+row).prop('disabled', false);
				$('#periodTeamLimit'+row).prop('disabled', false); 
			}
		}
		else {
			if ($('#allDayEventFlag'+row).prop('checked') == true) $('#allDayEventFlag'+row).prop('checked', false);
			else $('#allDayEventFlag'+row).prop('checked', true);
		
		}
	}
	
	function scheduleEventPeriod(scheduleEventId, scheduleEventPeriodId) {
		if (scheduleEventId != null && scheduleEventId != '' && scheduleEventPeriodId != null && scheduleEventPeriodId != '') {
		// navigate to Schedule Event Period Screen
		if (scheduleEventPeriodId == -1) scheduleEventPeriodId = '';
		if (scheduleEventId == -1) scheduleEventId = '';
		$('<input />').attr('type','hidden').attr('name', 'command').attr('value', 'scheduleEventPeriod').appendTo('#selfScheduleSettingsForm');
		$('<input />').attr('type','hidden').attr('name', 'scheduleEventId').attr('value', scheduleEventId).appendTo('#selfScheduleSettingsForm');
		$('<input />').attr('type','hidden').attr('name', 'scheduleEventPeriodId').attr('value', scheduleEventPeriodId).appendTo('#selfScheduleSettingsForm');
		$('#selfScheduleSettingsForm').submit();
		}
		else {
			 displayError('<?php echo 'Cannot Schedule Period: '; ?>');
		}
	}
	
	function scheduleEventPeriodCoach(scheduleEventId, scheduleEventPeriodId) {
			xmlhttp = new XMLHttpRequest();
			var tournTeamId = $("input:radio[name='tournTeamSelected']:checked").val(); 
			if (tournTeamId != null && tournTeamId != '') {
				xmlhttp.onreadystatechange = function() {
					if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
						if (xmlhttp.responseText.trim() == 'error0') {
							 displayError('<?php echo ERROR_SELF_SCHEDULE_Add_TEAM_0; ?>');	
						}
						else if (xmlhttp.responseText.trim() == 'error1') {
							 displayError('<?php echo ERROR_SELF_SCHEDULE_Add_TEAM_1; ?>');	
						}
						else if (xmlhttp.responseText.trim() == 'error2') {
							if(confirm('Your team is already scheduled for this event. Would you like to schedule for this period instead? ')) {
								removeAddTeamPeriod(tournTeamId, scheduleEventPeriodId);
							}
							//displayError('<?php echo ERROR_SELF_SCHEDULE_Add_TEAM_2; ?>');
						}
						else if (xmlhttp.responseText.trim() == 'error3') {
							if(confirm('Your team is already scheduled for this event period. Would you like to remove your team from this period? ')) {
								removeTeamPeriod(tournTeamId, scheduleEventPeriodId);
							}
							//displayError('<?php echo ERROR_SELF_SCHEDULE_Add_TEAM_2; ?>');
						}
						else {
							document.getElementById('overview').innerHTML = xmlhttp.responseText;
						}					
					}
				}	
			}
	        xmlhttp.open("GET","controller.php?command=addTeamEventPeriod&tournTeamId="+tournTeamId+"&scheduleEventPeriodId="+scheduleEventPeriodId+"&mode=coach", true);
	        xmlhttp.send();
	}
	
	function removeAddTeamPeriod(tournTeamId, scheduleEventPeriodId) {
			xmlhttp1 = new XMLHttpRequest();
			xmlhttp1.onreadystatechange = function() {
				if (xmlhttp1.readyState == 4 && xmlhttp1.status == 200) {
					if (xmlhttp1.responseText.trim() == 'error1') {
					
					}
					else {
						document.getElementById('overview').innerHTML = xmlhttp1.responseText;
					}					
				}
			}	
	        xmlhttp1.open("GET","controller.php?command=removeAddTeamPeriod&tournTeamId="+tournTeamId+"&scheduleEventPeriodId="+scheduleEventPeriodId, true);
	        xmlhttp1.send();
	}
	
	function removeTeamPeriod(tournTeamId, scheduleEventPeriodId) {
			xmlhttp1 = new XMLHttpRequest();
			xmlhttp1.onreadystatechange = function() {
				if (xmlhttp1.readyState == 4 && xmlhttp1.status == 200) {
					if (xmlhttp1.responseText.trim() == 'error1') {
					}
					else {
						document.getElementById('overview').innerHTML = xmlhttp1.responseText;
					}					
				}
			}	
	        xmlhttp1.open("GET","controller.php?command=removeTeamEventPeriod&tournTeamId="+tournTeamId+"&scheduleEventPeriodId="+ scheduleEventPeriodId+"&mode=coach", true);
	        xmlhttp1.send();
	}
	
	function selectScheduleTeam(tournTeamId) {
			xmlhttp = new XMLHttpRequest();
			xmlhttp.onreadystatechange = function() {
				if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
					if (xmlhttp.responseText.trim() == 'error1') {
						// displayError('<?php echo ERROR_SELF_SCHEDULE_EVENT_PERIODS_DELETE; ?>');	
					}
					else {
						document.getElementById('overview').innerHTML = xmlhttp.responseText;
						//displaySuccess('<?php echo SUCCESS_SELF_SCHEDULE_PERIODS_DELETED; ?>');
					}					
				}
			}	
	        xmlhttp.open("GET","controller.php?command=selectScheduleTeam&tournTeamId="+tournTeamId,true);
	        xmlhttp.send();
	}
	
	function updateSelectedTeam(elm) {
		if (elm.checked) {
		    $.ajax({
		        type     : "GET",
		        cache    : false,
		        url      : "controller.php?command=updateSelectedTeam&tournTeamId="+elm.value,
		        data     : $(this).serialize(),
		        success  : function(data) {
			    	//displayError(data);	
		           // $(".printArea").empty().append(data).css('visibility','visible');
		        }
		    });
		}
	}
	
	function displayTeams() {
		if ($('#scheduleTeamsDiv').is(":visible")) {
			$('#scheduleTeamsDiv').hide();
		}
		else {
			$('#scheduleTeamsDiv').show();
		}
	}
  
  </script>
    <style>
  
  
  </style>
  </head>
  
  <body>
  <?php include_once 'navbar.php'; ?>
  
  	<form action="controller.php" method="GET" id="selfScheduleSettingsForm">
     <div class="container">
	     
	     <?php $selfSchedule = unserialize($_SESSION["selfSchedule"]); 
		     // Disable if at least one team has self scheduled
			 $disable = '';
			 if ($selfSchedule->getSelfScheduleOpenFlag() == 1) $disable = 'disabled';
		     
	     ?>
	     
     
      <div id="errors" class="alert alert-danger" role="alert" style="display: none;"></div>
      <div id="messages" class="alert alert-success" role="alert" style="display: none;"></div>
      

      
      <ul class="nav nav-tabs">
	        <li><h1 style="margin-bottom: 0px;">Self Schedule</h1></li>
	        <li>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</li>
	        <?php if (isUserAccess(1)) { ?>
	     	<li class="<?php if ($_SESSION["selfSchedulScreen"] == 'SETTINGS') echo 'active';?>"><a style="margin-top: 20px;" href="controller.php?command=selfScheduleNavigation&tab=SETTINGS&">Settings</a></li>
		 	<?php } ?>
	     	<li class="<?php if ($_SESSION["selfSchedulScreen"] == 'SCHEDULE') echo 'active';?>"><a style="margin-top: 20px;" href="controller.php?command=selfScheduleNavigation&tab=SCHEDULE&">Schedule</a></li>
	     	<?php if (getCurrentRole() == 'COACH') { ?>
	     	<li class="<?php if ($_SESSION["selfSchedulScreen"] == 'MYSCHEDULE') echo 'active';?>"><a style="margin-top: 20px;" href="controller.php?command=selfScheduleNavigation&tab=MYSCHEDULE&">My Schedule</a></li>
	     	<?php } ?>
	      
      </ul>

	  <?php echo getSSTournamentHeader(); ?>
	  
    <?php if ($_SESSION["selfSchedulScreen"] == 'SETTINGS') {?>
    <div id="settings">
	    <h2>General Settings</h2>
		<table width="100%" class="borderless">
		<tr>
		<td width="25%"><label for="tournamentStartTime">Tournament Start Time:<span class="red">*</span></label></td>
		<td width="25%">
			<input type="time" class="form-control" name="tournamentStartTime" id="tournamentStartTime" value="<?php echo $selfSchedule->getStartTime(); ?>">
		</td>
		
		<td width="25%"><label for="tournamentEndTime">Tournament End Time:<span class="red">*</span></label></td>
		<td width="25%">
			<input type="time" class="form-control" name="tournamentEndTime" id="tournamentEndTime" value="<?php echo $selfSchedule->getEndTime(); ?>">
		</td>
		</tr>
		<tr>
		<td width="25%"><label for="selfScheduleOpen">Self Scheduling Open:<span class="red">*</span></label></td>
		<td width="25%">
			<select class="form-control" name="selfScheduleOpen" id="selfScheduleOpen">
				<option value="0" <?php if($selfSchedule->getSelfScheduleOpenFlag() == 0){echo("selected");}?>>No</option>
				<option value="1" <?php if($selfSchedule->getSelfScheduleOpenFlag() == 1){echo("selected");}?>>Yes</option>
			</select>
		</td>
		
		<td width="25%"></td>
		<td width="25%"></td>
		</tr>
		</table>
		<hr>		
		<button type="submit" class="btn btn-xs btn-danger" name="saveScheduleSettings" onclick="return validate();" value="">Apply</button>
		<button type="submit" class="btn btn-xs btn-primary" name="cancelScheduleSettings">Cancel</button>

		<h2>Add Periods</h2>
		<div id="periodTableDiv">
			<?php echo getPeriodsTable(); ?>
		</div>	
		
		<table class="borderless"><tr>
		<td><button type="button" class="btn btn-xs btn-primary" name="addPeriod" onclick="addTimePeriod()" <?php echo $disable; ?>>Add Period</button></td>
		<td><label for="periodNumber">Period Number: </label></td><td><input type="number" class="form-control" name="periodNumber" id="periodNumber" value="<?php echo $_SESSION["periodNumber"]; ?>" <?php echo $disable; ?>></td>
		<td><label for="periodStartTime">Period Start Time: </label></td><td><input type="time" class="form-control" name="periodStartTime" id="periodStartTime" value="<?php echo $_SESSION["periodStartTime"]; ?>" <?php echo $disable; ?>></td>
		<td><label for="periodEndTime">Period End Time: </label></td><td><input type="time" class="form-control" name="periodEndTime" id="periodEndTime" value="<?php echo $_SESSION["periodEndTime"]; ?>" <?php echo $disable; ?>></td>
		<td><label for="periodInterval">Interval Time After Period (Mins): </label></td><td><input type="number" class="form-control" name="periodInterval" id="periodInterval" value="<?php echo $_SESSION["periodInterval"]; ?>" <?php echo $disable; ?>></td>
		</tr>
		</table>
		
		<hr>
		
		<h2>Event Periods</h2>
		<div id="eventPeriodsTableDiv">
			<?php echo getEventPeriodsTable(); ?>
		</div>	
		
		<hr>
		     <button type="submit" class="btn btn-xs btn-danger" name="saveScheduleSettings" onclick="return validate();" value="">Apply</button>
			 <button type="submit" class="btn btn-xs btn-primary" name="cancelScheduleSettings">Cancel</button>
	</div>
	
	<?php } else if ($_SESSION["selfSchedulScreen"] == 'SCHEDULE') { ?>
	<div id="overview">
		<?php 
			echo getMyTeams();
			echo getScheduleOverview(); 
		?>
	</div> 
	<br />	
 	<button type="submit" class="btn btn-xs btn-primary" name="cancelScheduleSettings">Cancel</button>

	<?php } else if ($_SESSION["selfSchedulScreen"] == 'MYSCHEDULE') { ?>
	<div id="mySchedule">
		<?php
			echo getMySchedule();
		?>
	<br>	
	<button type="submit" class="btn btn-xs btn-primary" name="cancelScheduleSettings">Cancel</button>
	</div>
	<?php } ?>
	
	<br /><br />

      <hr>
	<?php include_once 'footer.php'; ?>

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
    
    <?php 
		displayErrors();
	?>

  </body>
</html>