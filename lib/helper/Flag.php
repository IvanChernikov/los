<?php
class Flag {
	public static function Set($name, $value = true) {
		$_SESSION['Flag'][$name] = $value;
	}
	public static function Exists($name) {
		return isset($_SESSION['Flag'][$name]);
	}
	public static function Get($name) {
		return Flag::Exists($name) ? $_SESSION['Flag'][$name] : null;
	}
	public static function Init() {
		$_SESSION['Flag'] = array();
		$_SESSION['Flag']['IsPost'] = (strtoupper($_SERVER['REQUEST_METHOD']) == 'POST');
	}
	public static function Remove($name) {
 		if (Flag::Exists($name)) {
			unset($_SESSION['Flag'][$name]);
		}
	}
	public static function Purge() {
		unset($_SESSION['Flag']);
		Flag::Init();
	}
	public static function Error($name, $message = 'An error has occurred') {
		Flag::Set($name, $message);
		throw new Exception($message);
	}
}