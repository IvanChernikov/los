<?php
class HandlerLeague extends HandlerBase {
	/*
	public function lol($id) {
		session_write_close();
		$lolResponse = json_decode(file_get_contents('http://status.leagueoflegends.com/shards/na'));
		$lolStatus = false;
		if (isset($lolResponse->services[1]) && $lolResponse->services[1]->status == 'online') {
			$lolStatus = true; 
		}
		echo json_encode(array($lolStatus,'N/A'));
	}
	*/
}