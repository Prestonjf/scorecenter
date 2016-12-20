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
	
require('libs/fpdf/fpdf.php');
include_once('score_center_objects.php');	
	
	function generateReport($mysqli) {
		$reportId = $_GET['reportId'];
		
		switch((int)$reportId) {
			case 0:		
				break;
			case 1:
				generatePreset1($mysqli);
				break;
			case 2:
				generatePreset2($mysqli);
				break;
			default:
				break;		
				
		}
		
		/**$errors = array();
		array_push($errors, $reportId);
		$_SESSION['scorecenter_errors'] = $errors; **/
	}
	
	function generatePreset1($mysqli) {
		
		// Generate PDF For each Team
		
		$pdf = new FPDF();
		$pdf->SetTitle($_SESSION["tournamentName"]. ' Event Results', true);
		$pdf->AddPage('L');
		$pdf->SetAutoPageBreak(true, 1); 
		
		// OVERVIEW HEADER
		$pdf->SetFont('Arial','',18);
		$pdf->SetTextColor(0);
		$pdf->Cell(0,10,$_SESSION["tournamentName"]. ' Event Results',0,0,'L');
		$pdf->Ln();
		
		$results = getEventResults($mysqli);
		
		$filename = str_replace(' ','_',$_SESSION["tournamentName"]);
		$pdf->Output('D', $filename.'_Report_Preset1.pdf',true);	
		exit();	
	}
	
	function generatePreset2($mysqli) {
		
	}
	
	function getEventResults($mysqli) {
		$sql = 'select E.NAME, GROUP_CONCAT(T1.NAME ORDER BY SCORE ASC) AS TEAM
					from TOURNAMENT_EVENT TE
					INNER JOIN EVENT E on TE.EVENT_ID=E.EVENT_ID
					INNER JOIN TOURNAMENT T on T.TOURNAMENT_ID=TE.TOURNAMENT_ID
					INNER JOIN TOURNAMENT_TEAM TT1 on T.TOURNAMENT_ID=TT1.TOURNAMENT_ID AND coalesce(TE.VERIFIED_FLAG,0) = 1 
					AND (TT1.ALTERNATE_FLAG = 0 or COALESCE(TE.PRIM_ALT_FLAG,0) = 1)
					LEFT JOIN TEAM_EVENT_SCORE TES1 on TES1.TOURN_EVENT_ID=TE.TOURN_EVENT_ID AND TT1.TOURN_TEAM_ID=TES1.TOURN_TEAM_ID 
						AND TES1.SCORE <= 6 AND TES1.SCORE > 0
					LEFT JOIN TEAM T1 ON TT1.TEAM_ID=T1.TEAM_ID
					WHERE T.TOURNAMENT_ID='.$_SESSION["tournamentId"].' AND (T1.NAME is null OR (T1.NAME is not null AND TES1.SCORE is not null))
					GROUP BY NAME ORDER BY UPPER(E.NAME) ASC, SCORE ASC ';
		$result = $mysqli->query($sql);
		$events = array();
		
 		if ($result) {
			while($row = $result->fetch_array(MYSQLI_BOTH)) {
				$event = new EventResult();
				$event->eventName = $row['NAME'];
				
					
					
			}
		}
	}
	
	
?>