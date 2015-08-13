<script type="text/javascript">
	function about() { 
		alert('Score Center \n \n Developed by Michigan Science Olympiad \n An open source scoring application for Science Olympiad Tournaments. \n \n Version: 1.0 (Alpha) - August 2015'); 
	}

</script>

<nav class="navbar navbar-default">
  <div class="container-fluid">
    <!-- Brand and toggle get grouped for better mobile display -->
    <div class="navbar-header">
      <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1" aria-expanded="false">
        <span class="sr-only">Toggle navigation</span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
      </button>
      <a class="navbar-brand" href="index.php">Score Center &nbsp;&nbsp;
      	<img alt="MISO Logo" src="img/misologo.png"  width="25" height="25" style="float: right;"></a>
    </div>

    <!-- Collect the nav links, forms, and other content for toggling -->
    <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
      <ul class="nav navbar-nav">
        <li class="<?php if ($_SERVER['REQUEST_URI'] == '/scorecenter/index.php') echo 'active';  ?>"><a href="index.php">Home<span class="sr-only">(current)</span></a></li>
        <li class="<?php if ($_SERVER['REQUEST_URI'] != '/scorecenter/index.php') echo 'active';  ?>"><a href="controller.php?command=loadAllTournaments&">Tournaments</a></li>       
      </ul>

      <ul class="nav navbar-nav navbar-right">
        <li class="dropdown">
          <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Configuration<span class="caret"></span></a>
          <ul class="dropdown-menu">
            <li><a href="controller.php?command=loadAllTournaments&">Manage Tournaments</a></li>
            <li><a href="controller.php?command=loadAllTeams&">Manage Teams</a></li>
            <li><a href="controller.php?command=loadAllEvents&">Manage Events</a></li>
            <li role="separator" class="divider"></li>
            <li><a href="#">Utilities</a></li>
            <li><a href="#" onclick="about();return false;" >About</a></li>
			<li role="separator" class="divider"></li>
			 <li><a href="controller.php?command=logout&">Logout</a></li>
          </ul>
        </li>
      </ul>
    </div><!-- /.navbar-collapse -->
  </div><!-- /.container-fluid -->
</nav>