<?php
class HandlerTeamspeak extends HandlerBase {
	public function GetStatus() {
		$status = parent::GetStatus();
		try {
			TeamSpeak3::init();
			$tsServer = TeamSpeak3::factory($this->Server->ConnectionString);
			$status['status'] = true;
			$status['count'] = (($tsServer->getProperty('virtualserver_clientsonline') - 1) . '/' . $tsServer->getProperty('virtualserver_maxclients'));
		} catch (Exception $e) {
			var_dump('Teamspeak Error', $e);
		}
		return $status;
	}
	public function GetInfo() {
		$info = parent::GetInfo();
		TeamSpeak3::init();
		try {
			$tsServer = TeamSpeak3::factory($this->Server->ConnectionString);
			$info['html'] = '<ul>'
				. $tsServer->getViewer(new TeamSpeak3_Viewer_Html("/css/img/ts/", "/css/img/ts/", "data:image"))
				. '</ul>';
		} catch (Exception $e) {
			var_dump('Teamspeak Error', $e);
		}
		return $info;
	}
}