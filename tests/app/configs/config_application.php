<?php

$config['routing']['search'] =  array('!^/routed!');
$config['routing']['replace'] = array('/smoke/routed');

$config['root_controller'] = null;
$config['root_action'] = null;

$config['default_controller'] = 'smoke';
$config['default_action'] = 'index';

$config['error_handler_class'] = 'TinyMVC_ErrorHandler';

$config['timer'] = true;

?>
