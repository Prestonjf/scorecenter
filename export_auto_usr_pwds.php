<?php 
	// Build spreadsheet to export
	// filename for download
  	$filename = $_SESSION["tournamentName"]." Supervisors " . $_SESSION["tournamentDivision"] . ".csv";
  	header("Content-Disposition: attachment; filename=\"$filename\"");
  	header("Content-Type: text/csv; charset=utf-8");
  		
  	$output = fopen('php://output', 'w');

	$tournamentResultsHeader = $_SESSION['tournamentResultsHeader'];
	$users = $_SESSION["EXPORT_GENERATED_USERS"];
	$headings = array();
	array_push($headings,"Event Name");
	array_push($headings,"Username");
	array_push($headings,"Password");
	fputcsv($output, $headings);
	foreach ($users as $row) {
		fputcsv($output, $row);
	}
	fclose($output);
	$_SESSION["EXPORT_GENERATED_USERS"] = null;
?>