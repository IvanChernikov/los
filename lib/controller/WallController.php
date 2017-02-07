<?php
class WallController extends ControllerBase {
	public function index($page = 1) {
		$options = array(
			'sort' => array('TimeCreated'),
			'limit' => array(
				($page-1)*10, // From 10 * (page number - 1)
				$page*10 // To 10 * page number
			)
		);
		$this->View->AddData('WallPosts', WallPost::LoadAll($options));
	}
	public function search($page = 1) {
		if (Flag::Get('IsPost')) {
			$options = array(
				'sort' => array('TimeCreated'),
				'limit' => array(
					($page-1)*10, // From 10 * (page number - 1)
					$page*10 // To 10 * page number
				)
			);

			$this->View->AddData('SearchString',$_POST['SearchString']);

		} else {
			Router::Redirect('/wall/index/');
		}
	}

	public function post() {
		if (!Auth::UserAuthorized(2)) {
			Router::Error(401);
		}
		if (Flag::Get('IsPost')) {
			$WallPost = new WallPost();
			$WallPost->AuthorID = Auth::GetCurrentUserID();
			$WallPost->Title = $_POST['Title'];
			$WallPost->Content = $_POST['Content'];
			$WallPost->Save();
			Router::Redirect('/home/index/');
		}
	}
	public function attach($fileID) {

	}

	public function update($id) {
		if (Flag::Get('IsPost')) {
			$WallPost = WallPost::LoadByID($id);
			if ($WallPost->AuthorID == Auth::GetCurrentUserID()) {
				$WallPost->Content = $_POST['content'];
				echo json_encode($WallPost->Save());
			}
		}
	}
	public function reply($postID) {
		if (!Auth::UserAuthorized(2)) {
			Router::Error(401);
		}
		if (Flag::Get('IsPost')) {
			$WallReply = new WallReply();
			$WallReply->AuthorID = Auth::GetCurrentUserID();
			$WallReply->WallPostID = $postID;
			$WallReply->Content = $_POST['Content'];
			$WallReply->Save();
			Router::Redirect('/home/index/');
		}
	}
}
