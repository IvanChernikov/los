<?php
class PathfinderDowntimeBuildingRoom extends ModelBase {
	public $BuildingID;
	public $RoomID;
	
	public $Room;
	public function GetRoom() {
		if (!isset($this->Room)) {
			$this->Room = PathfinderDowntimeRoom::LoadByID($this->RoomID);
		}
		return $this->Room;
	}
	
	public function __construct() {
		$this->GetRoom();
	}

}