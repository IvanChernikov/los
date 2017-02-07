<?php
class HomeController extends ControllerBase {
	public function index() {
		$options = array(
			'sort' => array('TimeCreated'),
			'limit' => array(5)
		);
		$this->View->AddData('WallPosts', WallPost::LoadAll($options));
		$options['sort'] = array('`When`');
		$this->View->AddData('Quotes', Quote::LoadAll($options));
		$this->View->AddData('Feeds', RssFeed::LoadAll());
	}

	public function motd() {
		// Rick-Roll
	}

	public function quote() {
		if (Flag::Get('IsPost') && Auth::UserAuthorized(1)) {
			$Quote = new Quote();
			$Quote->What = $_POST['What'];
			$Quote->Who = $_POST['Who'];
			$Quote->When = date('Y-m-d H:i:s', time());
			$Quote->AuthorID = Auth::GetCurrentUserID();
			$Quote->Save();
		}
		$options = array(
			'sort' => array('`When`')
		);
		$Quotes = Quote::LoadAll($options);
		$this->View->AddData('Quotes',$Quotes);
	}

	public function error($code = 400) {
		$this->View->AddData('ErrorCode', $code);
		$message = 'Something went wrong';
		switch ($code) {
			case 401:
				$message = 'You do not have the access level required to view this page/perform this command.';
				break;
			case 403:
				$message = 'Access to this page is forbidden.';
				break;
			case 404:
				$message = 'The page you were looking for was not found. Please make sure that your URL is valid.';
				break;
		}
		$this->View->AddData('ErrorMessage', $message);
	}

	public function rss($id = null) {
		if ($id === null) {
			$this->View->AddData('Feeds',RssFeed::LoadAll());
		} else {
			$this->View->AddData('Feed', RssFeed::LoadById($id));
		}
	}

	public function rssblock($id) {
		if (Flag::Get('IsPost')) {

		}
	}
}
