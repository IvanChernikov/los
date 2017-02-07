<?php
class HandlerMinecraft extends HandlerBase {
	public function GetStatus() {
		$status = parent::GetStatus();
		try {
			$mcServer = new MinecraftServer($this->Server->ConnectionString);
			$stats = MinecraftStats::retrieve($mcServer);
			$status['status'] = true;
			$status['count'] =  ($stats->online_players . '/' . $stats->max_players);
		} catch (MinecraftException $e) {
			var_dump($e);
		}
		return $status;
	}
	public function GetInfo() {
		$info = parent::GetInfo();
		try {
			$mcServer = new MinecraftServer($this->Server->ConnectionString);
			$stats = MinecraftStats::retrieve($mcServer);
			$info['html'] = '<div class="sf box light round edge lh32">'
				. '<span class="sf box size-4" style="color: #ffaa00;">Description</span>'
				. '<span class="sf box size-6">' . $stats->motd . '</span>'
				. '<span class="sf box size-4" style="color: #ffaa00;">Server Version</span>'
				. '<span class="sf box size-6">'. $stats->game_version . '</span></div>';
		} catch (MinecraftException $e) {
			var_dump($e);
		}
		return $info;
	}
}
