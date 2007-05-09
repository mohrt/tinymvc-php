<?php

/**
 * database.php
 *
 * application database configuration
 *
 * @package		TinyMVC
 * @author		Monte Ohrt
 */


$config['plugin'] = 'TMVC_PDO'; // plugin for db access

$config['type'] = 'mysql';      // connection type
$config['host'] = 'localhost';  // db hostname
$config['name'] = 'dbname';     // db name
$config['user'] = 'dbuser';     // db username
$config['pass'] = 'dbpass';     // db password

$config['persistent'] = false;  // db connection persistence?

?>