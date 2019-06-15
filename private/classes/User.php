<?php

class User {


	public static function getUserId() {
		return $_SESSION['id'];
	}



	public static function getUsername() {
		return $_SESSION['username'];
	}



	public static function isLoggedIn() {
		return isset($_SESSION['username']);
	}



	public static function isAdmin() {
		return (isset($_SESSION['type']) and strcmp($_SESSION['type'], 'Administrator') == 0);
	}



	public static function isModerator() {
		return (isset($_SESSION['type']) and strcmp($_SESSION['type'], 'Moderator') == 0);
	}


	public static function isRegularUser(){
		return (isset($_SESSION['type']) and strcmp($_SESSION['type'], 'Korisnik') == 0);
	}



	public static function login($username, $id, $userType, $setCookie) {
		$_SESSION['username'] = $username;
		$_SESSION['id'] = $id;
		$_SESSION['type'] = $userType;
		//TODO: cookie
	}



	public static function logout(){
		session_unset();
		session_destroy();
		DatabaseHandler::getInstance()->disconnect();
		//TODO: cookie
	}

}