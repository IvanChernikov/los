<?php
class PathfinderCharacter extends ModelBase {
	public $OwnerID;
	public $Name;
	public $Campaign;
	public $Race;
	public $Alignment;
	public $LandSpeed;
	public $ClimbSpeed;
	public $SwimSpeed;
	public $BurrowSpeed;
	public $FlySpeed;
	public $FlyAbility;
	public $Size;
	public $HasBackgroundSkills;
	
	public $Owner;
	public function GetOwner() {
		if (!isset($this->Owner)) {
			$this->Owner = User::LoadByID($this->OwnerID);
		}
		return $this->Owner;
	}
	
	public $Abilities;
	public function GetAbilities() {
		if (!isset($this->Abilties)) {
			$this->Abilities = $this->LoadChild('PathfinderAbilities', 'CharacterID', $this->ID);
			if ($this->Abilities === null) {
				$this->Abilities = new PathfinderAbilities();
				$this->Abilities->CharacterID = $this->ID;
				$this->Abilities->SetDefault();
				$this->Abilities->Save();
			}
		}
		return $this->Abilities;
	}
	
	public $Levels;
	public function GetLevels() {
		if (!isset($this->Levels)) {
			$this->Levels = $this->LoadCollectionByKey('PathfinderLevel','CharacterID');
		}
		return $this->Levels;
	}
	
	public $Skills;
	public function GetSkills() {
		if (!isset($this->Skills)) {
			$this->Skills = $this->LoadCollectionByKey('PathfinderCharacterSkill','CharacterID');
			if ($this->Skills === null || count($this->Skills) === 0) {
				$this->Skills = array();
				$skills = PathfinderSkill::LoadAll(array('match' => array('HasType' => 0, 'UseUntrained' => 1)));
				foreach ($skills as $skill) {
					$CharacterSkill = new PathfinderCharacterSkill();
					$CharacterSkill->CharacterID = $this->ID;
					$CharacterSkill->SkillID = $skill->ID;
					$CharacterSkill->Ability = $skill->DefaultAbility;
					$CharacterSkill->Ranks = 0;
					$CharacterSkill->IsClass = 0;
					$CharacterSkill->Type = null;
					$CharacterSkill->Save();
					$this->Skills[] = $CharacterSkill;
				}
			}
		}
		usort($this->Skills, function ($a, $b) { return strcmp($a->Skill->Name,$b->Skill->Name);}); 
		return $this->Skills;
	}
	
	public $Bonuses;
	public function GetBonuses() {
		if (!isset($this->Bonuses)) {
			$this->Bonuses = $this->LoadCollectionByKey('PathfinderBonus','CharacterID');
		}
		return $this->Bonuses;
	}
	
	public function __construct() {
		$this->GetOwner();
		// Remove password hash
		unset($this->Owner->Password);
		$this->GetAbilities();
		$this->GetLevels();
		$this->GetSkills();
		$this->GetBonuses();
	}
}