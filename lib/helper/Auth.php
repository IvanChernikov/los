<?php
class Auth {
	/* AccessLevel constants */
	const LEVEL_BANNED = 0;
	const LEVEL_REGULAR = 1;
	const LEVEL_EDITOR = 2;
	const LEVEL_MODERATOR = 3;
	const LEVEL_ADMINISTRATOR = 4;
	const LEVEL_WEBMASTER = 5;
	
	//  Returns string equivalent of $User->AccessLevel (ENUMERATOR)
	public static function GetAccessLevelString($level) {
		$lookup = array('Banned','Regular','Editor','Moderator','Administrator','Webmaster');
		return $lookup[$level];
	}
	// Checks if user is logged in
	public static function UserAuthenticated() {
		return isset($_SESSION['UsedID']);
	}
	// Checks if user has appropriate AccessLevel
	public static function UserAuthorized($level) {
		return (Auth::UserAuthenticated() && Auth::GetCurrentUser()->AccessLevel >= $level);
	}
	// Logs User in and write UsedID to $_SESSION
	public static function Authenthicate($id) {
		$_SESSION['UsedID'] = $id;
	}
	// Kills session, logging the user out
	public static function TerminateSession() {
		session_destroy();
	}
	// Returns UserID from $_SESSION or NULL if user not logged in
	public static function GetCurrentUserID() {
		return (Auth::UserAuthenticated() ? $_SESSION['UsedID'] : null);
	}
	// Return User instance or false if user not logged in or NULL if user not logged in
	public static function GetCurrentUser() {
		return (Auth::UserAuthenticated() ? User::LoadById(Auth::GetCurrentUserID()) : null);
	}

}