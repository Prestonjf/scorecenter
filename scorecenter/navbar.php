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
    
?>		
<script type="text/javascript">
	function about() { 
		alert('Score Center \n \nDeveloped by Michigan Science Olympiad \nAn open source scoring application for Science Olympiad Tournaments. \n \nVersion: 1.0 (Beta) - November 2015');
	}
	
	    jQuery(document).ready(function($){
   			$('#aboutLinkBox').popBox({width:200,height:350},'about');

			$('#aboutLink').click(function(){
   		 		$('#aboutLinkBox').triggerHandler('focus');
			});
	    });

</script>

<?php
		
		include_once('role_check.php');
		$isbasicMenu = false;
		
		if (strpos($_SERVER['REQUEST_URI'],'logon') !== false || strpos($_SERVER['REQUEST_URI'],'account') !== false || strpos($_SERVER['REQUEST_URI'],'slideshow') !== false) {
			$isbasicMenu = true;
		}
		
		if (!$isbasicMenu) {
			$userName = "";
			$role = "";
			$firstName = "";
			$lastName = "";
			if($_SESSION["userSessionInfo"] != null) {
				$userSessionInfo = unserialize($_SESSION["userSessionInfo"]);
				if ($userSessionInfo->getUserName() != null) {
					$userName = $userSessionInfo->getUserName();
					$firstName = $userSessionInfo->getFirstName();
					$lastName = $userSessionInfo->getLastName();
				}
				if ($userSessionInfo->getRole() != null) {
					$role = $userSessionInfo->getRole();
				}
			}
		}
		



echo '<nav class="navbar navbar-default">
  <div class="container-fluid">
    <!-- Brand and toggle get grouped for better mobile display -->
    <div class="navbar-header">
      <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1" aria-expanded="false">
        <span class="sr-only">Toggle navigation</span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
      </button>
      <a class="navbar-brand" href="controller.php?command=loadIndex&">Tournament Score Center &nbsp;&nbsp;
      	<img alt="MISO Logo" src="img/misologo.png"  width="25" height="25" style="float: right;"></a>
    </div>';

    echo '<!-- Collect the nav links, forms, and other content for toggling -->';
    echo '<div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">';
     echo '<ul class="nav navbar-nav">';
     if (!$isbasicMenu) {
       echo '<li class="'; if (strpos($_SERVER['REQUEST_URI'],'index') !== false) echo 'active';  echo'">';
        echo '<a href="controller.php?command=loadIndex&">Home<span class="sr-only">(current)</span></a></li>';
		if (isUserAccess(2)) {
        	echo '<li class="'; if (strpos($_SERVER['REQUEST_URI'],'index') === false) echo 'active';  echo'">';
        	echo '<a href="controller.php?command=loadAllTournaments&">Tournaments</a></li>';
        }      
      }
      echo '</ul>';
      echo '<ul class="nav navbar-nav navbar-right">';
      if (!$isbasicMenu) {
      	echo '<li><a href="controller.php?command=updateAccount&">Hello, '.$firstName.' '.$lastName.'</a></li><li><a href="controller.php?command=logout&"><u>Logout</u></a></li>';
      }
        echo '<li class="dropdown">';
          echo '<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Settings<span class="caret"></span></a>';
          echo '<ul class="dropdown-menu">';
          if (!$isbasicMenu AND isUserAccess(2)) {
            echo '<li><a href="controller.php?command=loadAllTournaments&">Manage Tournaments</a></li>';
            if (isUserAccess(1)) { echo '<li><a href="controller.php?command=loadAllTeams&">Manage Teams</a></li>'; }
            if (isUserAccess(1)) { echo '<li><a href="controller.php?command=loadAllEvents&">Manage Events</a></li>'; }
            if (isUserAccess(0)) { echo '<li><a href="controller.php?command=loadAllUsers&">Manage Users</a></li>'; }
            echo '<li role="separator" class="divider"></li>';
            if (isUserAccess(0)) { echo '<li><a href="controller.php?command=loadUtilities&">Utilities</a></li>'; }
           }
            echo '<li><a href="#" id="aboutLink" >About</a></li>';
            echo '<li><a href="http://scorecenter.prestonsproductions.com" target="_blank" id="" >Help</a></li>';
           if (!$isbasicMenu) {
			echo '<li role="separator" class="divider"></li>';
			echo '<li><a href="controller.php?command=logout&">Logout</a></li>';
			}
          echo '</ul>
        </li>
      </ul>
    </div><!-- /.navbar-collapse -->
  </div><!-- /.container-fluid -->
</nav>
<textarea id="aboutLinkBox" style="display: none;"></textarea>';
?>