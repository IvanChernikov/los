<?php
class WallReply extends ModelBase {
	public $AuthorID;
	public $WallPostID;
	public $TimeCreated;
	public $Content;

	public $Author;
	
	public function GetAuthor() {
		if (!isset($this->Author)) {
			$this->Author = User::LoadByID($this->AuthorID);
		}
		return $this->Author;
	}
}