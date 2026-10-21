<?php

/***
 * Smoke test front controller: runs the framework against tests/app
 * instead of tinymvc/myapp. Set PATH_INFO to choose the controller/action.
 ***/

error_reporting(E_ALL);

define('TMVC_ERROR_HANDLING',1);

if(!defined('DS'))
  define('DS',DIRECTORY_SEPARATOR);

define('TMVC_BASEDIR',dirname(__FILE__) . DS . '..' . DS . 'tinymvc' . DS);
define('TMVC_MYAPPDIR',dirname(__FILE__) . DS . 'app' . DS);

require(TMVC_BASEDIR . 'sysfiles' . DS . 'TinyMVC.php');

$tmvc = new tmvc();
$tmvc->main();

?>
