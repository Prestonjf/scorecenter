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
 * @version: 1.16.2, 09.05.2016 
 * @author: Preston Frazier http://scorecenter.prestonsproductions.com/index.php 
 * @license: http://www.gnu.org/licenses/gpl-3.0.en.html GPLv3
 */
    
		
	session_start(); 
	include_once('score_center_objects.php');
	include_once('logon_check.php');
	include_once('libs/score_center_global_settings.php');
	require_once 'login.php';
	
	$mysqli = mysqli_init();
	mysqli_options($mysqli, MYSQLI_OPT_LOCAL_INFILE, true);
	mysqli_real_connect($mysqli, $db_hostname,$db_username,$db_password,$db_database);
	
	if (mysqli_connect_errno()) {
		printf("Connect failed: %s\n", mysqli_connect_error());
		exit();
	}

	// Security Level Check
	include_once('role_check.php');
	checkUserRole(2);
?>
<!DOCTYPE html>
<html lang="en">
  <head>
	<?php include_once('libs/head_tags.php'); ?>
	<script src='js/spectrum.js'></script>
	<link rel='stylesheet' href='css/spectrum.css' />
  
  <script type="text/javascript">

	

  
  function saveMessage(message) {
		document.getElementById('messages').style.display = "block";
		document.getElementById('messages').innerHTML = "<strong>Saved: </strong>"+message+" has been saved successfully!";
		document.body.scrollTop = document.documentElement.scrollTop = 0;						
	}
	
	function clearFilterCriteria() {
		document.getElementById('eventName').value = '';
		document.getElementById('eventsNumber').value = '';
	}
	
	function print() {
		var mywindow = window.open('', 'Tournament Results', 'height=600,width=800');
        mywindow.document.write('<html><head><title>Tournament Results</title>');
        mywindow.document.write('<link href="css/bootstrap.min.css" rel="stylesheet">');
		mywindow.document.write('<link href="js/jquery-ui-1.11.4/jquery-ui.css" rel="stylesheet">');
		mywindow.document.write('<style>th.rotate {height: 185px;white-space: nowrap;} th.rotate > div {transform: translate(-10px, 0px) rotate(270deg);-webkit-transform: translate(-10px, 0px) rotate(270deg);-ms-transform: translate(-10px, 0px) rotate(270deg);width: 30px;}</style>');
        mywindow.document.write('</head><body>');
        mywindow.document.write($('#resultsGrid').html());
        mywindow.document.write('</body></html>');

        mywindow.document.close(); // necessary for IE >= 10
        mywindow.focus(); // necessary for IE >= 10

        mywindow.print();
        mywindow.close();

        return true;		
	}
	
	function changeResultColor(command, color) {
			xmlhttp = new XMLHttpRequest();
			xmlhttp.onreadystatechange = function() {
				if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
					clearError();
					clearSuccess();
					if (xmlhttp.responseText == 'error') {
						//error message	
					}
					else {
						// success message
						document.getElementById('resultsGrid').innerHTML = xmlhttp.responseText;
						$("#primaryResultsGrid").tablesorter();
						if (document.getElementById('alternateResultsGrid') != null)
							$("#alternateResultsGrid").tablesorter();						
						if (color == '-1') { 
							$("#primaryRowColor").spectrum("set", "#FFFFFF"); // D1ECD1
							$("#primaryColumnColor").spectrum("set", "#FFFFFF"); // D1D1D1
							$("#secondaryRowColor").spectrum("set", "#FFFFFF"); // FFFFFF
							$("#secondaryColumnColor").spectrum("set", "#FFFFFF"); // CEDCCE
							
						}
					}					
				}
			}	
        xmlhttp.open("GET","controller.php?command="+command+"&color="+color,true);
        xmlhttp.send();	
	}
  
  </script>
    <style>
	.red {
		color: red;
	}
	
	th.rotate {
		height: 185px;
		white-space: nowrap;
	}
	th.rotate > div {
		transform: translate(-10px, 0px) rotate(270deg);
		-webkit-transform: translate(-10px, 0px) rotate(270deg);
		-ms-transform: translate(-10px, 0px) rotate(270deg);
		width: 30px;
	}
	
  
  </style>
  </head>
  
  <body>
  <?php include_once 'navbar.php'; ?>
  
  	<form action="controller.php" method="GET" id="form1">
     <div class="container">
     
      <div id="errors" class="alert alert-danger" role="alert" style="display: none;"></div>
      <div id="messages" class="alert alert-success" role="alert" style="display: none;"></div>
     
     <h1>Tournament Results</h1>
	 <table width="100%">
	 <tr>
	 <td><h4>Tournament: <?php echo $_SESSION["tournamentName"] . ' - ' . $_SESSION["tournamentDate"]; ?></h4></td>
	 <td><h4>Overall Points: <span style="font-weight:normal;font-size:14px;"><?php echo $_SESSION["pointsSystem"]; ?></span></h4></td>
	 </tr>
	 <tr>
     <td><h4>Division: <?php echo $_SESSION["tournamentDivision"]; ?></h4></td>
	 <td><h4></h4></td> <!-- Max Points Earned Per Event: <span style="font-weight:normal;font-size:14px;"><?php //echo $_SESSION["highestScore"]; ?></span> -->
	 </tr>
	 <tr>
	 <td><h4>Events Completed: <?php echo $_SESSION["tournamentEventsCompleted"]; ?></h4></td>
	 <td></td>
	 </tr>
	 </table>
	 <hr>
	 	<button type="submit" class="btn btn-xs btn-success" name="exportResultsCSV" value='.$row['0'].'>Export to .csv</button>
	 	<button type="submit" class="btn btn-xs btn-success" name="exportResultsEXCEL" value='.$row['0'].'>Export to .xlsx</button>
	 	<!-- <input type="button" class="btn btn-xs btn-success" name="printResults" onclick="print();" value='Print'/> -->
	 	&nbsp;&nbsp;&nbsp; | &nbsp;&nbsp;&nbsp;
		<?php echo '<button type="submit" class="btn btn-xs btn-success" name="viewSlideShow" value="'.$row['0'].'" >View Slideshow</button>'; ?>
		<?php echo '<button type="submit" class="btn btn-xs btn-success" name="exportSlideShowPDF" value="'.$row['0'].'" >Export Slideshow to .pdf</button>'; ?>
	 <hr>

	<div id="resultsGrid">
		<?php
			$rowCount = 2;	// Color Counts
			$colWidth = 1;
		?>
        <table id="primaryResultsGrid" class="table table-bordered table-hover tablesorter" style="table-layout:fixed;">
        <thead>
            <tr>
				<th <?php echo 'width="5%" style="background-color: #'.$_SESSION["primaryColumnColor"].';border-bottom: 1px solid #000000;"'; ?> class="rotate"><div><span class="sortableTH">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;#</span></div></th>
				<th <?php echo 'style="background-color: #'.$_SESSION["secondaryRowColor"].';border-bottom: 1px solid #000000;"'; ?> width="20%"><div><span class="sortableTH"><?php echo $_SESSION["tournamentName"]; ?><br /><?php echo 'Division: '.$_SESSION["tournamentDivision"]; ?><br /><?php echo 'Date: '.$_SESSION["tournamentDate"]; ?></span></div></th>
				<?php
				$tournamentResultsHeader = $_SESSION['tournamentResultsHeader'];
				if ($tournamentResultsHeader != null) {
					foreach ($tournamentResultsHeader as $resultHeader) {
						$colWidth = sizeof($tournamentResultsHeader) + 2;
						$colWidth = 75 / $colWidth;
						
						echo '<th width="'.$colWidth.'%" style="padding-left: none; border-bottom: 1px solid #000000;'; 
						if ($rowCount % 2 == 0) echo ' background-color: #'.$_SESSION["primaryColumnColor"].';';
						else echo ' background-color: #'.$_SESSION["secondaryRowColor"].';';
						echo '" class="rotate"><div><span class="sortableTH">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'.$resultHeader.'</span></div></th>';						
						$rowCount++;
					}
				}
				?>
                <th width="<?php echo $colWidth;?>%" style="border-bottom: 1px solid #000000; <?php if ($rowCount % 2 == 0) echo ' background-color: #'.$_SESSION["primaryColumnColor"].'; '; else echo ' background-color: #'.$_SESSION["secondaryRowColor"].';';?>" class="rotate"><div><span class="sortableTH">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Total Score</span></div></th>
				<?php $rowCount++; ?>
			   <th width="<?php echo $colWidth;?>%" style="border-bottom: 1px solid #000000; <?php if ($rowCount % 2 == 0) echo ' background-color: #'.$_SESSION["primaryColumnColor"].'; '; else echo ' background-color: #'.$_SESSION["secondaryRowColor"].';';?>" class="rotate"><div><span class="sortableTH">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Final Rank</span></div></th>
            </tr>
        </thead>
        <tbody>
         <?php 
		 $rowCount = 0;
		 
		 $tournamentResults = $_SESSION['tournamentResults'];
         if ($tournamentResults != null) {
			 foreach ($tournamentResults as $resultRow) {
				$colCount = 0;
      			echo '<tr>'; //style="border-right: 1px solid #000000;
				echo '<td width="5%" '; 
					if ($rowCount % 2 == 0 && $colCount % 2 == 0) echo 'style="background-color: #'.$_SESSION["secondaryColumnColor"].';"'; 
					else if ($rowCount % 2 != 0 && $colCount % 2 == 0) echo 'style="background-color: #'.$_SESSION["primaryColumnColor"].';"'; 
					else if ($rowCount % 2 == 0 && $colCount % 2 != 0) echo 'style="background-color: #'.$_SESSION["primaryRowColor"].';"';	
					else echo 'style="background-color: #'.$_SESSION["secondaryRowColor"].';"';
				echo '><b>'.$resultRow['1'].'</b></td>';
				$colCount++;
				echo '<td style="white-space: nowrap; overflow: hidden;  border-right: 1px solid #000000;'; if ($rowCount % 2 == 0) echo ' background-color: #'.$_SESSION["primaryRowColor"]; else echo ' background-color: #'.$_SESSION["secondaryRowColor"]; echo '"><b>
				<div data-toggle="tooltip" title="'.$resultRow['2'].'">'.$resultRow['2'].'</div></b></td>';
				$i = 3;
				$colCount++;
				while ($i < sizeof($resultRow)-1) {
					echo '<td width="'.$colWidth.'%" style="'; 
						if ($i == (sizeof($resultRow)-4)) echo 'border-right: 1px solid #000000; ';
						if ($rowCount % 2 == 0 && $colCount % 2 == 0) echo 'background-color: #'.$_SESSION["secondaryColumnColor"].';'; 
						else if ($rowCount % 2 != 0 && $colCount % 2 == 0) echo 'background-color: #'.$_SESSION["primaryColumnColor"].';';	
						else if ($rowCount % 2 == 0 && $colCount % 2 != 0) echo 'background-color: #'.$_SESSION["primaryRowColor"].';';
						else echo 'background-color: #'.$_SESSION["secondaryRowColor"].';';
					echo '">'.$resultRow[$i].'</td>';
					$i++;
					$colCount++;
				}
				
				echo '</tr>';
			$rowCount++;
		 }
    	}
        ?>
          </tbody>
          </table>
		  <?php

		  if ($_SESSION['tournamentAlternateResults'] != null) {
				$rowCount = 2; // Color Counts
				$colWidth = 1;
		  $tournamentAlternateResults = $_SESSION['tournamentAlternateResults'];
          if ($tournamentAlternateResults != null) {
			 echo '<table id="alternateResultsGrid" class="table table-bordered table-hover tablesorter" style="table-layout:fixed;">';
			 echo '<thead><tr>';
			 echo '<th width="5%" style="background-color: #'.$_SESSION["primaryColumnColor"].';border-bottom: 1px solid #000000;" class="rotate"><div><span class="sortableTH">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;#</span></div></th>';
			 echo '<th style="background-color: #'.$_SESSION["secondaryRowColor"].';border-bottom: 1px solid #000000;" width="20%"><div><span class="sortableTH">'. $_SESSION["tournamentName"] . ' (Alternate)<br />Division: '.$_SESSION["tournamentDivision"].'<br /> Date: '.$_SESSION["tournamentDate"] .'</span></div></th> ';

				$tournamentResultsHeader = $_SESSION['tournamentResultsHeader'];
				if ($tournamentResultsHeader != null) {
					foreach ($tournamentResultsHeader as $resultHeader) {
						$colWidth = sizeof($tournamentResultsHeader) + 2;
						$colWidth = 75 / $colWidth;
						
						echo '<th width="'.$colWidth.'%" style="padding-left: none; border-bottom: 1px solid #000000;'; 
						if ($rowCount % 2 == 0) echo ' background-color: #'.$_SESSION["primaryColumnColor"].';';
						else echo ' background-color: #'.$_SESSION["secondaryRowColor"].';';
						echo '" class="rotate"><div><span class="sortableTH">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'.$resultHeader.'</span></div></th>';						
						$rowCount++;
					}
				}
				echo '<th width="'. $colWidth.'%" style="border-bottom: 1px solid #000000;'; if ($rowCount % 2 == 0) echo ' background-color: #'.$_SESSION["primaryColumnColor"].'; '; else echo ' background-color: #'.$_SESSION["secondaryRowColor"].';'; echo '" class="rotate"><div><span class="sortableTH">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Total Score</span></div></th>';
				$rowCount++;
				echo '<th width="'. $colWidth.'%" style="border-bottom: 1px solid #000000;'; if ($rowCount % 2 == 0) echo ' background-color: #'.$_SESSION["primaryColumnColor"].'; '; else echo ' background-color: #'.$_SESSION["secondaryRowColor"].';'; echo '" class="rotate"><div><span class="sortableTH">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Final Rank</span></div></th> ';

			echo '</tr></thead>';
			echo '<tbody>';
			 $rowCount = 0;
			 foreach ($tournamentAlternateResults as $resultRow) {
				$colCount = 0;
      			echo '<tr>'; //style="border-right: 1px solid #000000;
				echo '<td width="5%" ';  
					if ($rowCount % 2 == 0 && $colCount % 2 == 0) echo 'style="background-color: #'.$_SESSION["secondaryColumnColor"].';"'; 
					else if ($rowCount % 2 != 0 && $colCount % 2 == 0) echo 'style="background-color: #'.$_SESSION["primaryColumnColor"].';"'; 
					else if ($rowCount % 2 == 0 && $colCount % 2 != 0) echo 'style="background-color: #'.$_SESSION["primaryRowColor"].';"';	
					else echo 'style="background-color: #'.$_SESSION["secondaryRowColor"].';"';
				echo '><b>'.$resultRow['1'].'</b></td>';
				$colCount++;
				echo '<td width="20%" style="white-space: nowrap; overflow: hidden;  border-right: 1px solid #000000;'; if ($rowCount % 2 == 0) echo ' background-color: #'.$_SESSION["primaryRowColor"]; else echo ' background-color: #'.$_SESSION["secondaryRowColor"]; echo '"><b>
				<div data-toggle="tooltip" title="'.$resultRow['2'].'">'.$resultRow['2'].'</div></b></td>';
				$i = 3;
				$colCount++;
				while ($i < sizeof($resultRow)-1) {
					echo '<td width="'.$colWidth.'%" style="'; 
						if ($i == (sizeof($resultRow)-4)) echo 'border-right: 1px solid #000000; ';
						if ($rowCount % 2 == 0 && $colCount % 2 == 0) echo 'background-color: #'.$_SESSION["secondaryColumnColor"].';'; 
						else if ($rowCount % 2 != 0 && $colCount % 2 == 0) echo 'background-color: #'.$_SESSION["primaryColumnColor"].';';	
						else if ($rowCount % 2 == 0 && $colCount % 2 != 0) echo 'background-color: #'.$_SESSION["primaryRowColor"].';';
						else echo 'background-color: #'.$_SESSION["secondaryRowColor"].';';
					echo '">'.$resultRow[$i].'</td>';
					$i++;
					$colCount++;
				}
				
				echo '</tr>';
			$rowCount++;
			}
			echo '</tbody>';
			echo '</table>';
			}
		  }	
		  
		  ?>
		</div>
		  * = Trial Event <br />
		  + = Alternate Team <br /><br />
		  
		  Primary Row Color: <input type='text' class="primaryRowColor" id="primaryRowColor"/>
		  <script>
		  	$(".primaryRowColor").spectrum({
				color: "<?php echo $_SESSION["primaryRowColor"]; ?>",
				showInitial: true,
				showPalette: true,
				palette: [ ['#D1ECD1', '#D1D1D1', '#FFFFFF'], ['#CEDCCE'] ],
				change: function(color) {
					var str = color.toHexString();	
					changeResultColor('updatePRowColor', str.replace("#", ""));		
				}
			});
			</script>
			Primary Column Color: <input type='text' class="primaryColumnColor" id="primaryColumnColor"/>
			<script>
			$(".primaryColumnColor").spectrum({
				color: "<?php echo $_SESSION["primaryColumnColor"]; ?>",
				showInitial: true,
				showPalette: true,
				palette: [ ['#D1ECD1', '#D1D1D1', '#FFFFFF'], ['#CEDCCE'] ],
				change: function(color) {
					var str = color.toHexString();	
					changeResultColor('updatePColColor', str.replace("#", ""));			
				}
			});
			</script>
		  Seconday Row Color: <input type='text' class="secondaryRowColor" id="secondaryRowColor"/>
		  <script>
		  	$(".secondaryRowColor").spectrum({
				color: "<?php echo $_SESSION["secondaryRowColor"]; ?>",
				showInitial: true,
				showPalette: true,
				palette: [ ['#D1ECD1', '#D1D1D1', '#FFFFFF'], ['#CEDCCE'] ],
				change: function(color) {
					var str = color.toHexString();	
					changeResultColor('updateSRowColor', str.replace("#", ""));		
				}
			});
			</script>
		Seconday Column Color: <input type='text' class="secondaryColumnColor" id="secondaryColumnColor"/>
		  <script>
		  	$(".secondaryColumnColor").spectrum({
				color: "<?php echo $_SESSION["secondaryColumnColor"]; ?>",
				showInitial: true,
				showPalette: true,
				palette: [ ['#D1ECD1', '#D1D1D1', '#FFFFFF'], ['#CEDCCE'] ],
				change: function(color) {
					var str = color.toHexString();	
					changeResultColor('updateSColColor', str.replace("#", ""));				
				}
			});
			</script>
		  <br /><br />
		  <button type="submit" class="btn btn-xs btn-primary" name="cancelTournament">Cancel</button>
		  <button type="button" class="btn btn-xs btn-primary" name="resetResultsColors" onclick="changeResultColor('resetResultsColors','-1'); ">Reset Colors</button>
      <hr>
	<?php include_once 'footer.php'; ?>

    </div><!--/.container-->
    </form>
      
    
    <!-- Include all compiled plugins (below), or include individual files as needed -->
    <script src="js/bootstrap.min.js"></script>
    <script>
      $(document).ready(function(){
		$("#primaryResultsGrid").tablesorter(); 
		if (document.getElementById('alternateResultsGrid') != null)
			$("#alternateResultsGrid").tablesorter();
	});
    </script>
    
    <?php 
    	if ($_SESSION['savesuccessEvent'] != null and $_SESSION['savesuccessEvent'] == '1') { ?>
    	<script type="text/javascript">saveMessage('Event');</script>
   	<?php $_SESSION['savesuccessEvent'] = null; } ?> 	
    
  </body>
</html>