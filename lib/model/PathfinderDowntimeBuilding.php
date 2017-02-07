<?php
class PathfinderDowntimeBuilding extends ModelBase {

	public $Name;
	public $Notes;
	public $OwnerID;

	public $Rooms;
	public function GetRooms() {
		if (!isset($this->Rooms)) {
			$this->Rooms = $this->LoadCollectionByKey('PathfinderDowntimeBuildingRoom', 'BuildingID');
			usort($this->Rooms, function ($a, $b) { return strcmp($a->Room->Name,$b->Room->Name);});
		}

		return $this->Rooms;
	}
	public $Teams;
	public function GetTeams() {
		if (!isset($this->Teams)) {
			$this->Teams = $this->LoadCollectionByKey('PathfinderDowntimeBuildingTeam', 'BuildingID');
			usort($this->Teams, function ($a, $b) { return strcmp($a->Team->Name,$b->Team->Name);});
		}
	}

	public function __construct() {
		$this->GetRooms();
		if ($this->Rooms === null) {
			$this->Rooms = array();
		}
		$this->GetTeams();
		if ($this->Teams === null) {
			$this->Teams = array();
		}
	}
}
