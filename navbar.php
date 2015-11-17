<script type="text/javascript">
	function about() { 
		alert('Score Center \n \nDeveloped by Michigan Science Olympiad \nAn open source scoring application for Science Olympiad Tournaments. \n \nVersion: 1.0 (Beta) - November 2015'); 
	}

</script>

<?php
		$userName = "";
		$role = "";
		if($_SESSION["userSessionInfo"] != null) {
			$userSessionInfo = unserialize($_SESSION["userSessionInfo"]);
			if ($userSessionInfo->getUserName() != null) {
				$userName = $userSessionInfo->getUserName();
			}
			if ($userSessionInfo->getRole() != null) {
				$role = $userSessionInfo->getRole();
			}
		}
?>

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
      <a class="navbar-brand" href="controller.php?command=loadIndex&">Tournament Score Center &nbsp;&nbsp;
      	<img alt="MISO Logo" src="img/misologo.png"  width="25" height="25" style="float: right;"></a>
    </div>

    <!-- Collect the nav links, forms, and other content for toggling -->
    <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
      <ul class="nav navbar-nav">
        <li class="<?php if ($_SERVER['REQUEST_URI'] == '/scorecenter/index.php' or $_SERVER['REQUEST_URI'] == '/scorecenter/') echo 'active';  ?>">
        <a href="controller.php?command=loadIndex&">Home<span class="sr-only">(current)</span></a></li>
        <?php if ($role == 'ADMIN' or $role == 'VERIFIER') { ?>
        <li class="<?php if ($_SERVER['REQUEST_URI'] != '/scorecenter/index.php' and $_SERVER['REQUEST_URI'] != '/scorecenter/') echo 'active';  ?>">
        <a href="controller.php?command=loadAllTournaments&">Tournaments</a></li>
        <?php } ?>      
      </ul>
      <ul class="nav navbar-nav navbar-right">
      	<li><a href="controller.php?command=updateAccount&"><?php echo 'Logged in: '.$userName;?></a></li>
        <li class="dropdown">
          <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Configuration<span class="caret"></span></a>
          <ul class="dropdown-menu">
           <?php if ($role == 'ADMIN' or $role == 'VERIFIER') { ?>
            <li><a href="controller.php?command=loadAllTournaments&">Manage Tournaments</a></li>
            <li><a href="controller.php?command=loadAllTeams&">Manage Teams</a></li>
            <li><a href="controller.php?command=loadAllEvents&">Manage Events</a></li>
            <?php if ($role == 'ADMIN') { ?><li><a href="controller.php?command=loadAllUsers&">Manage Users</a></li> <?php } ?>
            <li role="separator" class="divider"></li>
            <?php if ($role == 'ADMIN') { ?> <li><a href="controller.php?command=loadUtilities&">Utilities</a></li><?php } ?>
            <?php } ?>
            <li><a href="#" onclick="about();return false;" >About</a></li>
			<li role="separator" class="divider"></li>
			 <li><a href="controller.php?command=logout&">Logout</a></li>
          </ul>
        </li>
      </ul>
    </div><!-- /.navbar-collapse -->
  </div><!-- /.container-fluid -->
</nav>