<?php
class View {
	public $Name;
	public $Path;
	public $Data;
	public $Template;
	
	public $Styles;
	public $Scripts;
	
	public function __construct($dir, $name) {
		$this->Name = $name;
		$this->Path = PATH_VIEW . $dir . DS . $name . '.phtml';
		$this->Data = array();
		$this->Template = DEFAULT_TEMPLATE;
		
		$this->Styles = array(
			// URL_CSS . 'reset.css',
			URL_CSS . 'style.css',
			URL_CSS . 'teamspeak.css',
			URL_CSS . 'mobile.css'
			// URL_CSS . 'base.css'
			// URL_CSS . 'dialogs.css',
			// URL_CSS . 'forms.css',
		);
		$this->Scripts = array(
			'http://code.jquery.com/jquery-2.2.2.min.js',
			'http://code.jquery.com/ui/1.11.4/jquery-ui.min.js',
			URL_JS . 'spin.min.js',
			URL_JS . 'lib.js'
		);
	}
	
	public function AddData($key,$value) {
		$this->Data[$key] = $value;
	}
	
	public function Render() {
		if (file_exists($this->Path)) {
			Html::GetBlock($this->Template, array('View' => $this));
		}
	}
}