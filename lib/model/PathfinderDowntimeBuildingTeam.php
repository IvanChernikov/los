<?php
class PathfinderDowntimeBuildingTeam extends ModelBase {
	public $BuildingID;
	public $TeamID;

	public $Team;
	public function GetTeam() {
		if (!isset($this->Team)) {
			$this->Team = PathfinderDowntimeTeam::LoadByID($this->TeamID);
		}
		return $this->Team;
	}

	public function __construct() {
		$this->GetTeam();
	}

}
