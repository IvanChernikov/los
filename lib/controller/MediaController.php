<?php
class MediaController extends ControllerBase {
	public function index() {
	}
	public function pictures() {
		$options = array(
			'sort' => array('ID')
		);
		$this->View->AddData('Images', Image::LoadAll($options));
	}
	public function music() {
		$this->View->Scripts[] = URL_JS . 'player.js';
		$this->View->AddData('Tracks',Track::LoadAll());
	}
	public function player($method) {
		if(Flag::Get('IsPost')) {
			switch ($method) {
				case "song":
					$id = $_POST['song_id'];
					echo json_encode(Track::LoadByID($id));
					break;
				default:
					echo "Wrong Method Supplied";
					break;
			}
		} else {
			echo "No Post Data Supplied";
		}
	}

	public function test() {

	}

	public function flash($file = null) {
		if ($file !== null) {
			$this->View->AddData('file', $file);
		}
	}
	public function files() {
		$this->View->AddData('Files',FileObject::LoadAll());
	}
	public function video() {

	}

	public function convert() {
		if (!Auth::UserAuthorized(1)) {
			Router::Error(401);
		}
		if (Flag::Get('IsPost')) {
			$URI = 'DoesNotMatter';
			$Title = $_POST['Title'];
			$Artist = $_POST['Artist'];
			$URL = escapeshellarg($_POST['URL']);
			$StartTime = escapeshellarg($_POST['StartTime']);
			$EndTime = escapeshellarg($_POST['EndTime']);

			$cmd = "cd scripts; sudo node yt-convert.js $URI $URL yt-convert $StartTime $EndTime";

			$ret_arr = array();
			$ret_val;
			exec($cmd , $ret_arr, $ret_val);

			if ($ret_val == 0 && count($ret_arr)) {
				$FileObject = new FileObject();
				$FileObject->File = File::GetUID('mp3');
				$FileObject->Type = 'mp3';
				$FileObject->UploaderID = Auth::GetCurrentUserID();
				$FileObject->Name = $Title;

				$JSON = json_decode($ret_arr[1]);

				if (rename($JSON->filePath, $FileObject->GetPath())) {
					$FileObject->Save();

					$Track = new Track();
					$Track->FileID = $FileObject->ID;
					$Track->Title = $Title;
					$Track->Artist = $Artist;
					$Track->Save();
					Router::Route("/media/files/");
					die();
				}
			}
		}
		$this->View->Scripts[] = URL_JS . 'youtube.js';
	}

	public function upload() {
		if (!Auth::UserAuthorized(1)) {
			Router::Error(401);
		}
		if (Flag::Get('IsPost')) {
			$files = File::UploadFileList();
			echo '<pre style="color:#20f020;">';
			foreach ($files as $file) {
				if (File::Validate($file)) {
					File::Save($file);
					echo sprintf('<p>File `%s` uploaded.</p>', $file['name']);
				} else {
					echo sprintf('<p>File `%s` failed. (%s)</p>', Flag::Get('UploadError'));
					Flag::Remove('UploadError');
				}
			}
			echo '</pre>';
			die();
		}
	}

	public function progress() {
	// TODO
	}
}
