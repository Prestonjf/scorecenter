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
	<?php include_once('functions/head_tags.php'); ?>
	<?php include_once('functions/pagination.php'); ?>

	 <script src="js/hammer.min.js"></script>
  <script type="text/javascript">
  	var slideshow = eval(<?php echo $_SESSION["resultSlideshow"]; ?>);
	var slideshowIndex = 0;
	var slideshowLength = Object.keys(slideshow).length;
  

	
  $(document).ready(function(){
  	//console.log(slideshow);
	generateSlideContent();
	
	// Touch Actions
	var hammertime = new Hammer(document.getElementById('slideshow'));
	hammertime.on('swipeleft', function(ev) {
		next();
	});
	hammertime.on('swiperight', function(ev) {
		previous();
	});
	hammertime.on('tap', function(ev) {
		nextAnimation();
	});
	hammertime.on('press', function(ev) {
		previousAnimation();
	});
	hammertime.on('pinchin', function(ev) {
		quit();
	});
	});
	
	
	window.addEventListener("keyup", dealWithKeyboard, false);
	 
	function dealWithKeyboard(e) {
		switch(e.keyCode) {	
			case 37: // previous
				previous();
				break;
			case 38: // previousAnimation
				previousAnimation();
				break;
			case 39: // next
				next();
				break;
			case 40: // nextAnimation
				nextAnimation();
				break;  
			case 81:
				quit();
				break;  
		} 
	}
	
	function previous() {
		if (slideshowIndex -1 >= 0) slideshowIndex--; 
		generateSlideContent();
	}
	function previousAnimation() {
		if ((slideshow[slideshowIndex].animationPosition - 1) >= 0) slideshow[slideshowIndex].animationPosition = slideshow[slideshowIndex].animationPosition - 1;
		generateSlideContent();
	}
	function next() {
		if (slideshowIndex + 1 < slideshowLength) slideshowIndex++; 
		generateSlideContent();
	}
	function nextAnimation() {
		var animationCount = Object.keys(slideshow[slideshowIndex].teamNames).length;
		if (animationCount == 0) animationCount = Object.keys(slideshow[slideshowIndex].labelValues).length;
		if ((slideshow[slideshowIndex].animationPosition + 1) <= animationCount) slideshow[slideshowIndex].animationPosition = slideshow[slideshowIndex].animationPosition + 1;
		generateSlideContent();	
	}
	function quit() {
		if(confirm('Are you sure you want to exit the slideshow?')) {
			$('#controllerForm').append('<input type="hidden" name="command" value="exitSlideShow" />');
			$("#controllerForm").submit(); 
		}	
	}
	
	function generateSlideContent() {
		var slideHtml = "";
		var slide = slideshow[slideshowIndex];
		
		if (slide.type == 'PLACEHOLDER') {
			slideHtml += '<div style="width: 100%; font-size: 2vw; white-space:nowrap; text-align: center;">' + '<i>Science Olympiad</i>' + '</div><br />'
			slideHtml += '<div style="width: 100%; font-size: 5vw; white-space:nowrap; text-align: center;">' + slide.headerText + '</div><br /><br />';
			slideHtml += '<center><img alt="" src="'+slide.logoPath+'" width="200" height="200"></center><br /><br />';
			slideHtml += '<div style="width: 100%; font-size: 3vw; white-space:nowrap; text-align: center;"><i>' + slide.text + '</i></div>';
		}
		else if (slide.type == 'GENERAL') {
			slideHtml += '<div style="width: 100%; font-size: 2vw; white-space:nowrap; text-align: center;">' + '<i>Science Olympiad</i>' + '</div><br />'
			slideHtml += '<div style="width: 100%; font-size: 5vw; white-space:nowrap; text-align: center;">' + slide.headerText + '</div><br /><br />';
			slideHtml += '<div style="width: 100%; font-size: 3vw; white-space:nowrap; text-align: center;"><i>' + slide.text + '</i></div>';
		}
		else if (slide.type == 'EVENTSCORE') {
			slideHtml += '<div style="width: 100%; font-size: 5vw; white-space:nowrap; text-align: center;">' + slide.headerText + '</div><br />'
			var count  = 0; 
			var animationPosition = slide.animationPosition;
			var animationCount = Object.keys(slideshow[slideshowIndex].teamNames).length;
			if (animationCount == 0) slideHtml += '<div style="width: 100%; font-size: 5vw; white-space:nowrap; text-align: left;">' + 'No Results Available' + '</div>';
			
			while (count < animationCount) {
				if (((animationCount-1) - animationPosition) >= count) slideHtml += '<div style="width: 100%; font-size: 1vw; white-space:nowrap; text-align: center;">&nbsp;</div>';
				else slideHtml += '<div style="width: 100%; font-size: 3.5vw; white-space:nowrap; text-align: left;">' + slide.teamNames[count] + '</div>';
				count++;
			}			
		}
		else if (slide.type == 'TEAMLIST') {
			slideHtml += '<div style="width: 100%; font-size: 5vw; white-space:nowrap; text-align: center;">' + slide.headerText + '</div><br />'
			var count  = 0;
			var animationPosition = slide.animationPosition;			
			//var elementCount = Object.keys(slideshow[slideshowIndex].labelValues).length;	
			while (count < animationPosition) {
				var element = slide.labelValues[count];
				slideHtml += '<div style="width: 100%; font-size: 2vw; white-space:nowrap; text-align: left;">' + element[0] + '</div>';
				if (element.length > 1)
					slideHtml += '<div style="width: 100%; font-size: 3vw; white-space:nowrap; text-align: left;">' + element[1] + '</div><br /><br />';
				count++;
			}			
		}
		else if (slide.type == 'OVERALLRESULTS') {
			slideHtml += '<div style="width: 100%; font-size: 5vw; white-space:nowrap; text-align: center;">' + slide.headerText + '</div><br />'
			var count  = 0;
			var animationPosition = slide.animationPosition;			
			//var elementCount = Object.keys(slideshow[slideshowIndex].labelValues).length;	
			while (count < animationPosition) {
				var element = slide.labelValues[count];
				slideHtml += '<div style="width: 100%; font-size: 2vw; white-space:nowrap; text-align: left;">' + element[0] + '</div>';
				if (element.length > 1)
					slideHtml += '<div style="width: 100%; font-size: 3.5vw; white-space:nowrap; text-align: left;">' + element[1] + '</div><br /><br />';
				count++;
			}			
		}
		
		
		document.getElementById('slideshow').innerHTML = slideHtml;	
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
  
  	<form action="controller.php" method="GET" id="controllerForm">
     <div class="container">
     
      <div id="errors" class="alert alert-danger" role="alert" style="display: none;"></div>
      <div id="messages" class="alert alert-success" role="alert" style="display: none;"></div>
     
		<div id="slideshow">
				
	 
		</div>
      <hr>
	<?php include_once 'footer.php'; ?>

    </div><!--/.container-->
    </form>
      
      
    <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <script src="js/jquery-1.11.3.js"></script>
    <!-- Include all compiled plugins (below), or include individual files as needed -->
    <script src="js/bootstrap.min.js"></script>
    
    <?php 
    	if ($_SESSION['savesuccessUser'] != null and $_SESSION['savesuccessUser'] == '1') { ?>
    	<script type="text/javascript">displaySuccess('<strong>Saved: </strong>User has been updated successfully!');</script>
   	<?php $_SESSION['savesuccessUser'] = null; } ?> 	
    
  </body>
</html>