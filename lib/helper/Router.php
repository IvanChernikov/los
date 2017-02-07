<?php
class Router {
	public static function Redirect($url) {
		header('location:'. $url);
	}

	public static function Route($url = false) {
		$command = Router::GetCommandArray($url);
		$controller = DEFAULT_CONTROLLER;
		$action = DEFAULT_ACTION;
		$parameters = array();
		if (isset($command[0])) {
			$controller = array_shift($command);
			if (isset($command[0])) {
				$action = array_shift($command);
				$parameters = array_values($command);
			}
		}
		Router::Dispatch($controller, $action, $parameters);
	}
	public static function GetCommandArray($url) {
		$rawUri = ($url ? $url :$_SERVER['REQUEST_URI']);
		// Remove any GET variables (use POST or directly from URI)
		$qPos = strpos($rawUri, '?');
		if (!$qPos === false) {
			$rawUri = substr($rawUri, 0, $qPos);
		}
		$command = explode('/', $rawUri);
		// Remove empty, re-order values and return
		return array_values(array_filter($command, 'Router::EmptyFilter'));
	}

	private static function EmptyFilter($val) {
		return !($val === '');
	}

	private static function Dispatch($controller, $action, $parameters) {
		// Get controller instance
		$instance = Router::GetController($controller);
		if (isset($instance)) {
			$instance->View = new View($controller, $action);
			$instance->Initialize();
			// Execute command
			if (method_exists($instance, $action)) {
				call_user_func_array(array($instance, $action), $parameters);
				// Render View
				$instance->View->Render();
			} else {
				Router::Error(404);
			}
		}
	}

	private static function GetController($name) {
		$controller = ucfirst($name) . 'Controller';
		if(class_exists($controller)) return new $controller;
		Router::Error(404);
	}

	public static function Error($code) {
		$status = Router::GetHttpStatus($code);
		header($_SERVER["SERVER_PROTOCOL"] . " $code $status");
		Router::Route("/home/error/$code");
		die();
	}

	private static function GetHttpStatus($code) {
		$codes = array(
		400 => 'Bad Request',
		401 => 'Unauthorized',
		402 => 'Payment Required',
		403 => 'Forbidden',
		404 => 'Not Found');
		return $codes[$code];
	}

}
