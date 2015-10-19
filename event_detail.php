<?php session_start(); 
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
	checkUserRole(2);

?>
<!DOCTYPE html>
<html lang="en">
  <head>
	<?php include_once('libs/head_tags.php'); ?>
	
  <script type="text/javascript">
   $(document).ready(function(){
	   
   });

	function validate() {
		var eventId = <?php if ($_SESSION["eventId"] == null or $_SESSION["eventId"] == '') echo '-1'; else echo $_SESSION["eventId"]; ?>;
		if ($('#eventName').val().trim() == '') {
			displayError("<strong>Validation Error:</strong> Event name is required.");
			return false;
		}
		
	/**	var xmlhttp = new XMLHttpRequest();
		xmlhttp.onreadystatechange = function() {
			if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
				clearError();
				clearSuccess();
				alert(xmlhttp.responseText);
				if (xmlhttp.responseText == 'error') {
					displayError("<strong>Cannot Add Event:</strong> Event has already been added.");
					return false;
				}	
				else {
					displaySuccess("<strong>Cannot Add Event:</strong> Event has already been added.");
					return true;
				}
			}
		}	

        xmlhttp.open("GET","controller.php?command=validateNewEvent&eventName="+$('#eventName').val()+"&eventId="+eventId,true);
        xmlhttp.send(); **/
		return true;
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
     
     <h1>Edit Event</h1>
	 <hr>
	<table width="90%" class="borderless">
	<tr>
	<td width="40%"><label for="eventName">Event Name:<span class="red">*</span></label></td>
	<td width="60%">
	<input type="text" size="40" class="form-control" name="eventName" id="eventName" value="<?php echo $_SESSION["eventName"];?>">
	</td>
	</tr>
	<tr>
	<td><label for="eventName">Winning Scoring System (For Auto Calculation):<span class="red">*</span></label></td>
	<td>
	<input type="radio" name="scoreSystemCode" id="scoreSystemCode1" value="HIGHRAW" <?php if ($_SESSION["scoreSystemCode"] =='HIGHRAW' or $_SESSION["scoreSystemCode"] == null) echo 'checked';?>> <label for="scoreSystemCode1">High Raw Score</label><br />
	<input type="radio" name="scoreSystemCode" id="scoreSystemCode2" value="HIGHRAWTIER" <?php if ($_SESSION["scoreSystemCode"] =='HIGHRAWTIER') echo 'checked';?>> <label for="scoreSystemCode2">High Raw Score / Tier Ranked</label><br />
	<input type="radio" name="scoreSystemCode" id="scoreSystemCode3" value="LOWRAW" <?php if ($_SESSION["scoreSystemCode"] =='LOWRAW') echo 'checked';?>> <label for="scoreSystemCode3">Low Raw Score</label><br />
	<input type="radio" name="scoreSystemCode" id="scoreSystemCode4" value="LOWRAWTIER" <?php if ($_SESSION["scoreSystemCode"] =='LOWRAWTIER') echo 'checked';?>> <label for="scoreSystemCode4">Low Raw Score / Tier Ranked</label><br />
	<input type="radio" name="scoreSystemCode" id="scoreSystemCode5" value="HIGHRAWTIER4LOW" <?php if ($_SESSION["scoreSystemCode"] =='HIGHRAWTIER4LOW') echo 'checked';?>> <label for="scoreSystemCode5">High Raw Score / Tier Ranked / 4th Tier Low</label><br />
	</td>
	</tr>
	
	<tr>
	<td><label>Event Description: </label></td>
	<td></td>
	</tr>
	<tr>
		<td colspan="2">
			<textarea class="form-control"  name="eventDescription" id="eventDescription" spellcheck="true" rows="5" cols="100"><?php echo $_SESSION["eventDescription"];?></textarea>
		</td>
	</tr>
	</table>
	
	<hr>

     <button type="submit" class="btn btn-xs btn-danger" name="saveEvent" onclick="return validate();" value="<?php echo $_SESSION["eventId"];?>">Save</button>
 	 <button type="submit" class="btn btn-xs btn-primary" name="cancelEvent" value="0">Cancel</button>


      <hr>
	<?php include_once 'footer.php'; ?>

    </div><!--/.container-->
    </form>
      
      
    <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <script src="js/jquery-1.11.3.js"></script>
    <!-- Include all compiled plugins (below), or include individual files as needed -->
    <script src="js/bootstrap.min.js"></script>
    
    <?php 
    	if ($_SESSION['saveEventError'] != null and $_SESSION['saveEventError'] == '1') { ?>
    	<script type="text/javascript">displayError("<strong>Cannot Add Event:</strong> Event has already been added.");</script>
   	<?php $_SESSION['saveEventError'] = null; } ?> 	
    
  </body>
</html>