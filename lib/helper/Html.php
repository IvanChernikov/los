<?php
class Html {
	public static function GetBlock($name, $data = array()) {
		foreach ($data as $key => $value) {
			global $$key;
			$$key = $value;
		}
		include PATH_BLOCK . $name . '.phtml';
	}
	public static function GetView($view) {
		foreach ($view->Data as $key => $value) {
			global $$key;
			$$key = $value;
		}
		include $view->Path;
	}
	/**
     * Generates a style link
     * @param string $url
     */
    public static function Style($url,$embed = false) {
		if ($embed) {
			return '<style>' . file_get_contents(PATH_CSS . pathinfo($url, PATHINFO_BASENAME)) . '</style>';
		} else {
			return "<link rel='stylesheet' href='$url'>";
		}
	}
	/**
	 * Generates a script tag
	 * @param $url = url of javascript
	 */
	public static function Script($url) {
		return "<script src='$url'></script>";
	}
	public static function Element($tag, $inner = null, $attributes = array()) {
		$attrs = (count($attributes) === 0 ? '' : Html::GetAttributeString($attributes));
		$elem = "<$tag$attrs>";
		if ($inner !== null) {
			$elem .= "$inner</$tag>";
		}
		return $elem;
	}

	private static function GetAttributeString($attributes) {
		$kvp = array();
		foreach ($attributes as $key => $value) {
			array_push($kvp, "$key='$value'");
		}
		return ' ' . implode(' ', $kvp);
	}

	public static function dump_debug() {
		$args = func_get_args();
		echo '<code><pre>';
		foreach ($args as $arg) {
			var_dump($arg);
		}
		echo '</pre></code>';
	}
}
