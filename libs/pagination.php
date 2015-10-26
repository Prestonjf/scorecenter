<?php
	define (PAGEROWS, 15); // rows per page 
	
	$totalResults = 0;
	$totalPages = 1;
	$resultStart = 0;
	$resultEnd = 0;
	$pageCount = 1;

	function paginationHeader($results) {
		$totalResults = sizeof($results);
		$totalPages = ceil(($totalResults+1) / PAGEROWS);
		if ($_SESSION["resultsPage"] == null) $_SESSION["resultsPage"] = 1;
		$resultStart = ($_SESSION["resultsPage"]-1) * PAGEROWS;
		if ($totalResults != 0) $resultStart = $resultStart + 1;
		$resultEnd = (($_SESSION["resultsPage"]-1) * PAGEROWS) + 14;
		if ($totalPages == 1) $resultEnd = $totalResults;
		$pageCount = 1;

		echo '<input type="hidden" id="selectedPage" value="'.$pageCount.'" >';
		echo '';
		echo '<div style="font-size: 13px;"><div style="width: 20%; border-bottom:1px solid #eee; float: left;">Results: <b><span id="pageStartId">'.($resultStart).'</span></b> to <b><span id="pageEndId">'.($resultEnd).'</span></b> of <b>'.$totalResults.'</b></div>';
		echo '<div id="pagesId" style="width: 80%; border-bottom:1px solid #eee; float: right; text-align:right;">Page: '; 
		echo ' <a href="javascript:void(0)" onclick="loadPage(\'first\',\''.$totalResults.'\',\''.$totalPages.'\')">|<</a> ';
		echo ' <a href="javascript:void(0)" onclick="loadPage(\'previous\',\''.$totalResults.'\',\''.$totalPages.'\')"><</a> ';
		while ($pageCount <= $totalPages) {
			if ($_SESSION["resultsPage"]==$pageCount) {
				echo '<b><u>'.$pageCount.'</u></b> '; 
			} 
			else {
				echo '<a href="javascript:void(0)" onclick="loadPage(\''.$pageCount.'\',\''.$totalResults.'\',\''.$totalPages.'\')">'.$pageCount.'</a> ';
			}
			 $pageCount++;
		}
		echo ' <a href="javascript:void(0)" onclick="loadPage(\'next\',\''.$totalResults.'\',\''.$totalPages.'\')">></a> ';
		echo ' <a href="javascript:void(0)" onclick="loadPage(\'last\',\''.$totalResults.'\',\''.$totalPages.'\')">>|</a> ';
		echo '</div></div>';
		echo '';
	}

	function paginationFooter($results) {	
		$totalResults = sizeof($results);
		$totalPages = ceil(($totalResults+1) / PAGEROWS);
		if ($_SESSION["resultsPage"] == null) $_SESSION["resultsPage"] = 1;
		$resultStart = ($_SESSION["resultsPage"]-1) * PAGEROWS;
		if ($totalResults != 0) $resultStart = $resultStart + 1;
		$resultEnd = (($_SESSION["resultsPage"]-1) * PAGEROWS) + 14;
		if ($totalPages == 1) $resultEnd = $totalResults;
		$pageCount = 1;
		
        echo '<div style="font-size: 13px;"><div style="width: 20%; border-top:1px solid #eee; float: left;">Results: <b><span id="pageStartId2">'.($resultStart).'</span></b> to <b><span id="pageEndId2">'.($resultEnd).'</span></b> of <b>'.$totalResults.'</b></div>';
		echo '<div id="pagesId2" style="width: 80%; border-top:1px solid #eee; float: right; text-align:right;">Page: ';
		echo ' <a href="javascript:void(0)" onclick="loadPage(\'first\',\''.$totalResults.'\',\''.$totalPages.'\')">|<</a> ';
		echo ' <a href="javascript:void(0)" onclick="loadPage(\'previous\',\''.$totalResults.'\',\''.$totalPages.'\')"><</a> ';		
		while ($pageCount <= $totalPages) {
			if ($_SESSION["resultsPage"]==$pageCount) {
				echo '<b><u>'.$pageCount.'</u></b> '; 
			} 
			else {
				echo '<a href="javascript:void(0)" onclick="loadPage(\''.$pageCount.'\',\''.$totalResults.'\',\''.$totalPages.'\')">'.$pageCount.'</a> ';
			}
			$pageCount++;
		}
		echo ' <a href="javascript:void(0)" onclick="loadPage(\'next\',\''.$totalResults.'\',\''.$totalPages.'\')">></a> ';
		echo ' <a href="javascript:void(0)" onclick="loadPage(\'last\',\''.$totalResults.'\',\''.$totalPages.'\')">>|</a> ';
		echo '</div></div><br /><br />';
	}
	
	function paginationRow($index) {
		$currentPage = ceil(($index+1) / PAGEROWS);
			echo '<tr id="resultPageRow'.$index.'" style="display: '; if ($_SESSION["resultsPage"]==$currentPage) echo ''; else echo 'none'; echo '">';
	}



?>