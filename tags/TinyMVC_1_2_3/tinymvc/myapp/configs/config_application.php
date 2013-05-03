<?php

/**
 * application.php
 *
 * application configuration
 *
 * @package		TinyMVC
 * @author		Monte Ohrt
 */

 
/* URL routing, use preg_replace() compatible syntax */
$config['routing']['search'] =  array();
$config['routing']['replace'] = array();
 
/* set this to force controller and method instead of using URL params */
$config['root_controller'] = null;
$config['root_action'] = null;

/* name of default controller/method when none is given in the URL */
$config['default_controller'] = 'default';
$config['default_action'] = 'index';

/* name of PHP function that handles system errors */
$config['error_handler_class'] = 'TinyMVC_ErrorHandler';

/* enable timer. use {TMVC_TIMER} in your view to see it */
$config['timer'] = true;

?>
