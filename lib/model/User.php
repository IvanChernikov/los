<?php
class User extends ModelBase {
	// ID in ModelBase
	public $Username;
	public $Password;
	public $Email;
	public $FirstName;
	public $LastName;
	public $TimeCreated;
	public $TimeLoggedOn;
	public $AccessLevel;
	
	/*
		checks is username and password are okay for login
		returns UserID if valid credentials
			otherwise false and sets the following flags if FALSE
				'UsernameInvalid'
				'PasswordInvalid'
	*/
	public static function ValidateLogin($username, $password) {
		$options = array('match' => array('Username' => $username));
		$result = static::LoadAll($options);
		if (count($result) !== 1) {
			Flag::Set('UsernameInvalid');
			return false;
		}
		if ($result[0]->Password !== sha1($password)) {
			Flag::Set('PasswordInvalid');
			return false;
		}
		$result[0]->TimeLoggedOn = date('Y-m-d H:i:s', time());
		$result[0]->Save();
		return $result[0]->ID;
	}
	
	public function GetPostCount() {
		$db = static::GetDB();
		$query = 'SELECT count(*) FROM WallPost WHERE AuthorID = :ID';
		$stm = $db->prepare($query);
		$params = array(':ID' => $this->ID);
		if ($stm->execute($params)) {
			return $stm->fetchColumn();
		} else {
			return 0;
		}
	}
	public function GetReplyCount() {
		$db = static::GetDB();
		$query = 'SELECT count(*) FROM WallReply WHERE AuthorID = :ID';
		$stm = $db->prepare($query);
		$params = array(':ID' => $this->ID);
		if ($stm->execute($params)) {
			return $stm->fetchColumn();
		} else {
			return 0;
		}
	}
	
	public function GetUploadCount() {
		$db = static::GetDB();
		$query = 'SELECT count(*) FROM FileObject WHERE UploaderID = :ID';
		$stm = $db->prepare($query);
		$params = array(':ID' => $this->ID);
		if ($stm->execute($params)) {
			return $stm->fetchColumn();
		} else {
			return 0;
		}
	}
	public function GetRecentUploads() {
		
	}
}