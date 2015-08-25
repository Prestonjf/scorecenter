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
?>