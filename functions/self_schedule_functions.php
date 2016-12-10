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
	
	include_once('score_center_objects.php');
	
	function getPeriodsTable() {
		$selfSchedule = unserialize($_SESSION["selfSchedule"]);
		$disable = '';
		if ($selfSchedule->getSelfScheduleOpenFlag() == 1) $disable = 'disabled';
		
		$html = '<table class="table table-hover" id="periodTable">';
		$html .= '<thead><tr>
				<th data-field="number" data-align="right" data-sortable="true">Period Number</th>
				<th data-field="startTime" data-align="right" data-sortable="true">Start Time</th>
				<th data-field="endTime" data-align="right" data-sortable="true">End Time</th>
				<th data-field="interval" data-align="right" data-sortable="true">Interval Time (Mins)</th>
				<th data-field="actions" data-align="right" data-sortable="true">Actions</th>
				</tr></thead>';
		$html.= '<tbody id="periodTableBody">';
		
		if ($selfSchedule != null AND $selfSchedule->getPeriodList() != null) {
			$periodList = $selfSchedule->getPeriodList();
			$count = 0;
			foreach ($periodList as $period) {
				$html .= '<tr><td>'.$period->getPeriodNumber().'</td><td>'.$period->getStartTime().'</td><td>'.$period->getEndTime().'</td><td>'.$period->getPeriodInterval().'</td><td>
				 <button type="button" class="btn btn-xs btn-danger" name="deletePeriod" onclick="return deleteTimePeriod(this,'.$count.');" value="'.$period->getSchedulePeriodId().'" '.$disable.'>Delete</button></td></tr>';
			$count++;
			}
		}
		
		
		
		$html.= '</tbody>';
		$html .= '</table>';
		return $html;
	}
	
	function getEventPeriodsTable() {
		$selfSchedule = unserialize($_SESSION["selfSchedule"]);
		$html = '';
		if ($selfSchedule != null AND $selfSchedule->getEventList() != null) {
			$eventList = $selfSchedule->getEventList();
			$count = 0;
			foreach ($eventList as $event) {
				
				// Disable Fields 
				$allDayEventDisabled = 'disabled';
				$blockPeriodsDisable = '';
				$addButtonDisabled = '';
				$selfScheduleBoxDisabled = '';
				
				if ($event->allDayFlag == 1) {
					 $allDayEventDisabled = ''; $blockPeriodsDisable = 'disabled';
				}
			//	if ($event->selfScheduleFlag != 1) {
			//		$allDayEventDisabled = 'disabled'; $blockPeriodsDisable = ''; $addButtonDisabled = '';  
			//	}
				if ($selfSchedule->getSelfScheduleOpenFlag() == 1) { 
					$allDayEventDisabled = 'disabled'; $blockPeriodsDisable = 'disabled';  $selfScheduleBoxDisabled = 'disabled'; $addButtonDisabled = 'disabled';
				}

				
				
				$html .= '<table width="100%" class="table table-hover" id="eventPeriodTable"><thead><tr>
				<th width="25%" bgcolor="c8dbeb"><label><h3>'.$event->eventName .'</h3></label></td>
			
				<th class="selfSchedule"><label>Self Schedule?</label><br><input type="checkbox" id="selfScheduleFlag'.$count.'" name="selfScheduleFlag'.$count.'" value="1" '.$selfScheduleBoxDisabled ;
				if ($event->selfScheduleFlag and $event->selfScheduleFlag == 1) $html .= ' checked ';
				$html .= '></th>';
				
				$html .= '<th class="selfSchedule"><label>All Day Event?</label><br><input type="checkbox" id="allDayEventFlag'.$count.'" name="allDayEventFlag'.$count.'" onchange="allDayEventChecked('.$count.','.sizeof($event->periodsList).');"value="1" '.$addButtonDisabled;
				if ($event->allDayFlag and $event->allDayFlag == 1) $html .= ' checked ';
				$html .='></th>
				<th class="selfSchedule"><label>Start Time</label><br>
				<div class="input-group">
				<input type="text" class="form-control timepicker_e_start_time'.$count.'" name="eventStartTime'.$count.'" id="eventStartTime'.$count.'"  onchange="validateDate(this)" value="'.$event->eventStartTime.'" '.$allDayEventDisabled.'>
				<span class="input-group-addon timepicker_e_start_time'.$count.'"><i class="glyphicon glyphicon-time"></i></span>
				</div>

				</th>
				<th class="selfSchedule"><label>Period Length</label><br><input type="text" class="form-control" name="periodLength'.$count.'" id="periodLength'.$count.'" value="'.$event->periodLength.'" '.$allDayEventDisabled.'></th>
				<th class="selfSchedule"><label>Period Interval</label><br><input type="text" class="form-control" name="periodInterval'.$count.'" id="periodInterval'.$count.'" value="'.$event->periodInterval.'" '.$allDayEventDisabled.'></th>
				<th class="selfSchedule"><label>Max Teams per Period</label><br><input type="text" class="form-control" name="teamLimit'.$count.'" id="teamLimit'.$count.'" value="'.$event->teamLimit.'" '.$allDayEventDisabled.'></th>
				</tr><thead>
				<tbody id="eventPeriodTableBody">';
				if ($event->periodsList) {
					$count2 = 0;
					foreach ($event->periodsList as $period) {
						$html .= '<tr><td colspan=3 bgcolor="e8e8e8">' . $period->periodNumber.'</td><td bgcolor="e8e8e8">' . $period->periodStartTime.'</td><td bgcolor="e8e8e8">' . $period->periodEndTime.'</td><td bgcolor="e8e8e8"></td><td bgcolor="e8e8e8">' . $period->teamLimit;
						if ($event->allDayFlag != 1) {	
							$html .= '<div style="float: right;"><button type="button" class="btn btn-xs btn-danger" name="deleteEventPeriod" onclick="deleteEventPrd(this,'.$count.','.$count2.');" value="'.$period->scheduleEventPeriodId.'" '.$addButtonDisabled.'>Delete</button></div>';
						}
						$html .= '</td></tr>';
						$count2++;
					}
				}
				$html .= '</body></table>';
				
				 $html .= '<table class="borderless" width="75%"><tr><td width="25%"><button type="button" class="btn btn-xs btn-primary" name="addEventPeriod'.$count.'" onclick="return addNewEventPeriod(this,'.$count.','.sizeof($event->periodsList).');" value="" '.$addButtonDisabled.'>Add Period</button> <button type="button" class="btn btn-xs btn-danger" name="deleteAllEventPrds" onclick="deleteAllEventPeriods(this,'.$count.');" value="" ' .$addButtonDisabled.'>Delete All</button></td>
				 
				 <td><label for="selectedPeriod'.$count.'">Period: </label></td><td><select class="form-control" name="selectedPeriod'.$count.'" id="selectedPeriod'.$count.'" '.$blockPeriodsDisable.'>
				 <option value=""></option>';
				 if ($selfSchedule->getPeriodList() != null) {
					 foreach ($selfSchedule->getPeriodList() as $presetPeriod) {
						 $html .= '<option value="'.$presetPeriod->getPeriodNumber().'">'.$presetPeriod->getPeriodNumber().') '.$presetPeriod->getStartTime().' - '.$presetPeriod->getEndTime().'</option>';
					 }
				 }
				 			
				 $html .= '</select></td>

				 <td><label for="periodTeamLimit'.$count.'">Max Teams per Period: </label></td><td><input type="number" class="form-control" name="periodTeamLimit'.$count.'" id="periodTeamLimit'.$count.'" value="" size="6" '.$blockPeriodsDisable.'></td></tr></table>
				 <br>';
			$count++;
			}
		}
		
		return $html;
		
	}
	
	function getMyTeams() {
		$selfSchedule = unserialize($_SESSION["selfSchedule"]);
		$html = '';
		$html .= '<div style="float: left; overflow: hidden;"><label><a href="javascript: displayTeams();">Select Team:<br><br></a></label></div><div style="overflow: hidden; padding-left: 4em;" id="scheduleTeamsDiv">';
		$count = 0;
		foreach ($selfSchedule->teamList as $team) {
			if ($team->teamAvailableFlag) {
				if ($count > 0 AND getCurrentRole() != 'COACH') $html .= ', ';
				else if ($count > 0) $html .= '<br>';
				$selected = false;
				$checked = '';
				if ($team->tournTeamId == $selfSchedule->tournTeamSelectedId) $checked = 'checked';
				if (getCurrentRole() == 'COACH') $html .= '<input type="radio" name="tournTeamSelected" onchange="updateSelectedTeam(this)" value="'.$team->tournTeamId.'" '.$checked.'> ';
				if ($team->teamSelectedFlag) $selected = true;
				$html .= '<div style="display: inline-block; white-space: nowrap;';
				if ($selected) $html .= ' background: #c8dbeb; border-radius: 5px; ';
				$html .= '"><a href="javascript: selectScheduleTeam('.$team->tournTeamId.');"><div class="idCircle" style="background: '.getCircleColor($count).';"></div> '.$team->teamName.'</a> ';
				$html .= '</div>';
				$count++;
			}
		}
		
		$html .= '</div><hr style="width: 100%;">';
		return $html;
	}
	
	// 6 colors to define teams
	function getCircleColor($count) {
		$color = 'red';
		switch ($count) {
			case 0: $color = 'red'; break;
			case 1: $color = 'green'; break;
			case 2: $color = 'blue'; break;
			case 3: $color = 'orange'; break;
			case 4: $color = 'black'; break;
			case 5: $color = 'purple'; break;
			case 6: $color = 'yellow'; break;
			case 7: $color = 'pink'; break;
			case 8: $color = 'brown'; break;
			case 9: $color = 'gray'; break;
			
			case 10: $color = '#CC0000'; break;
			case 11: $color = '#666600'; break;
			case 12: $color = '#66FF00'; break;
			case 13: $color = '#CC6699'; break;
			case 14: $color = '#66CCFF'; break;
			case 15: $color = '#FFCC99'; break;
			case 16: $color = '#990099'; break;
			case 17: $color = '#99CC99'; break;
			case 18: $color = '#33CC33'; break;
			case 19: $color = '#330000'; break;
			
			case 20: $color = '#E74C3C'; break;
			case 21: $color = '#8E44AD'; break;
			case 22: $color = '#3498DB'; break;
			case 23: $color = '#16A085'; break;
			case 24: $color = '#58D68D'; break;
			case 25: $color = '#F5B041'; break;
			case 26: $color = '#DC7633'; break;
			case 27: $color = '#CACFD2'; break;
			case 28: $color = '#99A3A4'; break;
			case 29: $color = '#566573'; break;
			
			case 30: $color = '#566573'; break;
			case 31: $color = '#EBDEF0'; break;
			case 32: $color = '#D4E6F1'; break;
			case 33: $color = '#D1F2EB'; break;
			case 34: $color = '#D4EFDF'; break;
			case 35: $color = '#FCF3CF'; break;
			case 36: $color = '#FAE5D3'; break;
			case 37: $color = '#F2F3F4'; break;
			case 38: $color = '#CCFF00'; break;
			case 39: $color = '#CC9900'; break;
			
			case 40: $color = '#641E16'; break;
			case 41: $color = '#512E5F'; break;
			case 42: $color = '#154360'; break;
			case 43: $color = '#0E6251'; break;
			case 44: $color = '#145A32'; break;
			case 45: $color = '#7D6608'; break;
			case 46: $color = '#784212'; break;
			case 47: $color = '#7B7D7D'; break;
			case 48: $color = '#4D5656'; break;
			case 49: $color = '#1B2631'; break;
			
				
		}
		return $color;
	}
	
	function getScheduleOverview() {
		$selfSchedule = unserialize($_SESSION["selfSchedule"]);
		$count = 0;
		$tdwidth = 10;
		$periodMap = array();
		$html = '';
		$html .= '<span class="red">*</span> = Self Schedule Event';
		// print header
		$html .= '<table width="100%"><tr class="selfScheduleDisplay"><td width="20%" bgcolor="c8dbeb" align="center" class="selfScheduleDisplay"></td>';
		if ($selfSchedule != null AND $selfSchedule->getPeriodList()) {
			foreach ($selfSchedule->getPeriodList() as $period) {
				$html .= '<td bgcolor="c8dbeb" align="center" class="selfScheduleDisplay" data-toggle="tooltip" title="'.$period->getStartTime().' - '.$period->getEndTime().'"><h4>'.$period->getStartTime().'</h4></td>';
				array_push($periodMap, $period->getStartTime());
			}
		}		
		$html .= '</tr>';
		if (sizeof($periodMap) > 0) {
			$tdwidth = 80 / sizeof($periodMap);
		}
		
		// Event Rows For Blocked Periods
		if ($selfSchedule != null AND $selfSchedule->getEventList() != null) {
			foreach ($selfSchedule->getEventList() as $event) {
				$ssEvent = '';
				if ($event->selfScheduleFlag) $ssEvent = '<span class="red">*</span>';
				if ($event->scheduleEventId == '') $event->scheduleEventId = '-1';
				if ($event->allDayFlag != 1) {
					$html .= '<tr class="selfScheduleDisplay"><td width="20%" bgcolor="c8dbeb" class="selfScheduleDisplay" style="padding-left: 0.5em;">';
					
				if (isUserAccess(1)) 
					$html .= '<input type="image" src="img/arrow_purple.png" class="downloadImg" name="exportEvent" alt="arrow_purple" height="20" width="20" value="'.$event->scheduleEventId.'"> ';
					$html .= $event->eventName.$ssEvent.'</td>';
					if ($event->periodsList != null) {
						foreach ($periodMap as $map) {
							$exists = false;
							foreach ($event->periodsList as $period) {	
								if ($period->scheduleEventPeriodId == '') $period->scheduleEventPeriodId = '-1';
								if ($map == $period->periodStartTime) {
									if ($event->selfScheduleFlag == 1) {
										$backgroundColor = 'c8dbeb';
										if ($period->slotsOpen == 0) $backgroundColor = 'ebc8c8';
										if (getCurrentRole() == 'COACH') {
										$html .= '<td class="selfScheduleDisplay" align="center" bgcolor="'.$backgroundColor.'" width="'.$tdwidth.'%" data-toggle="tooltip" title="'.$period->periodStartTime.' - '.$period->periodEndTime.'"><a href="javascript: scheduleEventPeriodCoach('.$event->scheduleEventId.','.$period->scheduleEventPeriodId.')">'.$period->slotsOpen.' of '.$period->teamLimit.'</a><br>';
										}
										else {
										$html .= '<td class="selfScheduleDisplay" align="center" bgcolor="'.$backgroundColor.'" width="'.$tdwidth.'%" data-toggle="tooltip" title="'.$period->periodStartTime.' - '.$period->periodEndTime.'"><a href="javascript: scheduleEventPeriod('.$event->scheduleEventId.','.$period->scheduleEventPeriodId.')">'.$period->slotsOpen.' of '.$period->teamLimit.'</a><br>';
										}
										if (true) {
											foreach($selfSchedule->teamList as $team) {
												if ($team->teamAvailableFlag) {
													foreach($team->linkedPeriodsList as $linked) {
														if ($linked == $period->scheduleEventPeriodId AND $team->teamSelectedFlag) {
															$html .= '<div class="idCircle" style="background: '.getCircleColor($team->teamAvailableId).';"></div> ';
															break;
														}
													}
												}
											}
										}
										if (getCurrentRole() == 'COACH') $html .= '</div></a>';
										$html .= '</td>';
									} else {
										$html .= '<td class="selfScheduleDisplay" align="center" bgcolor="c8dbeb" width="'.$tdwidth.'%">Open</td>';
									}
									$exists = true;
									break;
								}
							}
							if (!$exists) {
								$html .= '<td class="selfScheduleDisplay" align="center" bgcolor="e8e8e8" width="'.$tdwidth.'%"></td>';
							}
						}
					}
					else if ($event->periodsList == null OR sizeof($event->periodsList) == 0) {
						foreach ($periodMap as $map) {
							$html .= '<td class="selfScheduleDisplay" align="center" bgcolor="e8e8e8" width="'.$tdwidth.'%"></td>';
						}
					}
					$count++;
					$html .= '</tr>';
				}
			}	
		}
		
		$html .= '</table><br />';
		
		// Event Rows For All Day Events
		if ($selfSchedule != null AND $selfSchedule->getEventList() != null) {
			foreach ($selfSchedule->getEventList() as $event) {
				if ($event->scheduleEventId == '') $event->scheduleEventId = '-1';
				$ssEvent = '';
				if ($event->selfScheduleFlag) $ssEvent = '<span class="red">*</span>';
				$count = 1;
				if ($event->allDayFlag == 1) {
					$html .= '<table width="100%"><tr class="selfScheduleDisplay"><td width="20%" class="selfScheduleDisplay" bgcolor="c8dbeb" style="padding-left: 0.5em;">';
					if (isUserAccess(1)) 
						$html .= '<input type="image" src="img/arrow_purple.png" class="downloadImg" name="exportEvent" alt="arrow_purple" height="20" width="20" value="'.$event->scheduleEventId.'"> ';
					$html .= $event->eventName.$ssEvent.'</td>';
					if ($event->periodsList != null) {
						foreach ($event->periodsList as $period) {
							if ($period->scheduleEventPeriodId == '') $period->scheduleEventPeriodId = '-1';
							if ($count % 21 == 0) { 
								$html .= '</tr><tr class="selfScheduleDisplay"><td width="20%" class="selfScheduleDisplay" bgcolor="FFFFFF" style="padding-left: 0.5em;">&nbsp;</td>';
								$count = 1;
							}
							if ($event->selfScheduleFlag == 1) {
								$backgroundColor = 'c8dbeb';
								if ($period->slotsOpen == 0) $backgroundColor = 'ebc8c8';
								if (getCurrentRole() == 'COACH') {
									$html .= '<td class="selfScheduleDisplay" bgcolor="'.$backgroundColor.'" width="4%" align="center" data-toggle="tooltip" title="'.$period->periodStartTime.' - '.$period->periodEndTime.'"><a href="javascript: scheduleEventPeriodCoach('.$event->scheduleEventId.','.$period->scheduleEventPeriodId.')"><div style="font-size:60%;">'.$period->periodStartTime.'<br>'.$period->slotsOpen.' of '.$period->teamLimit.'</div></a>';
								} 
								else {
								$html .= '<td class="selfScheduleDisplay" bgcolor="'.$backgroundColor.'" width="4%" align="center" data-toggle="tooltip" title="'.$period->periodStartTime.' - '.$period->periodEndTime.'"><a href="javascript: scheduleEventPeriod('.$event->scheduleEventId.','.$period->scheduleEventPeriodId.')"><div style="font-size:60%;">'.$period->periodStartTime.'<br>'.$period->slotsOpen.' of '.$period->teamLimit.'</div></a>';
								}
								if (true) {
									foreach($selfSchedule->teamList as $team) {
										if ($team->teamAvailableFlag) {
											foreach($team->linkedPeriodsList as $linked) {
												if ($linked == $period->scheduleEventPeriodId AND $team->teamSelectedFlag) {
													$html .= '<div class="idCircle" style="background: '.getCircleColor($team->teamAvailableId).';"></div> ';
													break;
												}
											}
										}
									}
								}
								$html .= '</td>';
							}
							else {
								$html .= '<td class="selfScheduleDisplay" width="4%" bgcolor="c8dbeb" align="center" ><div style="font-size:60%;">Open</div></td>';
								//width="'.$tdwidth.'%"
							}
							$count++;	
						}
					}
					$count++;
					$html .= '</tr></table><br>';
				}
			}	
		}

		
		return $html;
	}
	
	function getMySchedule() {
		$selfSchedule = unserialize($_SESSION["selfSchedule"]);
		$myTeams = array();
		$html = '';
		$html .= '<span class="red">*</span> = Self Schedule Event';
		$html .= '<table width="100%" class="borderless"><thead><tr class="selfScheduleDisplay"><td width="20%" bgcolor="c8dbeb" align="center" class="selfScheduleDisplay"></td>';
		foreach ($selfSchedule->teamList as $team) {
			if ($team->teamAvailableFlag) {
				$html .= '<td bgcolor="c8dbeb" align="center" class="selfScheduleDisplay"><h4>'.$team->teamName.'</h4></td>';
				array_push($myTeams, $team);
			}
		}
		$html .= '</tr></thead><tbody>';
		
		foreach ($selfSchedule->getEventList() as $event) {
			$html .= '<tr class="selfScheduleDisplay">';
			$ssEvent = '';
			if ($event->selfScheduleFlag) $ssEvent = '<span class="red">*</span>';
			$html .= '<td bgcolor="c8dbeb" align="left" class="selfScheduleDisplay" style="padding-left: 0.5em;">'.$event->eventName.$ssEvent.'</td>';
			foreach($myTeams as $myTeam) {
				$exists = false;
				foreach ($event->periodsList as $period) {	
					if (in_array($period->scheduleEventPeriodId,$myTeam->linkedPeriodsList)) {
						$html .= '<td bgcolor="c8dbeb" align="center" class="selfScheduleDisplay">'.$period->periodStartTime.' - '.$period->periodEndTime.'</td>';
						$exists = true;
						break;
					}
				}
				if (!$exists AND $event->selfScheduleFlag) {
					$html .= '<td bgcolor="e8e8e8" align="center" class="selfScheduleDisplay">Not Registered</td>';
				}
				else if (!$exists) {
					$html .= '<td bgcolor="e8e8e8" align="center" class="selfScheduleDisplay">No Self Schedule</td>';
				}
			}

			$html .= '</tr>';
		}
		
		$html .= '</tbody></table>';
		return $html;
	}
	
	function getPeriodScheduler() {
		$selfSchedule = unserialize($_SESSION["selfSchedule"]);
		$scheduleEventPeriodId = $selfSchedule->currentPeriodId;
		$period = null;
		$event = null;
		foreach ($selfSchedule->getEventList() as $event1) {
			foreach ($event1->periodsList as $period1) {
				if ($period1->scheduleEventPeriodId == $scheduleEventPeriodId) {
					$period = $period1;
					$event = $event1;
					break;
				}
			}
		}
		
		$html = '';
		// Header		
		$html .= '<h1 style="margin-bottom: 5px;">Self Schedule: '.$event->eventName.' ('.$period->periodStartTime.' - '.$period->periodEndTime.')</h1>';
		$html .= getSSTournamentHeader();
		$html .= '<div style="float: left; overflow: hidden;"><button type="submit" class="btn btn-xs btn-primary" name="cancelSelfSchedulePeriod">Cancel</button></div>';
		$html .= '<h6><div style="padding-left: 5em; overflow: hidden;">1.To schedule your team, click the add button. <br>2. To remove your team from the schedule, click remove.<br><br>Each team can only be scheduled once per event. Every action you make will be saved automatically. Other teams may be scheduling at the same time. You will receive an error message if there are no remaining slots.</div></h6>';
		$html .= '<hr>';
		
		$html .= '<div style="float: left; overflow: hidden; width: 40%;">';
		$html .= '<table class="table table-hover" id="availableTeamsTable"><thead><tr>
		<th data-field="number" data-align="right" data-sortable="true">#</th>
		<th data-field="number" data-align="right" data-sortable="true">Teams</th>
		<th data-field="number" data-align="right" data-sortable="true">Action</th>
		</tr></thead>';
		$html .= '<tbody id="teamTableBody">';
		
		if ($selfSchedule->teamList) {
			foreach ($selfSchedule->teamList as $team) {
				if (!$team->teamLinkedToEventFlag) {
					$html .= '<tr><td>'.$team->teamNumber.'</td>';
					$html .= '<td>'.$team->teamName.'</td>';
					$html .= '<td>';
					if ($team->teamAvailableFlag) {
						$html .= '<button type="button" class="btn btn-xs btn-danger" name="addTeamEventPeriod" onclick="addTeamPeriod(this,'.$team->tournTeamId.','.$period->scheduleEventPeriodId.');" value=""' .$addButtonDisabled.'">Add</button>';
					}
					$html .= '</td></tr>';
				}
			
			}
		}
		$html .= '</tbody></table>';
		$html .= '</div>';
		$html .= '<div style="overflow: hidden; width: 60%; padding-left: 10%;">';
		$html .= '<table class="table table-hover" id="scheduledTeamsTable" ><thead><tr>
		<th data-field="number" data-align="right" data-sortable="true"></th>
		<th data-field="number" data-align="right" data-sortable="true">Scheduled Teams</th>
		<th data-field="number" data-align="right" data-sortable="true">Actions</th>
		</tr></thead>';
		$html .= '<tbody id="scheduledTeamsTableBody">';
		$count = 1;
		$teamPosition = 0;
		$teamLimit = $period->teamLimit;
		
		while ($count <= $teamLimit) {
			$team = null;
			$html .= '<tr><td>'.$count.'</td>';
			
			// Get Next Team Signed Up
			$tmpTeamPosition = 0;
			foreach ($selfSchedule->teamList as $team1) {
				if ($team1->teamLinkedToEventFlag) {
					if ($tmpTeamPosition == $teamPosition) {
						$team = $team1;
						$teamPosition++;
						break;
					}
					$tmpTeamPosition++;
				}
			}
			
			if ($team) {
				$html .= '<td>'.$team->teamName.'</td>';				
				$html .= '<td>';
				if ($team->teamAvailableFlag) {
					$html .= '<button type="button" class="btn btn-xs btn-danger" name="removeTeamEventPeriod" onclick="removeTeamPeriod(this,'.$team->tournTeamId.','.$period->scheduleEventPeriodId.','.$team->scheduleTeamId.');" value=""' .$addButtonDisabled.'">Remove</button>';
				}
				$html .= '</td></tr>';
			}
			else {
				$html .= '<td></td><td></td>';
			}
			
			
			
			/**else {
				$html .= '<tr><td>'.$count.'</td>';
				$html .= '<td></td>';
				$html .= '<td></td></tr>';
			}**/
			$count++;
		}
		$html .= '</tbody></table>';
		$html .= '</div>';
		
		return $html;
	}
	
	function setSelectedSelfScheduleTeam($tournTeamId) {
		$selfSchedule = unserialize($_SESSION["selfSchedule"]);
		$selfSchedule->tournTeamSelectedId = $tournTeamId;
		$_SESSION["selfSchedule"] = serialize($selfSchedule);	
	}
	
	// Generate Schedule Overiew PDF
	function exportScheduleOverview() {
		$selfSchedule = unserialize($_SESSION["selfSchedule"]);
		$periodMap = array();
		
		$pdf = new FPDF();
		$pdf->SetTitle('Tournament Schedule Overview', true);
		$pdf->AddPage('L');
		$pdf->SetAutoPageBreak(true, 1); 
		
		// OVERVIEW HEADER
		$pdf->SetFont('Arial','',18);
		$pdf->SetTextColor(0);
		$pdf->Cell(0,10,$selfSchedule->getTournamentName().' Schedule',0,0,'L');
		$pdf->Ln();

		$pdf->SetFont('Arial','',9);
		$pdf->SetTextColor(0);
		$pdf->SetFillColor(200,219,235);
		$pdf->SetDrawColor(255,255,255);
		$pdf->setLineWidth(.3);	
		$pdf->Cell(40,6,'',1,0,'C',true);
		if ($selfSchedule->getPeriodList()) {	
			foreach ($selfSchedule->getPeriodList() as $period) {
				$pdf->Cell(20,6,$period->getStartTime(),1,0,'C',true);
				array_push($periodMap, $period->getStartTime());
			}
		}
		$pdf->Ln();
		
		// OVERVIEW BLOCK SCHEDULE -
		foreach ($selfSchedule->getEventList() as $event) {
			if ($event->allDayFlag != 1) {
				$pdf->SetFillColor(200,219,235);
				$pdf->Cell(40,6,$event->eventName,1,0,'L',true);
				foreach ($periodMap as $map) {
					$exists = false;
					foreach ($event->periodsList as $period) {	
						if ($map == $period->periodStartTime) {
							if ($event->selfScheduleFlag == 1) {
								$pdf->SetFillColor(200,219,235);
								$pdf->Cell(20,6,$period->teamLimit. ' Teams',1,0,'C',true);
							}
							else {
								$pdf->SetFillColor(200,219,235);
								$pdf->Cell(20,6,'Open',1,0,'C',true);
							}
							$exists = true;
						} 
					}
					if (!$exists) {
						$pdf->SetFillColor(232,232,232);
						$pdf->Cell(20,6,'',1,0,'C',true);
					}
				}
				$pdf->Ln();
			}
		}
		$pdf->Ln();
		// OVERVIEW ALL DAY SCHEDULE -
		foreach ($selfSchedule->getEventList() as $event) {
			$count = 1;
			if ($event->allDayFlag == 1) {
				$pdf->SetFont('Arial','',9);
				$pdf->SetFillColor(200,219,235);
				$pdf->Cell(40,6,$event->eventName,1,0,'L',true);
				foreach ($event->periodsList as $period) {	
					if ($count % 21 == 0) { 
						$pdf->Ln();
						$pdf->SetFillColor(255,255,255);
						$pdf->Cell(40,6,'',1,0,'L',true);
					}
						if ($event->selfScheduleFlag == 1) {
							$pdf->SetFont('Arial','',4);
							$pdf->SetFillColor(200,219,235);
							$pdf->Cell(8,6,$period->periodStartTime,1,0,'C',true);
						}
						else {
							$pdf->SetFont('Arial','',4);
							$pdf->SetFillColor(200,219,235);
							$pdf->Cell(8,6,$period->periodStartTime,1,0,'C',true);
						}
					$count++;	
				}
				$pdf->Ln();$pdf->Ln();
			}
		}

		$filename = str_replace(' ','_',$selfSchedule->getTournamentName());
		$pdf->Output('D', $filename.'_Schedule.pdf',true);
	}
	
	function exportEventSchedule($scheduleEventId) {
		$selfSchedule = unserialize($_SESSION["selfSchedule"]);
		if ($scheduleEventId == -1) return;
		
		foreach ($selfSchedule->getEventList() as $event) {
			if ($scheduleEventId == $event->scheduleEventId) {
				$filename = $event->eventName. "_Schedule.csv";
			  	header("Content-Disposition: attachment; filename=\"$filename\"");
			  	header("Content-Type: text/csv; charset=utf-8");
			  	$output = fopen('php://output', 'w');
			  	
			  	// Event Title
			  	$title = array($selfSchedule->getTournamentName() .': '.$event->eventName . '');
			  	fputcsv($output, $title);
			  	fputcsv($output, array());
			  	
			  	// Periods
			  	if ($event->periodsList != null) {
			  		foreach($event->periodsList as $period) {
				  		// Team List For Period
				  		$teams = array();
				  		foreach ($selfSchedule->teamList as $team) {
							if (in_array($period->scheduleEventPeriodId, $team->linkedPeriodsList)) {
								array_push($teams, array($team->teamName,$team->teamNumber));
							}
						}
				  		
				  		fputcsv($output, array($period->periodStartTime.' - '.$period->periodEndTime));	
				  		if ($event->selfScheduleFlag) {
					  		for ($i = 1; $i <= $period->teamLimit; $i++) {
						  		$teamName = '';
						  		$teamNumber = '';
						  		if (sizeof($teams) >= $i) {
							  		$x = $teams[$i-1];
							  		$teamName = $x[0];
							  		$teamNumber = $x[1];
						  		}
	
						  		$row = array($i.': '.$teamName,$teamNumber); 
						  		fputcsv($output, $row);
					  		}	
				  		}
				  		else {
					  		fputcsv($output, array('Open',''));
				  		}			  		
				  		fputcsv($output, array());
			  		}
			  	
			  	
			  	}
			  	fclose($output);
			  	exit;
				break;				
			}
		}
	}
	
	function exportAllEventsSchedule() {
		$selfSchedule = unserialize($_SESSION["selfSchedule"]);
		$filename = $selfSchedule->getTournamentName(). "_Event_Schedules.csv";
		header("Content-Disposition: attachment; filename=\"$filename\"");
		header("Content-Type: text/csv; charset=utf-8");
		$output = fopen('php://output', 'w');
		
		fputcsv($output, array($filename));
		fputcsv($output, array());
			  	
		foreach ($selfSchedule->getEventList() as $event) {
			  	// Event Title
			  	$title = array($event->eventName . '');
			  	fputcsv($output, $title);
			  	
			  	// Periods
			  	if ($event->periodsList != null) {
			  		foreach($event->periodsList as $period) {
				  		// Team List For Period
				  		$teams = array();
				  		foreach ($selfSchedule->teamList as $team) {
							if (in_array($period->scheduleEventPeriodId, $team->linkedPeriodsList)) {
								array_push($teams, array($team->teamName,$team->teamNumber));
							}
						}
				  		
				  		fputcsv($output, array($period->periodStartTime.' - '.$period->periodEndTime));	
				  		if ($event->selfScheduleFlag) {
					  		for ($i = 1; $i <= $period->teamLimit; $i++) {
						  		$teamName = '';
						  		$teamNumber = '';
						  		if (sizeof($teams) >= $i) {
							  		$x = $teams[$i-1];
							  		$teamName = $x[0];
							  		$teamNumber = $x[1];
						  		}
	
						  		$row = array($i.': '.$teamName,$teamNumber); 
						  		fputcsv($output, $row);
					  		}
				  		}	
				  		else {
					  		fputcsv($output, array('Open',''));	
				  		}		  		
				  		fputcsv($output, array());
			  		}
			  	
			  	
			  	}
			  	fputcsv($output, array());
			  	fputcsv($output, array());
			}
		fclose($output);
		exit;
	}
	
	function exportMySchedule() {
		$selfSchedule = unserialize($_SESSION["selfSchedule"]);
		$myTeams = array();
		
		$filename = $selfSchedule->getTournamentName(). "_My_Schedule.csv";
		header("Content-Disposition: attachment; filename=\"$filename\"");
		header("Content-Type: text/csv; charset=utf-8");
		$output = fopen('php://output', 'w');
		$header = array();
		array_push($header, $selfSchedule->getTournamentName());
		
		foreach ($selfSchedule->teamList as $team) {
			if ($team->teamAvailableFlag) {
				array_push($myTeams, $team);
				array_push($header, $team->teamName);
			}
		}
		
		fputcsv($output, $header);
		fputcsv($output, array());
		
		foreach ($selfSchedule->getEventList() as $event) {
			$row = array();
			$ssEvent = '';
			if ($event->selfScheduleFlag) $ssEvent = '*';
			array_push($row,$event->eventName.$ssEvent);
			foreach($myTeams as $myTeam) {
				$exists = false;
				foreach ($event->periodsList as $period) {	
					if (in_array($period->scheduleEventPeriodId,$myTeam->linkedPeriodsList)) {
						array_push($row, $period->periodStartTime .' - '.$period->periodEndTime);
						$exists = true;
						break;
					}
				}
				if (!$exists AND $event->selfScheduleFlag) {
					array_push($row, 'Not Registered');
				}
				else if (!$exists) {
					array_push($row, 'No Self Schedule');
				}
			}
			fputcsv($output, $row);	
		}
		fclose($output);
		exit;
	}
	
	
?>