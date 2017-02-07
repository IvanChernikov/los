<?php
class PathfinderAbilities extends ModelBase {
	public $CharacterID;
	public $STR;
	public $DEX;
	public $CON;
	public $INT;
	public $WIS;
	public $CHA;
	
	/*
	private function GetMod($value) {
		return floor(($value-10)/2);
	}
	*/
	
	public function SetDefault() {
		$this->STR = 10;
		$this->DEX = 10;
		$this->CON = 10;
		$this->INT = 10;
		$this->WIS = 10;
		$this->CHA = 10;
	}
}