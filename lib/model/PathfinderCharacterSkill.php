<?php
class PathfinderCharacterSkill extends ModelBase {
	public $CharacterID;
	public $SkillID;
	public $Ability;
	public $Ranks;
	public $IsClass;
	public $Type;
	
	public $Skill;
	public function GetSkill() {
		if (!isset($this->Skill)) {
			$this->Skill = PathfinderSkill::LoadByID($this->SkillID);
		}
		return $this->Skill;
	}
	
	public function __construct() {
		$this->GetSkill();
	}
}