<?php 

/*
 * Just for simple testing and demonstration purposes
 */

class qe_User {
  
  private $id;
  private $username;
  private $email;
  private $password;
  
  public function __construct ($username, $password) {
	echo '<p>Constructor Called</p>';
	$this->username = $username;
	$this->password = $password;
  }
  
  public function register () {
	// Test
	echo '<p>User registered</p>';
  }
  
  public function login () {
	$this->auth_user();
  }
 
  public function auth_user () {
	// Test
	echo '<p>' .$this->username. ' is authenticated</p>';
  }

  public function __destruct () {
	echo '<p>Destructor Called</p>';
  }

}

class qe_Post {
  
  private $name;
  
  public function __set ($name, $value) {
	echo '<p>Setting ' .$name. ' to <strong>' .$value. '</strong></p>';
	$this->$name = $value;
  }
  
  public function __get ($name) {
	echo '<p>Getting ' .$name. ' <strong>' .$this->name. '</strong></p>';
	return ($this->$name);
  }
  
  public function __isset ($name) {
	echo '<p>Is ' .$name. ' set?</p>';
	return (isset($this->name));
  }
  
}