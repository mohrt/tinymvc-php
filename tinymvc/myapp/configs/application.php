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
 
/* name of default controller when none is given in the URL */
$config['default_controller'] = 'default';

/* name of PHP function that handles system errors */
$config['error_handler_class'] = 'TinyMVC_ErrorHandler';


?>
