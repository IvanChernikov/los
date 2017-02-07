<?php
class File {
	public static function UploadFileList() {
		$files = $_FILES['files'];
		$file_ary = array();
		$file_count = count($files['name']);
		$file_keys = array_keys($files);

		for ($i=0; $i<$file_count; $i++) {
			foreach ($file_keys as $key) {
				$file_ary[$i][$key] = $files[$key][$i];
			}
		}
		return $file_ary;
	}

	// File Array { ['name'], ['type'], ['tmp_name'], ['error'], ['size'] }
	public static function Validate(&$file) {
		try {
			// Check for errors
			switch($file['error']) {
				case UPLOAD_ERR_OK:
					break;
				case UPLOAD_ERR_NO_FILE:
					Flag::Error('UploadError','No files uploaded');
				case UPLOAD_ERR_INI_SIZE:
				case UPLOAD_ERR_FORM_SIZE:
					Flag::Error('UploadError','Total upload size cannot exceed 100MB');
				default:
					Flag::Error('UploadError','Unknown upload error occurred');
			}
			// Check size
			if ($file['size'] > 104857600) {
				Flag::Error('UploadError','Total upload size cannot exceed 100MB');
			}
			// Check type
			$fileInfo = new finfo(FILEINFO_MIME_TYPE);
			if (false === $file['extension'] = array_search(
				$fileInfo->file($file['tmp_name']),
				File::AllowedTypes(),
				true)) {
				var_dump($fileInfo->file($file['tmp_name']));
				Flag::Error('UploadError','Extension "'. $ext .'" is not allowed for upload');
			}
		} catch (Exception $e) {
			return false;
		}
		return !Flag::Exists('UploadError');
	}

	public static function Save($file) {
		$FileObject = new FileObject();
		$FileObject->File = File::GetUID($file['extension']);
		$FileObject->Type = ($file['extension'] === 'jpeg' ? 'jpg' : $file['extension']);
		$FileObject->UploaderID = Auth::GetCurrentUserID();
		$FileObject->Name = $file['name'];
		if (move_uploaded_file($file['tmp_name'], $FileObject->GetPath())) {
			$FileObject->Save();
			File::Categorize($FileObject, $file);
		} else {

		}
	}

	public static function Categorize($object, $file) {
		switch ($file['extension']) {
			case 'jpg':
			case 'jpeg':
			case 'gif':
			case 'png':
				$Image = new Image();
				$Image->FileID = $object->ID;
				$Image->Title = $file['name'];
				$Image->Save();
				break;
			case 'mp3':
				$Track = new Track();
				$Track->FileID = $object->ID;
				$Track->Title = $file['name'];
				$Track->Artist = 'Unknown';
				$Track->Save();
				break;
		}
	}

	public static function SaveConvertedTrack($json) {
		$data = json_decode($json);

		$FileObject = new FileObject();
		$FileObject->File = File::GetUID('mp3');
		$FileObject->Type = 'mp3';
		$FileObject->UploaderID = Auth::GetCurrentUserID();
		$FileObject->Name = $data->Title;
		if (move_uploaded_file($data->Path, $FileObject->GetPath())) {
			$FileObject->Save();
			$Track = new Track();
			$Track->FileID = $object->ID;
			$Track->Title = $data->Title;
			$Track->Artist = $data->Artist;
			$Track->Save();
		}
	}

	public static function GetUID($extension) {
		$db = Database::getInstance()->con;
		$stm = $db->prepare('SELECT UUID();');
		if ($stm->execute()) {
			return $stm->fetchColumn() . '.' . $extension;
		} else {
			return null;
		}
	}

	private static function AllowedTypes() {
		return array(
			'png' => 'image/png',
			'gif' => 'image/gif',
			'jpg' => 'image/jpeg',
			'jpeg' => 'image/jpeg',
			'pdf' => 'application/pdf',
			'mp3' => 'audio/mp3',
			'mp3' => 'audio/mpeg',
			'mp4' => 'video/mpeg',
			'mp4' => 'video/mp4',
			'zip' => 'application/zip'
			);
	}
}
