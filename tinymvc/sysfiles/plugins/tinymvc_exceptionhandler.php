<?php

/***
 * Name:       TinyMVC
 * About:      An MVC application framework for PHP
 * Copyright:  (C) 2007-2008 Monte Ohrt, All rights reserved.
 * Author:     Monte Ohrt, monte [at] ohrt [dot] com
 * License:    LGPL, see included license file  
 ***/

// ------------------------------------------------------------------------

/**
 * TinyMVC_ExceptionHandler
 * 
 * A simple exception handler to display exceptions in a formatted box.
 *
 * @package		TinyMVC
 * @author		Monte Ohrt
 */

class TinyMVC_ExceptionHandler extends ErrorException {
  
	/**
	 * printException
	 *
	 * @access	public
	 * @param   Exception|Throwable $e
	 */    
  public static function printException($e)
  {
    switch ($e->getCode()) {
        case E_ERROR:
				  $code_name = 'E_ERROR';
        break;
        case E_WARNING:
				  $code_name = 'E_WARNING';
        break;
        case E_PARSE:
				  $code_name = 'E_PARSE';
        break;
        case E_NOTICE:
				  $code_name = 'E_NOTICE';
        break;
        case E_CORE_ERROR:
				  $code_name = 'E_CORE_ERROR';
        break;
        case E_CORE_WARNING:
				  $code_name = 'E_CORE_WARNING';
        break;
        case E_COMPILE_ERROR:
				  $code_name = 'E_COMPILE_ERROR';
        break;
        case E_COMPILE_WARNING:
				  $code_name = 'E_COMPILE_WARNING';
        break;
        case E_USER_ERROR:
				  $code_name = 'E_USER_ERROR';
        break;
        case E_USER_WARNING:
				  $code_name = 'E_USER_WARNING';
        break;
        case E_USER_NOTICE:
				  $code_name = 'E_USER_NOTICE';
        break;
        case 2048: // E_STRICT; the constant is deprecated as of PHP 8.4
				  $code_name = 'E_STRICT';
        break;
        case E_RECOVERABLE_ERROR:
				  $code_name = 'E_RECOVERABLE_ERROR';
        break;
        case E_DEPRECATED:
				  $code_name = 'E_DEPRECATED';
        break;
        case E_USER_DEPRECATED:
				  $code_name = 'E_USER_DEPRECATED';
        break;
				default:
				  $code_name = $e->getCode();
			  break;
	  }
	  ?>
    <span style="text-align: left; background-color: #fcc; border: 1px solid #600; color: #600; display: block; margin: 1em 0; padding: .33em 6px">
      <b>Error:</b> <?=self::escape($code_name)?><br />
      <b>Message:</b> <?=self::escape($e->getMessage())?><br />
      <b>File:</b> <?=self::escape($e->getFile())?><br />
      <b>Line:</b> <?=(int)$e->getLine()?>
    </span>
	  <?php
  }
  
	/**
	 * handleException
	 *
	 * @access	public
	 * @param   Exception|Throwable $e
	 */    
  public static function handleException($e)
  {
       return self::printException($e);
  }

	/**
	 * escape
	 *
	 * html-escape a value for display in the error box
	 *
	 * @access	private
	 * @param   mixed $value
	 * @return  string
	 */    
  private static function escape($value)
  {
    return htmlspecialchars((string)$value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
  }
}

?>