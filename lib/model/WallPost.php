<?php
class WallPost extends ModelBase {
	public $AuthorID;
	public $Title;
	public $Content;
	public $TimeCreated;
	
	public $Author;
	
	public function GetReplies() {
		return $this->LoadCollectionByKey('WallReply','WallPostID');
	}
	public function GetAuthor() {
		if (!isset($this->Author)) {
			$this->Author = User::LoadByID($this->AuthorID);
		}
		return $this->Author;
	}
}