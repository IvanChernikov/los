<?php
// DEBUG
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Application META
define ('APP_TITLE', 'LoS');
define ('APP_AUTHOR', 'Ivan Chernikov');
define ('APP_ADMIN_EMAIL', 'ivan.chernikov25@gmail.com');

// Path Constants
define ('DS', DIRECTORY_SEPARATOR );
define ('PATH_ROOT', dirname ( __FILE__ ));
define ('PATH_LIB', PATH_ROOT . DS . 'lib' . DS);
define ('PATH_CSS', PATH_ROOT . DS . 'css' . DS);
define ('PATH_MEDIA', PATH_ROOT . DS . 'media' . DS);
define ('PATH_TEMP', PATH_ROOT . DS . 'tmp' . DS);
define ('PATH_SCRIPT', PATH_ROOT .DS . 'scripts' . DS);

// Application Paths
define ('PATH_BLOCK', PATH_LIB . 'block' . DS);
define ('PATH_CORE', PATH_LIB . 'core' . DS);
define ('PATH_CONTROLLER', PATH_LIB . 'controller' . DS);
define ('PATH_EXT', PATH_LIB . 'ext' . DS);
define ('PATH_HELPER', PATH_LIB . 'helper' . DS);
define ('PATH_MODEL', PATH_LIB . 'model'. DS);
define ('PATH_VIEW', PATH_LIB . 'view' . DS);

// URL Constants
define ('URL_CSS', '/css/');
define ('URL_JS', '/js/');
define ('URL_MEDIA', '/media/');

// MySQL DB
define ('DB_HOSTNAME', 'localhost');
define ('DB_USERNAME', 'admin');
define ('DB_PASSWORD', 'werewolf');
define ('DB_PORT', 3306);
define ('DB_DATABASE', 'los');

// Default Application Configuration
define ('DEFAULT_CONTROLLER', 'home');
define ('DEFAULT_ACTION', 'index');
define ('DEFAULT_TEMPLATE', 'Template');

// Auto-Loader
spl_autoload_register (null, false);
spl_autoload_extensions ('.php, .class.php, .lib.php');
/* CORE */
function coreLoader($class) {
	$filename = $class . '.php';
	$filepath = PATH_CORE . $filename;
	if (!file_exists($filepath)) {
		return false;
	}
	include_once $filepath;
}
spl_autoload_register('coreLoader');

/* CONTROLLER */
function controllerLoader($class) {
	$filename = $class . '.php';
	$filepath = PATH_CONTROLLER . $filename;
	if (!file_exists($filepath)) {
		return false;
	}
	include_once $filepath;
}
spl_autoload_register('controllerLoader');

/* MODEL */
function modelLoader($class) {
	$filename = $class . '.php';
	$filepath = PATH_MODEL . $filename;
	if (!file_exists($filepath)) {
		return false;
	}
	include_once $filepath;
}
spl_autoload_register('modelLoader');

/* HELPER */
function helperLoader($class) {
	$filename = $class . '.php';
	$filepath = PATH_HELPER . $filename;
	if (!file_exists($filepath)) {
		return false;
	}
	include_once $filepath;
}
spl_autoload_register('helperLoader');

/* EXTENSIONS AND EXTERNAL LIBRARIES */
function extLoader($class) {
    $libraries = array(
		'ts3Api',
		'serverStatus'
		);
	$found = false;
    foreach ($libraries as $lib) {
        $filename =  $class . '.php';
        $filepath = PATH_EXT . $lib . DS . $filename;
        if (file_exists($filepath)) {
            $found = true;
            include $filepath;
            break;
        }
    }
    return $found;
}
spl_autoload_register('extLoader');
