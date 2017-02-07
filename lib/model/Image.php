<?php
class Image extends ModelBase {
	public $FileID;
	public $Title;
	public $Comment;
	
	public $File;
	
	public function GetFile() {
		if (!isset($this->File)) {
			$this->File = FileObject::LoadByID($this->FileID);
		}
		return $this->File;
	}
}