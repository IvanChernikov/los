<?php
class AjaxController extends ControllerBase {
	public function index(){
		Router::Error(403);
	}
	public function block($name) {
		header('Content-Type: text/html');
		$params = (Flag::Get('IsPost') ? $_POST : array());
		echo Html::GetBlock($name,$params);
	}
}