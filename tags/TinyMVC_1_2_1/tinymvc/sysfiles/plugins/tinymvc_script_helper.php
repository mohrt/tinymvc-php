<?php

/***
 * Name:       TinyMVC
 * About:      An MVC application framework for PHP
 * Copyright:  (C) 2007-2008 Monte Ohrt, All rights reserved.
 * Author:     Monte Ohrt, monte [at] ohrt [dot] com
 * License:    LGPL, see included license file  
 ***/

// ------------------------------------------------------------------------

Class TinyMVC_Script_Helper
{

  /**
   * debug
   *
   * show PHP variable in debug window
   *
   * @access  public
   * @param   mixed    $var     variable to display     
   * @param   string   $name    optional header name
   * @param   boolean  $return  return or output contents
   * @param   boolean  $esc     html-escape output
   * @param   boolean  $hide    hide in html comments
   * @return  string
   */  
  static function debug($var,$name=null,$return=false,$esc=true,$hide=false)
  {
    ob_start();
    if(!$hide)
    {
      echo '<pre style="background-color: #000; border: 1px solid #3f3; clear: both; color: #3f3; line-height: 1.2em; margin: 2em 0; text-align: left;">';
      if(isset($name))
        echo '<strong style="background-color: #3f3; color: #000; display: block; padding: .33em 12px;">'.$name.'</strong>';
      echo '<span style="display: block; max-height: 430px; overflow: auto; padding: 0 6px 1.2em 6px;">';
    }
    else
      echo '<!--';
    echo $esc ? htmlentities(print_r($var,true)) : print_r($var,true);
    if(!$hide)
      echo '</span></pre>';
    else
      echo '-->';
    if(!$return)
      ob_end_flush();
    else
    {
      $contents = ob_get_contents();
      ob_end_clean();
      return $contents;
    }
  }
  
  /**
   * redirect
   *
   * redirect web browser and exit
   *
   * @access  public
   * @param   string   $uri     where to redirect to
   */  
  static function redirect($uri)
  {
    // sanity check
    if(empty($uri))
      return false;
      
    header("Location: $uri");
    exit;
  }
  
}
  
  
?>
