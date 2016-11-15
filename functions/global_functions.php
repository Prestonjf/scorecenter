<?php
	
	// Display Page Level Errors
	function displayErrors() {
		if ($_SESSION['scorecenter_errors'] != null) {
		    $errors = $_SESSION['scorecenter_errors'];
		    foreach($errors as $error) {
			    echo '<script type="text/javascript">displayError("'.$error.'");</script>';
			    break;
		    }		    
		    $_SESSION['scorecenter_errors'] = null;
	    }
	}
	
	
	
?>