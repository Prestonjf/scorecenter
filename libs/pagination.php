<?php

	$totalResults = 0;
	$totalPages = 1;
	$resultStart = 0;
	$resultEnd = 0;
	$pageCount = 1;

	function paginationHeader($results) {
		$totalResults = sizeof($results);
		$totalPages = ceil(($totalResults+1) / 15);
		if ($_SESSION["resultsPage"] == null) $_SESSION["resultsPage"] = 1;
		$resultStart = ($_SESSION["resultsPage"]-1) * 15;
		if ($totalResults != 0) $resultStart = $resultStart + 1;
		$resultEnd = (($_SESSION["resultsPage"]-1) * 15) + 14;
		if ($totalPages == 1) $resultEnd = $totalResults;
		$pageCount = 1;
		
		echo '<tr>';
		echo '<td colspan="3" style="font-size: 12px; border-style:none;"><div style="float: left;">Results: <b><span id="pageStartId">'.($resultStart).'</span></b> to <b><span id="pageEndId">'.($resultEnd).'</span></b> of <b>'.$totalResults.'</b></div>';
		echo '<div id="pagesId" style="float: right;">Page: '; 
		while ($pageCount <= $totalPages) {
			if ($_SESSION["resultsPage"]==$pageCount) {
				echo '<b><u>'.$pageCount.'</u></b> '; 
			} 
			else {
				echo '<a href="javascript:void(0)" onclick="loadPage(\''.$pageCount.'\',\''.$totalResults.'\',\''.$totalPages.'\')">'.$pageCount.'</a> ';
			}
			 $pageCount++;
		}
		echo '</div></td>';
		echo '</tr>';
	}

	function paginationFooter($results) {	
		$totalResults = sizeof($results);
		$totalPages = ceil(($totalResults+1) / 15);
		if ($_SESSION["resultsPage"] == null) $_SESSION["resultsPage"] = 1;
		$resultStart = ($_SESSION["resultsPage"]-1) * 15;
		if ($totalResults != 0) $resultStart = $resultStart + 1;
		$resultEnd = (($_SESSION["resultsPage"]-1) * 15) + 14;
		if ($totalPages == 1) $resultEnd = $totalResults;
		$pageCount = 1;
		
        echo '<tr><td colspan="3" style="font-size: 12px; border-style:none;"><div style="float: left;">Results: <b><span id="pageStartId2">'.($resultStart).'</span></b> to <b><span id="pageEndId2">'.($resultEnd).'</span></b> of <b>'.$totalResults.'</b></div>';
		echo '<div id="pagesId2" style="float: right;">Page: '; 
		while ($pageCount <= $totalPages) {
			if ($_SESSION["resultsPage"]==$pageCount) {
				echo '<b><u>'.$pageCount.'</u></b> '; 
			} 
			else {
				echo '<a href="javascript:void(0)" onclick="loadPage(\''.$pageCount.'\',\''.$totalResults.'\',\''.$totalPages.'\')">'.$pageCount.'</a> ';
			}
			$pageCount++;
		}
		echo '</div></td></tr>';
	}
	
	function paginationRow($index) {
		$currentPage = ceil(($index+1) / 15);
			echo '<tr id="resultPageRow'.$index.'" style="display: '; if ($_SESSION["resultsPage"]==$currentPage) echo ''; else echo 'none'; echo '">';
	}



?>