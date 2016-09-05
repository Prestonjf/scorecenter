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
 * @version: 1.16.2, 09.05.2016 
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
?>