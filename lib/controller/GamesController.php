<?php
class GamesController extends ControllerBase {
	public function index() {

	}
	public function snake() {
		$this->loadScript('snake');
	}
	public function jet() {
		$this->loadScript('jet');
	}
	public function star() {
		$this->loadScript('star');
	}
	private function loadScript($name) {
		$this->View->Scripts[] = URL_JS . 'game.' . $name . '.js';
	}
	public function Initialize() {
		parent::Initialize();
		$this->View->Template = 'NoSideBar';
		$this->loadScript('engine');
	}
}
