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
include_once('functions/global_functions.php');	
	
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
		$pdf = new FPDF();
		$pdf->SetTitle($_SESSION["tournamentName"]. ' Event Results', true);
		$events = getEventResults($mysqli);
		$teams = getTournamentTeams($mysqli);
		
		// Generate PDF For each Team
		while($team = $teams->fetch_array(MYSQLI_BOTH)) {
			$pdf->AddPage('P');
			$pdf->SetAutoPageBreak(false, 1); 
			
			// OVERVIEW HEADER
			$pdf->SetFont('Arial','',18);
			$pdf->SetTextColor(0);
			$pdf->Cell(0,10,$_SESSION["tournamentName"]. ' Event Results ',0,0,'L');
			$pdf->Ln();
			$pdf->SetFont('Arial','',16);
			$pdf->Cell(0,10,$team['TEAM_NUMBER'] .') '.$team['NAME'],0,0,'L');
			$pdf->Ln();
			$pdf->SetFont('Arial','',10);
			$y = $pdf->GetY();
			$count = 0;
			$eventCount = $events->num_rows;
			$rows = ceil($eventCount / 4);
			while($event = $events->fetch_array(MYSQLI_BOTH)) {	
				$pdf->SetFont('Arial','U',10);		
				$pdf->Cell(30,10,$event['NAME'],0,0,'L');
				$count++;
				$x = $pdf->GetX()-30;
				$pdf->Ln(5);
				$pdf->setX($x);
				
				// Print Teams and Specific Team
				$t = $event['TEAM'];
				$item = explode('%*%,', $t);
				$pdf->SetFont('Arial','',8);
				$rank = 1;
				$specificTeam;
				$specificTeamRank;
				// Teams Through 6th place
				foreach($item as $i) {
					$ti = explode('%:%',$i);
					if ($ti[2] AND $ti[2] > 0 AND $ti[2] <= 6) {
						$teamName = $ti[0];
						$ti[3] = str_replace('%*%','',$ti[3]);
						if (strlen($teamName) > 28) $teamName = substr($teamName, 0, 28).'.';
						if ($ti[3] != 'P')
							$pdf->Cell(30,10,getEventStatus($ti[3]).' '.$teamName,0,0,'L');
						else
							$pdf->Cell(30,10,$rank.' '.$teamName,0,0,'L');
						$x = $pdf->GetX()-30;
						$pdf->Ln(5);
						$pdf->setX($x);
						$rank++;
					}
					if ($ti[1] == $team['TOURN_TEAM_ID']) {
						if ($ti[2] == 0) $specificTeamRank = getEventStatus($ti[3]);
						else $specificTeamRank = $ti[2];
						
						if (strlen($ti[0]) > 28) $specificTeam = substr($ti[0], 0, 28).'.';
						else $specificTeam = $ti[0];
					}
				}
				// Specific Team
				$pdf->SetFont('Arial','B',8);
				$pdf->Cell(30,10,$specificTeamRank.' '.$specificTeam,0,0,'L');
				
				$x = $pdf->GetX()-30;
				$pdf->Ln(7);
				$pdf->setX($x);

				if ($count % $rows == 0) {
					// Add New Page for Team is over 24 Events
					if ($count == 24 AND $eventCount > 24) {
						$count = 0;
						$pdf->AddPage('P');
						$pdf->SetAutoPageBreak(false, 1);
						$pdf->SetFont('Arial','',18);
						$pdf->SetTextColor(0);
						$pdf->Cell(0,10,$_SESSION["tournamentName"]. ' Event Results ',0,0,'L');
						$pdf->Ln();
						$pdf->SetFont('Arial','',16);
						$pdf->Cell(0,10,$team['TEAM_NUMBER'] .') '.$team['NAME'],0,0,'L');
						$pdf->Ln();
						$pdf->SetFont('Arial','',10);
						$y = $pdf->GetY();
					}
					else {
						$pdf->SetXY($pdf->GetX()+20+30,$y);
					}
				}
			}
			$events->data_seek(0);
		}
		// Output PDF
		$filename = str_replace(' ','_',$_SESSION["tournamentName"]);
		$pdf->Output('D', $filename.'_Report_Preset1.pdf',true);	
		exit();	
	}
	
	function generatePreset2($mysqli) {
		$pdf = new FPDF();
		$pdf->SetTitle($_SESSION["tournamentName"]. ' Event Results', true);
		$events = getEventResults($mysqli);
		
		// Generate PDF For each Event
			$rank = 1;
			$eventCount = $events->num_rows;
			//$rows = ceil($eventCount / 4);
			while($event = $events->fetch_array(MYSQLI_BOTH)) {
				$rank = 1;
				$pdf->AddPage('P');
				$pdf->SetAutoPageBreak(false, 1);
				$pdf->SetFont('Arial','',18);
				$pdf->SetTextColor(0);
				$pdf->Cell(0,10,$_SESSION["tournamentName"]. ' Event Results ',0,0,'L');
				$pdf->Ln();
				$pdf->SetFont('Arial','',16);
				$pdf->Cell(0,10,$event['NAME'],0,0,'L');
				$pdf->Ln();
				$pdf->SetFont('Arial','',10);
				$y = $pdf->GetY();
				
				// Print All Places
				$t = $event['TEAM'];
				$item = explode('%*%,', $t);
				foreach($item as $i) {
					$ti = explode('%:%',$i);
					$teamName = $ti[0];
					$ti[3] = str_replace('%*%','',$ti[3]);
					if (strlen($teamName) > 40) $teamName = substr($teamName, 0, 40).'.';
					if ($ti[3] != 'P')
						$pdf->Cell(30,10,getEventStatus($ti[3]).' '.$teamName,0,0,'L');
					else 
						$pdf->Cell(30,10,$rank.' '.$teamName,0,0,'L');
					$x = $pdf->GetX()-30;
					$pdf->Ln(5);
					$pdf->setX($x);
					
					if ($rank % 30 == 0) {
						$pdf->SetXY($pdf->GetX()+30+50,$y);
					}
					$rank++;
				}
			}
		// Output PDF
		$filename = str_replace(' ','_',$_SESSION["tournamentName"]);
		$pdf->Output('D', $filename.'_Report_Preset1.pdf',true);	
		exit();	
	}
	
	function getEventResults($mysqli) {
		$sql = ' select E.NAME,COALESCE(TE.PRIM_ALT_FLAG,0) as PRIM_ALT, GROUP_CONCAT(CONCAT(T1.NAME,\'%:%\',TT1.TOURN_TEAM_ID,\'%:%\',TES1.SCORE,\'%:%\',TES1.TEAM_STATUS,\'%*%\') ORDER BY FIELD(TES1.TEAM_STATUS,\'P\',\'X\',\'N\',\'D\'), SCORE ASC ) AS TEAM
					from TOURNAMENT_EVENT TE
					INNER JOIN EVENT E on TE.EVENT_ID=E.EVENT_ID
					INNER JOIN TOURNAMENT T on T.TOURNAMENT_ID=TE.TOURNAMENT_ID
					INNER JOIN TOURNAMENT_TEAM TT1 on T.TOURNAMENT_ID=TT1.TOURNAMENT_ID AND coalesce(TE.VERIFIED_FLAG,0) = 1 
					AND (TT1.ALTERNATE_FLAG = 0 or COALESCE(TE.PRIM_ALT_FLAG,0) = 1)
					LEFT JOIN TEAM_EVENT_SCORE TES1 on TES1.TOURN_EVENT_ID=TE.TOURN_EVENT_ID AND TT1.TOURN_TEAM_ID=TES1.TOURN_TEAM_ID 
						
					LEFT JOIN TEAM T1 ON TT1.TEAM_ID=T1.TEAM_ID
					WHERE T.TOURNAMENT_ID='.$_SESSION["tournamentId"].' AND (T1.NAME is null OR (T1.NAME is not null AND TES1.SCORE is not null))
					GROUP BY NAME ORDER BY UPPER(E.NAME) ASC ';
		$result = $mysqli->query('SET SESSION group_concat_max_len = 10000');			
		$result = $mysqli->query($sql);
		return $result;
	}
	
	function getTournamentTeams($mysqli) {
		$result = $mysqli->query("SELECT T.NAME, TT.TEAM_NUMBER, TT.TOURN_TEAM_ID, TT.ALTERNATE_FLAG
    	 	FROM TEAM T INNER JOIN TOURNAMENT_TEAM TT ON TT.TEAM_ID=T.TEAM_ID AND COALESCE(TT.ALTERNATE_FLAG,0) = 0
    	 	WHERE TT.TOURNAMENT_ID = " .$_SESSION["tournamentId"]. " ORDER BY if(CAST(TT.TEAM_NUMBER AS UNSIGNED)=0,1,0), CAST(TT.TEAM_NUMBER AS UNSIGNED) ASC, T.NAME ASC ");
    	 return $result;
	}
	
	
?>