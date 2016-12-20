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
	include_once('functions/global_functions.php');
	include_once('functions/constants.php');
	require_once 'login.php';
	include_once('role_check.php');
	include_once('logon_check.php'); // Check Valid Session
	checkUserRole(2); // Security Level Check
?>
<!DOCTYPE html>
<html lang="en">
  <head>
	<?php include_once('functions/head_tags.php'); ?>
	<?php include_once('functions/pagination.php'); ?>
	
	<script type="text/javascript">
	
	function generateReport(report) {		
		$('<input />').attr('type','hidden').attr('name', 'command').attr('value', 'generateTournamentReport').appendTo('#tournamentReportsForm');
		$('<input />').attr('type','hidden').attr('name', 'reportId').attr('value', report).appendTo('#tournamentReportsForm');
		$('#tournamentReportsForm').submit();		
	}
	
	
	</script>
	
	<style>
		.reportHeader {
			width:100%; 
			padding: 0.1em; 
			background-color: #eeeeee;
			margin-bottom: 0.5em;
			border-radius: 6px;
			padding-left: 1em;
		}
		.reportTd {
			width: 50%;
			align-content: left;
			padding-bottom: 1em; 
		}
	</style>
</head>
  
  <body>
  <?php include_once 'navbar.php'; ?>
  
  <form action="controller.php" method="GET" id="tournamentReportsForm">
     <div class="container">
	     
	 <div id="errors" class="alert alert-danger" role="alert" style="display: none;"></div>
	 <div id="messages" class="alert alert-success" role="alert" style="display: none;"></div>
	 
	 <h1>Tournament Reports</h1>
	 <?php
	     echo getTournamentHeader();
	  ?>   
	 <table width="100%" >
	 <tr>
	 <td class="reportTd" valign="top"><div class="reportHeader"><h4><span class="red">*</span>Report Type</h4></div>
		 <input type="radio" name="reportType" value="overall" /> Overall Results &nbsp;
		 <input type="radio" name="reportType" value="event" /> Event Results &nbsp;
		 <input type="radio" name="reportType" value="team" /> Team Results&nbsp;
	 </td>
	 <td width="50%" align="left" valign="top" rowspan="4" valign="top" style="border-left: 5em solid white; ">
		 <div class="reportHeader"><h4>Preset Reports <img src="img/question_blue.png" alt="question_blue" height="10" width="10" data-toggle="tooltip" title="Hover over the blue question mark to see a description of the preset report."></h4> </div>
		 <div>
		 <button type="button" class="btn btn-xs btn-primary" name="generateTournamentReport1" onclick="generateReport('1')">Generate Preset 1</button>&nbsp;&nbsp;&nbsp;
		 <img src="img/question_blue.png" alt="question_blue" height="10" width="10" data-toggle="tooltip" title="A one page report is generated per team. Top 6 teams are listed per event. Team's finishing position is also listed per event.">&nbsp;&nbsp;&nbsp;Event Results to 6th Place + Team Rank
		 <hr>
		 </div>
		 <div>
		 <button type="button" class="btn btn-xs btn-primary" name="generateTournamentReport2" onclick="generateReport('2')">Generate Preset 2</button>&nbsp;&nbsp;&nbsp;
		 <img src="img/question_blue.png" alt="question_blue" height="10" width="10" data-toggle="tooltip" title="A one page report is generated per event. All ranks listed.">&nbsp;&nbsp;&nbsp;Event Results For All Events
		 <hr>
		 </div>
		 
	 </td>
	 </tr>
	 <tr>
	 <td class="reportTd" valign="top"><div class="reportHeader"><h4>Selected Events</h4></div>

	 </td>
	 </tr>
	 <tr>
	 <td class="reportTd" valign="top"><div class="reportHeader"><h4>Selected Teams</h4></div>

	 </td>
	 </tr>
	 <tr>
	 <td class="reportTd" valign="top"><div class="reportHeader"><h4><span class="red">*</span>Output Type</h4></div>
		 <input type="radio" name="outputType" value="pdf" /> .pdf &nbsp;
		 <input type="radio" name="outputType" value="xlsx" /> .xlsx &nbsp;
		 <input type="radio" name="outputType" value="csv" /> .csv &nbsp;
	 </td>
	 </tr>
		 
		 

		 
		 
	 </table>


	 <br>
	 <button type="button" class="btn btn-xs btn-primary" name="generateTournamentReport0" onclick="generateReport('0')">Generate Report</button>
	 <button type="submit" class="btn btn-xs btn-primary" name="cancelTournamentReports">Cancel</button>
 
	 <br /><br />
     <hr>
	 <?php include_once 'footer.php'; ?>
     </div>
  </form>
  
    <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <script src="js/jquery-1.11.3.js"></script>
    <!-- Include all compiled plugins (below), or include individual files as needed -->
    <script src="js/bootstrap.min.js"></script>
 
    
    <?php 
	    displayMsgs();
		displayErrors();
	?>
	
  </body>
</html>
 