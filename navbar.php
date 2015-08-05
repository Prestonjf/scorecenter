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
        <li class="active"><a href="index.php">Home <span class="sr-only">(current)</span></a></li>
        <li><a href="controller.php?command=loadAllTournaments&">Tournaments</a></li>       
      </ul>

      <ul class="nav navbar-nav navbar-right">
        <li class="dropdown">
          <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Configuration<span class="caret"></span></a>
          <ul class="dropdown-menu">
            <li><a href="controller.php?command=loadAllTournaments&">Manage Tournaments</a></li>
            <li><a href="#">Manage Teams</a></li>
            <li><a href="controller.php?command=loadAllEvents&">Manage Events</a></li>
            <li role="separator" class="divider"></li>
            <li><a href="#">Utilities</a></li>
            <li><a href="#">About</a></li>
          </ul>
        </li>
      </ul>
    </div><!-- /.navbar-collapse -->
  </div><!-- /.container-fluid -->
</nav>