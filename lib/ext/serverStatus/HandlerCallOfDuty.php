<?php
class HandlerCallOfDuty extends HandlerBase {
	private $data = '';
	private $serverData = array();
	private $players = array();
	private $scores = array();
	private $pings = array();
	private $meta = array();
	private $timeout = 1;

	public function GetStatus() {
		$status = parent::GetStatus();
		try {
			if ($this->getServerStatus()) {
				$this->parseServerData();
				$status['count'] = sizeof($this->players) . " / " . $this->serverData['sv_maxclients'];
				$status['status'] = true;
			}
		} catch (Exception $e) {
			var_dump($e);
		}
		return $status;
	}

	public function GetInfo() {
		$info = parent::GetInfo();
		try {

			if ($this->getServerStatus()) {
				$this->parseServerData();
				$info['html'] = $this->getCODHTML();
			}
		} catch (Exception $e) {
			var_dump($e);
		}
		return $info;
	}

	private function getServerStatus(){
		$error = false;

		if (!empty($this->Server->ConnectionString)){
			$handle = @fsockopen($this->Server->ConnectionString);

			if ($handle){
				socket_set_timeout($handle, $this->timeout);
				stream_set_blocking($handle, 1);
				stream_set_timeout($handle, 5);

				fputs($handle, "\xFF\xFF\xFF\xFFgetstatus\x00");
				fwrite($handle, "\xFF\xFF\xFF\xFFgetstatus\x00");

				$this->data = fread($handle, 8192);
				$this->meta = stream_get_meta_data($handle);
				$counter = 8192;

				while (!feof($handle) && !$error && $counter < $this->meta['unread_bytes']){
					$this->data .= fread($handle, 8192);
					$this->meta = stream_get_meta_data($handle);

					if ($this->meta['timed_out']){
						$error = true;
					}

					$counter += 8192;
				}

				if ($error){
					// echo 'Request timed out.';
					return false;
				}else{
					if (strlen(trim($this->data)) == 0){
						// echo 'No data received from server.';
						return false;
					}else{
						return true;
					}
				}
			}else{
				// echo 'Could not connect to server.';
				return false;
			}

			fclose($handle);
		}
	}

	private function parseServerData(){
		$this->serverData = explode("\n", $this->data);
		$tempplayers = array();
		for ($i = 2; $i <= sizeof($this->serverData) - 1; $i++){

			$tempplayers[sizeof($tempplayers)] = trim($this->serverData[$i]);
		}

		$tempdata = array();
		$tempdata = explode('\\', $this->serverData[1]);
		$this->serverData = array();

		foreach($tempdata as $i => $v){
			if (fmod($i, 2) == 1){
				$t = $i + 1;

				$this->serverData[$v] = $tempdata[$t];
			}
		}

		$this->serverData['sv_hostname'] = $this->colorCode($this->serverData['sv_hostname']);
		$this->serverData['_Maps'] = explode('-', $this->serverData['_Maps']);
		foreach($tempplayers as $i => $v){

			if (strlen(trim($v)) > 1){
				$temp = explode(' ', $v);
				$this->scores[sizeof($this->scores)] = $temp[0];
				$this->pings[sizeof($this->pings)] = $temp[1];

				$pos = strpos($v, '"') + 1;
				$endPos = strlen($v) - 1;

				$this->players[sizeof($this->players)] = substr($v, $pos, $endPos - $pos);
			}
		}
	}

	function colorCode($string){
		$string .= "^";

		$find = array(
			'/\^0(.*?)\^/is',
			'/\^1(.*?)\^/is',
			'/\^2(.*?)\^/is',
			'/\^3(.*?)\^/is',
			'/\^4(.*?)\^/is',
			'/\^5(.*?)\^/is',
			'/\^6(.*?)\^/is',
			'/\^7(.*?)\^/is',
			'/\^8(.*?)\^/is',
			'/\^9(.*?)\^/is',
		);

		$replace = array(
			'<span style="color:#000000;">$1</span>^',
			'<span style="color:#F65A5A;">$1</span>^',
			'<span style="color:#00F100;">$1</span>^',
			'<span style="color:#EFEE04;">$1</span>^',
			'<span style="color:#0F04E8;">$1</span>^',
			'<span style="color:#04E8E7;">$1</span>^',
			'<span style="color:#F75AF6;">$1</span>^',
			'<span style="color:#FFFFFF;">$1</span>^',
			'<span style="color:#7E7E7E;">$1</span>^',
			'<span style="color:#6E3C3C;">$1</span>^',
		);


		$string = preg_replace($find, $replace, $string);
		return substr($string, 0, strlen($string) - 1);
	}

	private function getCODHTML() {
		$html = '<div class="sf box light edge round lh32">'
			. '<div class="sf box size-4" style="color: #ffaa00;">Game Mode</div><div class="sf box size-6">' . $this->getCODGameType(). '</div>'
			. '<div class="sf box size-4" style="color: #ffaa00;">Map</div><div class="sf box size-6">' . $this->getCODMapName() . '</div>'
			. '</div>';
		foreach ($this->players as $k => $v) {
			$html .= '<div class="sf box light edge round lh32 mt">'
				. '<span class="sf box size-5" style="color: #ffaa00>' . $this->players[$k] . '</span>'
				. '<span class="sf box size-5">' . $this->scores[$k] . '</span>'
				. '</div>';
		}
		return $html;
	}
	private function getCODMapName() {
		$lookup = array(
			'mp_convoy' => 'Ambush',
			'mp_backlot' => 'Backlot',
			'mp_bloc' => 'Bloc',
			'mp_bog' => 'Bog',
			'mp_broadcast' => 'Broadcast',
			'mp_carentan' => 'Chinatown',
			'mp_countdown' => 'Countdown',
			'mp_crash' => 'Crash',
			'mp_creek' => 'Creek',
			'mp_crossfire' => 'Crossfire',
			'mp_citystreets' => 'District',
			'mp_farm' => 'Downpour',
			'mp_killhouse' => 'Killhouse',
			'mp_overgrown' => 'Overgrown',
			'mp_pipeline' => 'Pipeline',
			'mp_shipment' => 'Shipment',
			'mp_showdown' => 'Showdown',
			'mp_strike' => 'Strike',
			'mp_vacant' => 'Vacant',
			'mp_cargoship' => 'Wet Work',
			'mp_crash_snow' => 'Winter Crash'
			);
		return $lookup[$this->serverData['mapname']];
	}
	private function getCODGameType() {
		$lookup = array(
			'dm' => 'Free-For-All',
			'war' => 'Team Deathmatch',
			'dom' => 'Domination',
			'koth' => 'Headquarters',
			'sab' => 'Sabotage',
			'sd' => 'Search and Destroy'
		);
		return $lookup[$this->serverData['g_gametype']];
	}
}
