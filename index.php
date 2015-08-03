<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <title>Score Center - Michigan Science Olympiad</title>

    <!-- Bootstrap -->
    <link href="css/bootstrap.min.css" rel="stylesheet">

    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
  </head>
  <body>
  <?php include_once 'navbar.php'; ?>
  <h1></h1>
  
     <div class="container">

      <div class="row row-offcanvas row-offcanvas-right">

        <div class="col-xs-12 col-sm-9">
          <p class="pull-right visible-xs">
            <button type="button" class="btn btn-primary btn-xs" data-toggle="offcanvas">Toggle nav</button>
          </p>
          <div class="jumbotron">
            <h2>Welcome to Score Center!</h2>
            <p>This is a new electronic scoring system designed for Michigan Science Olympiad.
            The tool allows tournament organizers the ability to manage multiple tournaments including
            customizable teams and events per tournament.</p>
          </div>
          
          <form action="controller.php" method="GET">
          
          <h2>Today's Tournaments</h2>
        <table class="table table-hover">
        <thead>
            <tr>
                <th data-field="name" data-align="right" data-sortable="true">Tournament Name</th>
                <th data-field="division" data-align="center" data-sortable="true">Division</th>
                <th data-field="location" data-sortable="true">Location</th>
                <th data-field="date" data-sortable="true">Date</th>
                <th data-field="actions" data-sortable="true">Actions</th>
            </tr>
        </thead>
        <tbody>
        <?php
        	require_once 'login.php';
		 	$db_server = mysql_connect($db_hostname, $db_username, $db_password);
 			if (!db_server) die("Unable to connect to MySQL: " . mysql_error());
 			mysql_select_db($db_database);
 			$result = mysql_query("SELECT TOURNAMENT_ID, NAME, LOCATION,DIVISION, DATE_FORMAT(DATE,'%m/%d/%Y') 'DATE' FROM TOURNAMENT WHERE
 			DATE_FORMAT(DATE, '%Y-%m-%d') = DATE_FORMAT(CURDATE(), '%Y-%m-%d')"); 
 			if ($result) {
 				if (mysql_num_rows($result) == 0) { echo '<tr><td colspan="5">No Tournaments Today</td></tr>';}
 				else {
      				while($row = mysql_fetch_array($result)) {
      					echo '<tr>';
      					echo '<td>'; echo $row['1']; echo '</td>';
						echo '<td>'; echo $row['3']; echo '</td>';
						echo '<td>'; echo $row['2']; echo '</td>';
						echo '<td>'; echo $row['4']; echo '</td>';
						echo '<td>';
						echo '<button type="submit" class="btn btn-xs btn-primary" name="enterScores" value="'.$row['0'].'">Enter Scores</button> &nbsp;'; 				
						echo '<button type="submit" class="btn btn-xs btn-success" name="printScores" value='.$row['0'].'>Print Scores</button>';
						echo '</td>';						
						echo '</tr>';	
      				}
      			}
	
    		} else {
    			echo '<tr><td colspan="5">No Tournaments Today</td></tr>';
    		}
        ?>

        </tbody>
    	</table>
		
		</form>


        </div><!--/.col-xs-12.col-sm-9-->

        <div class="col-xs-6 col-sm-3 sidebar-offcanvas" id="sidebar">
          <div class="list-group">
            <a href="#" class="list-group-item active">Quick Links</a>
            <a href="#" class="list-group-item">Michigan Science Olympiad Website</a>
            <a href="#" class="list-group-item">MSU State Tournament Website</a>

          </div>
        </div><!--/.sidebar-offcanvas-->
      </div><!--/row-->

      <hr>

	<?php include_once 'footer.php'; ?>

    </div><!--/.container-->
    
    
    
      <!--   <form class="navbar-form navbar-left" role="search">
        <div class="form-group">
          <input type="text" class="form-control" placeholder="Search">
        </div>
        <button type="submit" class="btn btn-default">Submit</button>
      </form> -->
      
      
    <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
    <!-- Include all compiled plugins (below), or include individual files as needed -->
    <script src="js/bootstrap.min.js"></script>
  </body>
</html>