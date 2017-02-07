<?php
class Track extends ModelBase {
	public $FileID;
	public $Title;
	public $Artist;
	public $Comment;
	
	public $File;
	
	public function GetFile() {
		if (!isset($this->File)) {
			$this->File = FileObject::LoadByID($this->FileID);
		}
		return $this->File;
	}
	public function __construct() {
		$this->GetFile();
	}
}