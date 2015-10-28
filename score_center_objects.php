<?php

// USER SESSION INFO OBJECT
class UserSessionInfo {
	
   private $authenticatedFlag;
   private $userId;
   private $userName;
   private $firstName;
   private $lastName;
   private $role;
   private $phoneNumber;
   
   
 
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
   
   public function loadUserSessionInfo() {
	   
	   
	   
   }
}

// SLIDESHOW SLIDE OBJECT
class slideshowSlide {
	
   private $type;
   private $teamNames = array();
   private $headerText;
   private $headerText2;
   private $logoPath;
   private $text;
   private $animationPosition;
   
   
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
   
   public function loadSlideShowObject() {
	   
	   
	   
   }
}
?>