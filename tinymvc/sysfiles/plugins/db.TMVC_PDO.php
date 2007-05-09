<?php

/***
 * Name:       TinyMVC
 * About:      An MVC application framework for PHP
 * Copyright:  (C) 2007, New Digital Group Inc.
 * Author:     Monte Ohrt, monte [at] ohrt [dot] com
 * License:    LGPL, see included license file  
 ***/

// ------------------------------------------------------------------------

/* define SQL actions */
if(!defined('TMVC_SQL_NONE'))
  define('TMVC_SQL_NONE', 0);
if(!defined('TMVC_SQL_INIT'))
  define('TMVC_SQL_INIT', 1);
if(!defined('TMVC_SQL_ALL'))
  define('TMVC_SQL_ALL', 2);

/**
 * TMVC_PDO
 * 
 * PDO database access
 * compile PHP with --enable-pdo (default with PHP 5.1+)
 *
 * @package		TinyMVC
 * @author		Monte Ohrt
 */

class TMVC_PDO
{
 	/**
	 * $pdo
	 *
	 * the PDO object handle
	 *
	 * @access	public
	 */
  var $pdo = null;
  
 	/**
	 * $result
	 *
	 * the query result handle
	 *
	 * @access	public
	 */
  var $result = null;
  
 	/**
	 * $fetch_mode
	 *
	 * the results fetch mode
	 *
	 * @access	public
	 */
  var $fetch_mode = PDO::FETCH_ASSOC;
  
 	/**
	 * class constructor
	 *
	 * @access	public
	 */
  function __construct() {

   include(TMVC_MYAPPDIR . 'configs' . DS . 'database.php');
    
   if(!class_exists('PDO'))
     trigger_error("PHP PDO package is required.",E_USER_ERROR);
     
   if(empty($config))
     trigger_error("database definitions required.",E_USER_ERROR);

    /* create link */
    try {    
      $this->pdo = new PDO(
        "{$config['type']}:host={$config['host']};dbname={$config['name']}",
        $config['user'],
        $config['pass'],
        array(PDO::ATTR_PERSISTENT => !empty($config['persistent']) ? true : false)
        );
    } catch (PDOException $e) {
        trigger_error(sprintf("Can't connect to PDO database '{$config['type']}'. Error: %s",$e->getMessage()),E_USER_ERROR);
    }
    
  }

	/**
	 * query
	 *
	 * execute a database query
	 *
	 * @access	public
	 * @param   array $params an array of query params
	 * @param   int $fetch_mode the fetch formatting mode
	 */    
  function query($query,$params=null,$fetch_mode=null)
  {
    return $this->_query($query,$params,TMVC_SQL_NONE,$fetch_mode);
  }  

	/**
	 * query_all
	 *
	 * execute a database query, return all records
	 *
	 * @access	public
	 * @param   array $params an array of query params
	 * @param   int $fetch_mode the fetch formatting mode
	 */    
  function query_all($query,$params=null,$fetch_mode=null)
  {
    return $this->_query($query,$params,TMVC_SQL_ALL,$fetch_mode);
  }  

	/**
	 * query_init
	 *
	 * execute a database query, return one record
	 *
	 * @access	public
	 * @param   array $params an array of query params
	 * @param   int $fetch_mode the fetch formatting mode
	 */    
  function query_init($query,$params=null,$fetch_mode=null)
  {
    return $this->_query($query,$params,TMVC_SQL_INIT,$fetch_mode);
  }  
  
	/**
	 * _query
	 *
	 * internal query method
	 *
	 * @access	private
	 * @param   string $query the query string
	 * @param   array $params an array of query params
	 * @param   int $return_type none/all/init
	 * @param   int $fetch_mode the fetch formatting mode
	 */    
  function _query($query,$params=null,$return_type = TMVC_SQL_NONE,$fetch_mode=null)
  {
  
    /* if no fetch mode, use default */
    if(!isset($fetch_mode))
      $fetch_mode = PDO::FETCH_ASSOC;  
  
    /* prepare the query */
    $this->result = $this->pdo->prepare($query);
    
    /* execute with params */
    $this->result->execute($params);  
  
    /* get result with fetch mode */
    $this->result->setFetchMode($fetch_mode);  
  
    switch($return_type)
    {
      case TMVC_SQL_INIT:
        return $this->result->fetch();
        break;
      case TMVC_SQL_ALL:
        return $this->result->fetchAll();
        break;
      case TMVC_SQL_NONE:
      default:
        break;
    }  
    
  }

	/**
	 * next
	 *
	 * go to next record in result set
	 *
	 * @access	public
	 * @param   int $fetch_mode the fetch formatting mode
	 */    
  function next($fetch_mode=null)
  {
    if(isset($fetch_mode))
      $this->result->setFetchMode($fetch_mode);
    return $this->result->fetch();
  }

	/**
	 * last_insert_id
	 *
	 * get last insert id from previous query
	 *
	 * @access	public
	 * @return	int $id
	 */    
  function last_insert_id()
  {
    return $this->pdo->lastInsertId();
  }

	/**
	 * num_rows
	 *
	 * get number of returned rows from previous select
	 *
	 * @access	public
	 * @return	int $id
	 */    
  function num_rows()
  {
    return count($this->result->fetchAll());
  }


 	/**
	 * class destructor
	 *
	 * @access	public
	 */
  function __destruct()
  {
    $this->pdo = null;  
  }
  
}

?>
