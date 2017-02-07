<?php
class AdminController extends ControllerBase {
	public function __construct() {
		if (!Auth::UserAuthorized(4)) {
			Router::Error(401);
		}
	}
	public function index() {

	}
	public function userList() {
		$this->View->AddData('Users', User::LoadAll());
	}

	public function detailView($model = false,$id = false) {
		if ($model !== false && $id !== false) {
			$meta = new MetaInfo($model);
			$instance = $meta->GetInstance($id);
			$this->View->AddData('Model', $model);
			$this->View->AddData('Meta', $meta);
			$this->View->AddData('Instance', $instance);
		} else {
			if ($model === false) {
				$model = '';
			}
			Router::Route("/admin/listView/$model");
		}

	}

	public function listView($model = false) {
		if ($model) {
			$meta = new MetaInfo($model);
			$this->View->AddData('Model',$model);
			$this->View->AddData('Meta', $meta);
			$this->View->AddData('List', $meta->GetList());
		}

	}
	public function updateProperty() {
		if (Flag::Get('IsPost')) {
			$map = Editor::GetPropertyMap($_POST['name']);
			$property = $map[2];
			$value = $_POST['value'];
			$meta = new MetaInfo($map[0]);
			if (strlen($meta->key) === 0) {
				$instance = $meta->GetInstance($map[1]);
				$instance->$property = $value;

				echo json_encode($instance->Save());
			} else {
				echo json_encode('error, tried to update Key property');
			}
		}
	}
}
