<?php

/***
 * Name:       TinyMVC
 * About:      An MVC application framework for PHP
 * Copyright:  (C) 2007-2008 Monte Ohrt, All rights reserved.
 * Author:     Monte Ohrt, monte [at] ohrt [dot] com
 * License:    LGPL, see included license file  
 ***/

if(!defined('TMVC_VERSION'))
  define('TMVC_VERSION','1.0.2-dev');

/* directory separator alias */
if(!defined('DS'))
  define('DS',DIRECTORY_SEPARATOR);  
  
/**
 * tmvc
 *
 * main object class
 *
 * @package		TinyMVC
 * @author		Monte Ohrt
 */

class tmvc
{
  /**
   * instance
   *
   * get/set the tmvc object instance
   *
   * @access	public
   * @param   object $new_instance reference to new object instance
   * @return  object $instance reference to object instance
   */    
  public static function &instance($new_instance=null)
  {
    static $instance = null;
    if(isset($new_instance) && is_object($new_instance))
      $instance = $new_instance;
    return $instance;
  }

  /**
   * timer
   *
   * get/set timer values
   *
   * @access  public
p
   * @param   string $id the timer id to set (or compare with $id2)
   * @param   string $id2 the timer id to compare with $id
   * @return  float  difference of two times
   */    
  public static function timer($id=null,$id2=null)
  {
    static $times = array();
    if($id !== null && $id2 !== null)
      return (isset($times[$id]) && isset($times[$id2])) ? ($times[$id2] - $times[$id]) : false;
    elseif($id !== null)
      return $times[$id] = microtime();
    return false;
  }
}

// set initial timer
tmvc::timer('tmvc_app_start');

/**
 * __autoload
 *
 * auto-load internal plugin classes
 *
 * @access	public
 * @param   string $class_name the class name we are trying to load
 * @return  boolean  success or failure
 */    
function __autoload($class_name) {
  $filepath = TMVC_BASEDIR . 'sysfiles' . DS . 'plugins' . DS . "internal.{$class_name}.php";
  if(!file_exists($filepath))
    $filepath = TMVC_BASEDIR . 'myfiles' . DS . 'plugins' . DS . "internal.{$class_name}.php";
  if(!file_exists($filepath))
    $filepath = TMVC_MYAPPDIR . 'plugins' . DS . "internal.{$class_name}.php";
  if(!file_exists($filepath))
    return false;
  return require_once $filepath;
}

/**
 * tmvc_error_handler
 *
 * the internal error handler function
 *
 * @access	public
 */    
function tmvc_error_handler($errno, $errstr, $errfile, $errline)
{

  include(TMVC_MYAPPDIR . 'configs' . DS . 'application.php');

  $errors = new $config['error_handler_class'];
  $errors->trigger_error($errno, $errstr, $errfile, $errline);

  /* don't execute PHP internal error handler */
  return true;
  
}

/* define myapp directory */
if(!defined('TMVC_MYAPPDIR'))
  define('TMVC_MYAPPDIR', TMVC_BASEDIR . 'myapp' . DS);

/* include application config */
include(TMVC_MYAPPDIR . 'configs' . DS . 'application.php');

/* apply routing */
if(!empty($config['routing']['search'])&&!empty($config['routing']['replace']))
  $_SERVER['PATH_INFO'] = preg_replace($config['routing']['search'],$config['routing']['replace'],$_SERVER['PATH_INFO']);
  
/* get controller/method from path_info,
   use defaults if none given */
$path_info = !empty($_SERVER['PATH_INFO']) ? explode('/',$_SERVER['PATH_INFO']) : null;
$controller = !empty($path_info[1]) ? preg_replace('!\W!','',$path_info[1]) : $config['default_controller'];
$controller_file = TMVC_MYAPPDIR . DS . 'controllers' . DS . "{$controller}.php";
$unknown_controller = false;

set_error_handler('tmvc_error_handler');

/* see if controller exists */
if(!file_exists($controller_file))
{
  $unknown_controller = $controller;
  $controller = $config['default_controller'];
  $controller_file = TMVC_MYAPPDIR . DS . 'controllers' . DS . "{$controller}.php";
}

include($controller_file);

/* see if controller class exists */
$controller_class = $controller.'_Controller';
if(!class_exists($controller_class))
  trigger_error("Unknown controller class '{$controller_class}'",E_USER_ERROR);
  
$tmvc = new $controller_class(true);

/* see if controller class method exists */
$controller_method = !empty($path_info[2]) ? $path_info[2] : 'index';

/* cannot call method names starting with underscore */
if(substr($controller_method,0,1)=='_')
  trigger_error("Private method name not allowed '{$controller_method}'",E_USER_ERROR);

include(TMVC_MYAPPDIR . 'configs' . DS . 'autoload.php');

/* auto-load libraries */
if(!empty($config['libraries']))
{
  foreach($config['libraries'] as $library)
    if(is_array($library))
      $tmvc->load->library($library[0],$library[1]);
    else
      $tmvc->load->library($library);
}

/* auto-load scripts */
if(!empty($config['scripts']))
{
  foreach($config['scripts'] as $script)
    $tmvc->load->script($script);
}
  
if($config['timer'])
  ob_start();

try {
  if($unknown_controller === false)
    $tmvc->$controller_method();
  else
    $tmvc->catch_controller($unknown_controller,$controller_method);
} catch (Exception $e) {
  trigger_error("Unknown controller method '{$controller_method}'",E_USER_ERROR);
}

if($config['timer'])
{
  $output = ob_get_contents();
  ob_end_clean();
  tmvc::timer('tmvc_app_end');
  echo str_replace('{TMVC_TIMER}',sprintf('%0.5f',tmvc::timer('tmvc_app_start','tmvc_app_end')),$output);
}
 
?>
