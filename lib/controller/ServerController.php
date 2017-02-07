<?php
class ServerController extends ControllerBase {
	public function __construct() {
		parent::__construct();
		//  close session to avoid locking
		session_write_close();
		//  this class always returns JSON
		header('Content-Type: application/json');
	}
	public function index(){
		Router::Error(403);
	}
	public function getStatus($id) {
        $Server = Server::LoadByID($id);
		echo json_encode($Server->Handler->GetStatus());
    }
	public function getInfo($id) {
		$Server = Server::LoadByID($id);
		echo json_encode($Server->Handler->GetInfo());
	}
}