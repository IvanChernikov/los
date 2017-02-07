<?php
class UserController extends ControllerBase {
	public function index() {
		if (Auth::UserAuthenticated()) {
			Router::Route('/user/profile/' . Auth::GetCurrentUserID());
		} else {
			Router::Route('/user/login/');
		}
	}

	public function profile($id = null) {
		if (Auth::UserAuthenticated()) {
			$id = ($id === null ? Auth::GetCurrentUserID() : $id);
			$User = User::LoadByID($id);

			$this->View->AddData('User',$User);
			$this->View->AddData('PostCount',$User->GetPostCount());
			$this->View->AddData('ReplyCount',$User->GetReplyCount());
			$this->View->AddData('UploadCount',$User->GetUploadCount());
		} else {
			Router::Route('/user/login/');
		}
	}
	
	public function login() {
		if (Auth::UserAuthenticated()) {
			Router::Route('/user/profile');
			die();
		}
		$id = null;
		if (Flag::Get('IsPost') && 
			$id = User::ValidateLogin($_POST['Username'],$_POST['Password'])) {
			Auth::Authenthicate($id);
			Router::Redirect('/home/index/');
			die();
		}
	}
	public function logout() {
		Auth::TerminateSession();
		Router::Redirect('/home/index/');
	}
	public function register() {
		if (Auth::UserAuthenticated()) {
			Router::Route('/user/profile');
			die();
		}
		if (Flag::Get('IsPost')) {
			if ($this->verify('Username',$_POST['Username']) && 
				$this->verify('Email', $_POST['Email']) && 
				$_POST['Password'] === $_POST['PasswordVerify']) {
				// All is good, register me please
				$User = new User();
				$User->Username = $_POST['Username'];
				$User->Password = sha1($_POST['Password']);
				$User->Email = $_POST['Email'];
				$User->FirstName = $_POST['FirstName'];
				$User->LastName = $_POST['LastName'];
				$User->AccessLevel = 1;
				$User->TimeCreated = date('Y-m-d H:i:s', time());
				$User->TimeLoggedOn = date('Y-m-d H:i:s', time());
				$User->Save();
				// Perfect, log me in now
				$this->login();
			}
		}
	}
	public function test($id) {
		Auth::Authenthicate($id);
	}
	private function CheckRegistration() {
	}
	public function verify($key, $value) {
		$isValid = true;
		switch($key) {
			case 'Username':
				$isValid = preg_match('/^[A-Za-z][A-Za-z0-9]{5,23}$/',$value);
				break;
			case 'Email':
				$isValid = filter_var($value,FILTER_VALIDATE_EMAIL);
				break;
			default:
				$isValid = false;
		}
		
		if ($isValid) {
			$options = array('match' => array($key => $value));
			$isValid = (count(User::LoadAll($options)) == 0);
		}
		
		echo json_encode($isValid);
		return $isValid;
	}
}