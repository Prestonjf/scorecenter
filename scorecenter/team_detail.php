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
include_once('logon_check.php');
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
	checkUserRole(1);
	
	// Edit or Readonly
	$disable = '';
	if ($_SESSION["disableRecord"] != null and $_SESSION["disableRecord"] == 1) {
		//$disable = 'disabled';	
	}
	
?>
<!DOCTYPE html>
<html lang="en">
  <head>
	<?php include_once('functions/head_tags.php'); ?>
	
  <script type="text/javascript">
  $(document).ready(function(){
  
    //	$("#addTournament").click(function(){
     //   	alert("add");
    //	});

    	
    	
	});
	function validate() {
		if ($('#teamName').val().trim() == '') {
			displayError("<strong>Validation Error:</strong> Team Name is required.");
			return false;
		}
		if ($('#teamDivision').val().trim() == '') {
			displayError("<strong>Validation Error:</strong> Team Divison is required.");
			return false;
		}
		if ($('#teamState').val().trim() == '') {
			displayError("<strong>Validation Error:</strong> Team State is required.");
			return false;
		}
		if ($('#teamRegion').val().trim() == '') {
			displayError("<strong>Validation Error:</strong> Team Region is required.");
			return false;
		}
		return true;
	}
	
	function validateDelete() {
		if(confirm('Are you sure you wish to unlink this coach?')) {
			return true;
		}
		return false;
	}

  
  </script>
    <style>
  	.borderless td {
  			padding-top: 1em;
			padding-right: 2em;
  			border: none;
  	}
	.red {
		color: red;
	}
  
  
  </style>
  </head>
  
  <body>
  <?php include_once 'navbar.php'; ?>
  
  	<form action="controller.php" method="GET">
     <div class="container">
     
      <div id="errors" class="alert alert-danger" role="alert" style="display: none;"></div>
      <div id="messages" class="alert alert-success" role="alert" style="display: none;"></div>
     
     <h1>Edit Team</h1>
	 <hr>
	<table width="100%" class="borderless">
	<tr>
	<td width="20%"><label for="teamName">Team Name:<span class="red">*</span></label></td>
	<td width="30%">
	<input type="text" size="40" class="form-control" name="teamName" id="teamName" <?php echo $disable; ?> value="<?php echo $_SESSION["teamName"];?>">
	</td>
	<td><label for="teamDivision">Division:<span class="red">*</span></label></td>
	<td>
	<select class="form-control" name="teamDivision" id="teamDivision" <?php echo $disable; ?>>
			<option value=""></option>
			<option value="A" <?php if($_SESSION["teamDivision"] == 'A'){echo("selected");}?>>A</option>
			<option value="B" <?php if($_SESSION["teamDivision"] == 'B'){echo("selected");}?>>B</option>
			<option value="C" <?php if($_SESSION["teamDivision"] == 'C'){echo("selected");}?>>C</option>
	</select>
	</td>
	</tr>
	
	<tr>
	<td width="20%"><label for="teamState">Team State:<span class="red">*</span></label></td>
	<td width="30%">
	<select class="form-control" name="teamState" id="teamState" <?php echo $disable; ?>>
			<option value=""></option>
			<?php
			if ($_SESSION["stateCodeList"] != null) {	
				$results = $_SESSION["stateCodeList"];
				foreach($results as $row) {	
					echo '<option value="'.$row['REF_DATA_CODE'].'" '; if($_SESSION["teamState"] == $row['REF_DATA_CODE']){echo("selected");} echo '>'.$row['DISPLAY_TEXT'].'</option>';
				}
			}
			?>
	</select>
	</td>
	<td width="20%"><label for="teamCity">Team City: </label></td>
	<td width="30%">
	<input type="text" size="40" class="form-control" name="teamCity" id="teamCity" <?php echo $disable; ?> value="<?php echo $_SESSION["teamCity"];?>">
	</td>
	</tr>
	
	<tr>
	<td width="20%"><label for="teamRegion">Team Region:<span class="red">*</span></label></td>
	<td width="30%">
	<select class="form-control" name="teamRegion" id="teamRegion" <?php echo $disable; ?>>
			<option value=""></option>
			<?php
			if ($_SESSION["regionCodeList"] != null) {	
				$results = $_SESSION["regionCodeList"];
				foreach($results as $row) {	
					echo '<option value="'.$row['REF_DATA_CODE'].'" '; if($_SESSION["teamRegion"] == $row['REF_DATA_CODE']){echo("selected");} echo '>'.$row['DISPLAY_TEXT'].'</option>';
				}
			}
			?>
	</select>
	</td>
	<td width="20%"></td>
	<td width="30%"></td>
	</tr>
	
	<tr>
	<td><label for="teamPhone">Team Phone Number: </label></td>
	<td>
	<input type="text" size="40" class="form-control" name="teamPhone" id="teamPhone" <?php echo $disable; ?> value="<?php echo $_SESSION["teamPhone"];?>">
	</td>
	<td><label for="teamEmail">Team Email Address: </label></td>
	<td>
	<input type="text" size="40" class="form-control" name="teamEmail" id="teamEmail" <?php echo $disable; ?> value="<?php echo $_SESSION["teamEmail"];?>">
	</td>
	</tr>

	
	<tr>
	<td><label>Team Description: </label></td>
	<td></td>
	</tr>
	<tr>
		<td colspan="4">
			<textarea class="form-control"  name="teamDescription" id="teamDescription" spellcheck="true" rows="5" cols="100" <?php echo $disable; ?>><?php echo $_SESSION["teamDescription"];?></textarea>
		</td>
	</tr>
	</table>

      <hr>
      
      	<h2>Coaches</h2>
        <table class="table table-hover" id="coachTable">
        <thead>
            <tr>
                <th width="30%" data-field="name" data-align="right" data-sortable="true">Coach Name</th>
                <th width="60%" data-field="name" data-align="right" data-sortable="true">Coach Email / Username</th>
				<th width="10%" data-field="actions" data-align="center" data-sortable="true">Actions</th>
            </tr>
        </thead>
        <tbody id="coachTableBody">
         <?php
	        
			$coachList = $_SESSION["coachList"];
			$coachCount = 0;
			if ($coachList) {
				foreach ($coachList as $coach) {
					echo '<tr>';
      				echo '<td>'; echo $coach['3']; echo '</td>';
					echo '<td>'; echo $coach['4']; echo '</td>';
					echo '<td>';
					if ($disable != 'disabled') {
						echo '<button type="submit" class="btn btn-xs btn-danger" onclick="return validateDelete()" name="deleteCoach" value='.$coach[2].'>Delete</button>';
					}
					echo '</td>';
					echo '</tr>';
					
					$coachCount++;
				}
			}        
        ?>  
        </tbody>
        </table>
	<?php if ($disable != 'disabled') { ?>
	<div class="input-group">
			<button type="submit" class="btn btn-xs btn-primary" name="addCoach">Select Coach</button>
	</div>
	<?php } ?>
	<hr>

	<?php if ($disable != 'disabled') { ?>
    	<button type="submit" class="btn btn-xs btn-danger" name="saveTeam" onclick="return validate();" value="<?php echo $_SESSION["teamId"];?>">Save</button>
    <?php } ?>
 	<button type="submit" class="btn btn-xs btn-primary" name="cancelTeam" value="0">Cancel</button>

      
      <hr>
	<?php include_once 'footer.php'; ?>

    </div><!--/.container-->
    </form>
      
      
    <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <script src="js/jquery-1.11.3.js"></script>
    <!-- Include all compiled plugins (below), or include individual files as needed -->
    <script src="js/bootstrap.min.js"></script>
    
    <?php 
    	if ($_SESSION['saveTeamError'] != null and $_SESSION['saveTeamError'] == '1') { ?>
    	<script type="text/javascript">displayError("<strong>Cannot Add Team:</strong> Team has already been added.");</script>
   	<?php $_SESSION['saveTeamError'] = null; } 
   	 if ($_SESSION['duplicateCoachError'] != null and $_SESSION['duplicateCoachError'] == '1') { ?>
    	<script type="text/javascript">displayError("<strong>Cannot Add Coach:</strong> Coach has already been added.");</script>
   	<?php $_SESSION['duplicateCoachError'] = null; } ?>
    
  </body>
</html>