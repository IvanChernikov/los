<?php
class FileObject extends ModelBase {
	public $UploaderID;
	public $File;
	public $Type;
	public $UploadTime;
	public $Name;
	
	public $Uploader;
	
	public function GetUploader() {
		if (!isset($this->Uploader)) {
			$this->Uploader = User::LoadByID($this->UploaderID);
		}
		return $this->Uploader;
	}
	
	public function GetPath() {
		return PATH_MEDIA . $this->Type . DS . $this->File;
	}
	public function GetUrl() {
		return URL_MEDIA . $this->Type . '/' . $this->File;
	}
}