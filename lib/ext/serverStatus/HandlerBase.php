<?php
class HandlerBase {
	protected $Server;
	
	public function __construct($Server) {
		$this->Server = $Server;
	}
	public function GetStatus() {
		return array('id' => $this->Server->ID, 'status' => false, 'count' => 'ERROR');
	}
	public function GetInfo() {
		return array('id' => $this->Server->ID, 'html' => '503 Server Error');
	}
}