<?php
class PathfinderController extends ControllerBase {
	public function index() {}
	public function character($id) {
		$this->UpdateView();
		$this->View->Scripts[] = URL_JS . 'pf.character.lib.js';
		$this->View->Scripts[] = URL_JS . 'pf.character.js';
		$this->View->AddData('Character',PathfinderCharacter::LoadByID($id));
	}
	public function calculator() {
		$this->UpdateView();
	}
	private function UpdateView() {
		$this->View->Styles[] = URL_CSS . 'pathfinder.css';
	}
	public function updateCharacter() {
		// id, type, property, value
		$Object = null;
		switch ($_POST['type']) {
			case 'character':
				$Object = PathfinderCharacter::LoadByID($_POST['id']);
				break;
			case 'abilities':
				$Object = PathfinderAbilities::LoadByID($_POST['id']);
				break;
			case 'level':
				$Object = PathfinderLevel::LoadByID($_POST['id']);
				break;
			case 'skill':
				$Object = PathfinderCharacterSkill::LoadByID($_POST['id']);
				break;
			default:
				die("Error: Wrong data type sent to server");
		}
		// var_dump($_POST);
		$Object->$_POST['property'] = $_POST['value'];
		$Object->Save();
		echo "Changes Saved";
	}

	public function updateCharacterLevel() {
		switch ($_POST['type']) {
			case 'up':
				$Level = new PathfinderLevel();
				$Level->CharacterID = $_POST['id'];
				$Level->Save();
				echo json_encode($Level);
				break;
			case 'down':
				$Level = PathfinderLevel::LoadByID($_POST['id']);
				var_dump($Level, $_POST['id']);
				$Level->Delete();
				break;
		}
	}

	public function addSkill() {
		$CharID = $_POST['CharID'];
		$SkillID = $_POST['SkillID'];
		$SkillType = $_POST['SkillType'];
		$Skill = PathfinderSkill::LoadByID($SkillID);
		if ($Skill != null) {
			$CharSkill = new PathfinderCharacterSkill();
			$CharSkill->SkillID = $SkillID;
			$CharSkill->CharacterID = $CharID;
			$CharSkill->Ability = $Skill->DefaultAbility;
			$CharSkill->IsClass = false;
			$CharSkill->Ranks = 0;
			$CharSkill->Type = (($Skill->HasType == 1) ? $SkillType : null);
			$CharSkill->Save();
			$CharSkill->Skill = $Skill;
			echo json_encode($CharSkill);
		} else {
			echo json_encode(false);
		}
	}

	public function getSkillList() {
		$Skills = PathfinderSkill::LoadAll(array('sort' => array('Name','ASC')));
		echo json_encode($Skills);
	}

	public function test($id) {
	//	$Abilities = PathfinderAbilities::LoadByID($id);
	//	$Abilities->Delete();
	}

	public function verbalduel() {
		$this->UpdateView();
	}

	public function building($id = null) {
		$this->UpdateView();
		if (is_numeric($id)) {
			$Building = PathfinderDowntimeBuilding::LoadByID($id);
			if (isset($Building) && $Building !== null && $Building !== false) {
				$this->View->AddData('Building',$Building);
				if ($Building->OwnerID === Auth::GetCurrentUserID()) {
					$Rooms = PathfinderDowntimeRoom::LoadAll(array('sort' => array('Name','ASC')));
					$Teams = PathfinderDowntimeTeam::LoadAll(array('sort' => array('Name','ASC')));
					$this->View->AddData('Rooms',$Rooms);
					$this->View->AddData('Teams',$Teams);
				}
			}
		} elseif ($id === 'addRoom') {
			$this->AddRoom();
		} elseif ($id === 'addBuilding') {
			$this->AddBuilding();
		} elseif ($id === 'addTeam') {
			$this->addTeam();
		} else {
			$Buildings = PathfinderDowntimeBuilding::LoadAll(array('sort' => array('Name','ASC')));
			$this->View->AddData('Buildings',$Buildings);
		}
	}
	private function AddRoom() {
		if(Flag::Get('IsPost')) {
			if (isset($_POST['BuildingID']) && isset($_POST['RoomID'])) {
				$RoomID = $_POST['RoomID']; $BuildingID = $_POST['BuildingID'];
				$Building = PathfinderDowntimeBuilding::LoadByID($BuildingID);
				$Room = PathfinderDowntimeRoom::LoadByID($RoomID);
				if (isset($Building) && isset($Room) && $Building->OwnerID === Auth::GetCurrentUserID()) {
					$ExistingRooms = PathfinderDowntimeBuildingRoom::LoadAll(array('match' => array('RoomID' => $RoomID, 'BuildingID' => $BuildingID)));
					//var_dump($ExistingRooms, $Building, $Room);
					if (isset($ExistingRooms) && count($ExistingRooms) === 1) {
						$ExistingRooms[0]->Count += 1;
						$ExistingRooms[0]->Save();
					} elseif (count($ExistingRooms) === 0){
						$BuildingRoom = new PathfinderDowntimeBuildingRoom();
						$BuildingRoom->BuildingID = $Building->ID;
						$BuildingRoom->RoomID = $Room->ID;
						$BuildingRoom->Count = 1;
						$BuildingRoom->Save();
					}
					Router::Redirect('/pathfinder/building/'.$Building->ID);
				} else {
					Router::Redirect('/pathfinder/building');
				}
			}

		}
	}
	private function AddTeam() {
		if(Flag::Get('IsPost')) {
			if (isset($_POST['BuildingID']) && isset($_POST['TeamID'])) {
				$TeamID = $_POST['TeamID']; $BuildingID = $_POST['BuildingID'];
				$Building = PathfinderDowntimeBuilding::LoadByID($BuildingID);
				$Team = PathfinderDowntimeTeam::LoadByID($TeamID);
				if (isset($Building) && isset($Team) && $Building->OwnerID === Auth::GetCurrentUserID()) {
					$ExistingTeams = PathfinderDowntimeBuildingTeam::LoadAll(array('match' => array('TeamID' => $TeamID, 'BuildingID' => $BuildingID)));
					//var_dump($ExistingTeams, $Building, $Team);
					if (isset($ExistingTeams) && count($ExistingTeams) === 1) {
						$ExistingTeams[0]->Count += 1;
						$ExistingTeams[0]->Save();
					} elseif (count($ExistingTeams) === 0){
						$BuildingTeam = new PathfinderDowntimeBuildingTeam();
						$BuildingTeam->BuildingID = $Building->ID;
						$BuildingTeam->TeamID = $Team->ID;
						$BuildingTeam->Count = 1;
						$BuildingTeam->Save();
					}
					Router::Redirect('/pathfinder/building/'.$Building->ID);
				} else {
					Router::Redirect('/pathfinder/building');
				}
			}

		}
	}
	private function AddBuilding() {
		if(Flag::Get('IsPost')) {
			if (!Auth::UserAuthorized(2)) {
				Router::Error(401);
			}
			$Building = new PathfinderDowntimeBuilding();
			$Building->Name = $_POST['Name'];
			$Building->OwnerID = Auth::GetCurrentUserID();
			$Building->Notes = $_POST['Notes'];
			$Building->Save();
			Router::Redirect('/pathfinder/building/'.$Building->ID);
		}
	}
}
