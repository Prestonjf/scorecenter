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
    
	
// USER SESSION INFO OBJECT
class UserSessionInfo {
	
   private $authenticatedFlag;
   private $userId;
   private $userName;
   private $firstName;
   private $lastName;
   private $role;
   private $phoneNumber;
   private $domain;
   private $state;
   private $teamsCoached = array();
   
   
 
   public function __construct($userName) {
      $this->userName = $userName;
	  $this->loadUserSessionInfo();
   }
	
	public function setAuthenticatedFlag($authenticatedFlag) {
		$this->authenticatedFlag = $authenticatedFlag;
	}
	public function getAuthenticatedFlag() {
		return $this->authenticatedFlag;
	}
	
	public function setUserName($userName) {
		$this->userName = $userName;
	}
	public function getUserName() {
		return $this->userName;
	}
	
	public function setFirstName($firstName) {
		$this->firstName = $firstName;
	}
	public function getFirstName() {
		return $this->firstName;
	}
	
	public function setLastName($lastName) {
		$this->lastName = $lastName;
	}
	public function getLastName() {
		return $this->lastName;
	}
	
	public function setRole($role) {
		$this->role = $role;
	}
	public function getRole() {
		return $this->role;
	}
	
	public function setUserId($userId) {
		$this->userId = $userId;
	}
	public function getUserId() {
		return $this->userId;
	}
	
	public function setPhoneNumber($phoneNumber) {
		$this->phoneNumber = $phoneNumber;
	}
	public function getPhoneNumber() {
		return $this->phoneNumber;
	}
	
	public function setDomain($domain) {
		$this->domain = $domain;
	}
	public function getDomain() {
		return $this->domain;
	}
	
	public function setState($state) {
		$this->state = $state;
	}
	public function getState() {
		return $this->state;
	}
	public function setTeamsCoached($teamsCoached) {
		$this->teamsCoached = $teamsCoached;
	}
	public function getTeamsCoached() {
		return $this->teamsCoached;
	}
   
   public function loadUserSessionInfo() {
	   
	   
	   
   }
}

// SLIDESHOW SLIDE OBJECT
class slideshowSlide {
	
   public $type;
   public $teamNames = array();
   public $headerText;
   public $headerText2;
   public $logoPath;
   public $text;
   public $animationPosition = 0;
   public $labelValues = array(); 
   
   
   public function __construct() {
	
   }
	
	public function setType($type) {
		$this->type = $type;
	}
	public function getType() {
		return $this->type;
	}
	
	public function setTeamNames($teamNames) {
		$this->teamNames = $teamNames;
	}
	public function getTeamNames() {
		return $this->teamNames;
	}
	
	public function setHeaderText($headerText) {
		$this->headerText = $headerText;
	}
	public function getHeaderText() {
		return $this->headerText;
	}
	
	public function setHeaderText2($headerText2) {
		$this->headerText2 = $headerText2;
	}
	public function getHeaderText2() {
		return $this->headerText2;
	}
	
	public function setLogoPath($logoPath) {
		$this->logoPath = $logoPath;
	}
	public function getLogoPath() {
		return $this->logoPath;
	}
	
	public function setText($text) {
		$this->text = $text;
	}
	public function getText() {
		return $this->text;
	}
	
	public function setAnimationPosition($animationPosition) {
		$this->animationPosition = $animationPosition;
	}
	public function getAnimationPosition() {
		return $this->animationPosition;
	}
	
	public function setLabelValues($labelValues) {
		$this->labelValues = $labelValues;
	}
	public function getLabelValues() {
		return $this->labelValues;
	}
   
   public function loadSlideShowObject() {
	   
	   
	   
   }
}

class tournamentResultHeader {
	public $eventName;
	public $trialEventFlag;
	public $completedFlag;
	public $tournEventId;
	
	public function __construct() {
	
   }	
}

class EventResult {
	public $eventName;
	public $teamList = array();
	
	public function __construct() {
	
   }
}

// SELF SCHEDULE OBJECT
class selfSchedule {
	
   private $tournamentId;
   private $tournamentName;
   private $tournamentDate;
   private $tournamentDivision;
   private $tournamentLocation;
   private $tournamentScheduleId;
   private $startTime;
   private $endTime;
   private $selfScheduleOpenFlag = false;
   private $periodList;
   private $eventList;
   public $teamList = array();
   public $noTeams = array();
   public $reservedEventPeriods = array();
   public $reservedSelected = false;
   public $currentPeriodId;
   public $tournTeamSelectedId;
   public $selfScheduleAlternateTeamFlag = false;
  
   
   public function __construct() {
	
   }
   
   	public function setTournamentId($tournamentId) {
		$this->tournamentId = $tournamentId;
	}
	public function getTournamentId() {
		return $this->tournamentId;
	}
	
		public function setTournamentName($tournamentName) {
		$this->tournamentName = $tournamentName;
	}
	public function getTournamentName() {
		return $this->tournamentName;
	}
	
		public function setTournamentDate($tournamentDate) {
		$this->tournamentDate = $tournamentDate;
	}
	public function getTournamentDate() {
		return $this->tournamentDate;
	}
	
		public function setTournamentDivision($tournamentDivision) {
		$this->tournamentDivision = $tournamentDivision;
	}
	public function getTournamentDivision() {
		return $this->tournamentDivision;
	}
	
		public function setTournamentLocation($tournamentLocation) {
		$this->tournamentLocation = $tournamentLocation;
	}
	public function getTournamentLocation() {
		return $this->tournamentLocation;
	}
	
		public function setTournamentScheduleId($tournamentScheduleId) {
		$this->tournamentScheduleId = $tournamentScheduleId;
	}
	public function getTournamentScheduleId() {
		return $this->tournamentScheduleId;
	}
	
		public function setStartTime($startTime) {
		$this->startTime = $startTime;
	}
	public function getStartTime() {
		return $this->startTime;
	}
	
		public function setEndTime($endTime) {
		$this->endTime = $endTime;
	}
	public function getEndTime() {
		return $this->endTime;
	}
	
	public function setPeriodList($periodList) {
		$this->periodList = $periodList;
	}
	public function getPeriodList() {
		return $this->periodList;
	}
	
		public function setSelfScheduleOpenFlag($selfScheduleOpenFlag) {
		$this->selfScheduleOpenFlag = $selfScheduleOpenFlag;
	}
	public function getSelfScheduleOpenFlag() {
		return $this->selfScheduleOpenFlag;
	}
	
		public function setEventList($eventList) {
		$this->eventList = $eventList;
	}
	public function getEventList() {
		return $this->eventList;
	}
}

// SELF SCHEDULE PERIOD OBJECT
class selfSchedulePeriod {
	private $schedulePeriodId;
	private $startTime;
	private $endTime;
	private $periodInterval;
	private $periodNumber;
	
	public function __construct() {
	
   	}
	public function setSchedulePeriodId($schedulePeriodId) {
		$this->schedulePeriodId = $schedulePeriodId;
	}
	public function getSchedulePeriodId() {
		return $this->schedulePeriodId;
	}
	public function setStartTime($startTime) {
		$this->startTime = $startTime;
	}
	public function getStartTime() {
		return $this->startTime;
	}
	
	public function setEndTime($endTime) {
		$this->endTime = $endTime;
	}
	public function getEndTime() {
		return $this->endTime;
	}
	
	public function setPeriodInterval($periodInterval) {
		$this->periodInterval = $periodInterval;
	}
	public function getPeriodInterval() {
		return $this->periodInterval;
	}
	
	public function setPeriodNumber($periodNumber) {
		$this->periodNumber = $periodNumber;
	}
	public function getPeriodNumber() {
		return $this->periodNumber;
	}
}

// SELF SCHEDULE EVENT OBJECT
class selfScheduleEvent {
	public $tournEventId;
	public $scheduleEventId;
	public $eventName;
	public $allDayFlag;
	public $periodsList;
	public $periodLength;
	public $periodInterval;
	public $teamLimit;
	public $selfScheduleFlag;
	public $eventStartTime;

	
	public function __construct() {
	
   }	
}

// SELF SCHEDULE EVENT PERIOD OBJECT
class selfScheduleEventPeriod {
	public $scheduleEventPeriodId;
	public $scheduleEventId;
	public $schedulePeriodId;
	public $allDayFlag;
	public $periodStartTime;
	public $periodEndTime;
	public $periodInterval;
	public $teamLimit;
	public $slotsOpen = 0;
	public $periodNumber;

	
	public function __construct() {
	
   }	
}

   // SELF SCHEDULE TEAM
class selfScheduleTeam {
	  public $teamId;
	  public $tournTeamId;
	  public $teamName;
	  public $teamNumber;
	  public $scheduledTime;
	  public $scheduleTeamId;
	  public $teamLinkedToEventFlag = false;
	  public $teamAvailableFlag = false;
	  public $teamAvailableId = 0;
	  public $teamSelectedFlag = false;
	  public $linkedPeriodsList = array();
	
	public function __construct() {
	
   }  
}

// NAVIGATION HANDLER
class navigationHandler {
	public $toPath;
	public $fromPath;
	public $pathCain = array();
	public $command;
	public $parameters = array();
	
	
	public function __construct() {
	
   } 
}




?>