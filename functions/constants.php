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
	
	
/** Declared Tournament Score Center Constant **/

// Error Messages
define('ERROR_SELF_SCHEDULE_1', '<strong>Cannot Save Schedule: </strong>All required fields must be entered.');
define('ERROR_SELF_SCHEDULE_PERIOD_ADD', '<strong>Cannot Add Period: </strong>All period criteria must be entered.');	
define('ERROR_SELF_SCHEDULE_EVENT_PERIOD_ADD', '<strong>Cannot Add Period: </strong>Period already exists for this event.');	
define('ERROR_SELF_SCHEDULE_EVENT_PERIOD_DELETE', '<strong>Cannot Delete Period: </strong>A Team is already scheduled for this period.');
define('ERROR_SELF_SCHEDULE_EVENT_PERIODS_DELETE', '<strong>Cannot Delete Periods: </strong>A Team is already scheduled for one of these periods.');
define('ERROR_SELF_SCHEDULE_Add_TEAM_0', '<strong>Cannot Schedule Team: </strong>Self Scheduling is currently closed.');
define('ERROR_SELF_SCHEDULE_Add_TEAM_1', '<strong>Cannot Schedule Team: </strong>This period already has the maximum amount of teams scheduled.');
define('ERROR_SELF_SCHEDULE_Add_TEAM_2', '<strong>Cannot Schedule Team: </strong>This team is already scheduled for this event in another period.');
define('ERROR_SELF_SCHEDULE_SCHEDULE_TEAM_1', '<strong>Cannot Schedule Team: </strong>Self Scheduling is currently closed.');
define('ERROR_SELF_SCHEDULE_SCHEDULE_TEAM_2', '<strong>Cannot Schedule Team: </strong>This period is currently unavailable.');

define('ERROR_TOURNAMENT_ADD_VERIFIER','<strong>Cannot Add Verifier:</strong> Verifier has already been added.');


	
	
// Success Messages
define('SUCCESS_SELF_SCHEDULE_PERIOD_DELETED', '<strong>Deleted: </strong>Period has been deleted.');
define('SUCCESS_SELF_SCHEDULE_PERIODS_DELETED', '<strong>Deleted: </strong>Periods have been deleted.');			
define('SUCCESS_SELF_SCHEDULE_SAVED', '<strong>Saved: </strong>Self Schedule has been saved.');		
	
	
// String Split Token
define ('STRING_SPLIT_TOKEN','%***|***|***|***%');	
?>