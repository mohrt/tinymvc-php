<?php

/***
 * Name:       TinyMVC
 * About:      An MVC application framework for PHP
 * Copyright:  (C) 2007-2008 Monte Ohrt, All rights reserved.
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
 * TinyMVC_PDO
 * 
 * PDO database access
 * compile PHP with --enable-pdo (default with PHP 5.1+)
 *
 * @package		TinyMVC
 * @author		Monte Ohrt
 */

class TinyMVC_PDO
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
	 * $query_params
	 *
	 * @access	public
	 */
  var $query_params = array('select' => '*');

 	/**
	 * $last_query
	 *
	 * @access	public
	 */
  var $last_query = null;

 	/**
	 * $last_query_type
	 *
	 * @access	public
	 */
  var $last_query_type = null;
  
 	/**
	 * class constructor
	 *
	 * @access	public
	 */
  function __construct($config) {
    
   if(!class_exists('PDO',false))
     throw new Exception("PHP PDO package is required.");
     
   if(empty($config))
     throw new Exception("database definitions required.");

   if(empty($config['charset']))
    $config['charset'] = 'utf8';

   if(!empty($config['dsn']))
     $dsn = $config['dsn'];
   elseif($config['type'] == 'sqlsrv')
     $dsn = "{$config['type']}:Server={$config['host']};Database={$config['name']}";
   else
     $dsn = "{$config['type']}:host={$config['host']};dbname={$config['name']};charset={$config['charset']}";
     
    /* attempt to instantiate PDO object and database connection */
    try {    
      $this->pdo = new PDO(
        $dsn,
        $config['user'],
        $config['pass'],
        array(PDO::ATTR_PERSISTENT => !empty($config['persistent']) ? true : false)
        );
      $this->pdo->exec("SET CHARACTER SET {$config['charset']}"); 
    } catch (PDOException $e) {
        throw new Exception(sprintf("Can't connect to PDO database '{$config['type']}'. Error: %s",$e->getMessage()));
    }
    
    // make PDO handle errors with exceptions
    $this->pdo->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);    
    
  }

	/**
	 * select
	 *
	 * set the  active record select clause
	 *
	 * @access	public
	 * @param   string $clause
	 */    
  function select($clause)
  {
    return $this->query_params['select'] = $clause;
  }  

	/**
	 * from
	 *
	 * set the  active record from clause
	 *
	 * @access	public
	 * @param   string $clause
	 */    
  function from($clause)
  {
    return $this->query_params['from'] = $clause;
  }  

	/**
	 * where
	 *
	 * set the  active record where clause
	 *
	 * @access	public
	 * @param   string $clause
	 */    
  function where($clause,$args)
  {
    if(empty($clause))
      throw new Exception(sprintf("where cannot be empty"));
  
    if(!preg_match('![=<>]!',$clause))
     $clause .= '=';  
  
    if(strpos($clause,'?')===false)
      $clause .= '?';
      
    $this->_where($clause,(array)$args,'AND');    
  }  

	/**
	 * orwhere
	 *
	 * set the  active record orwhere clause
	 *
	 * @access	public
	 * @param   string $clause
	 */    
  function orwhere($clause,$args)
  {
    $this->_where($clause,$args,'OR');    
  }  
  
	/**
	 * _where
	 *
	 * set the active record where clause
	 *
	 * @access	public
	 * @param   string $clause
	 */    
  private function _where($clause, $args=array(), $prefix='AND')
  {    
    // sanity check
    if(empty($clause))
      return false;
    
    // make sure number of ? match number of args
    if(($count = substr_count($clause,'?')) && (count($args) != $count))
      throw new Exception(sprintf("Number of where clause args don't match number of ?: '%s'",$clause));
      
    if(!isset($this->query_params['where']))
      $this->query_params['where'] = array();
      
    return $this->query_params['where'][] = array('clause'=>$clause,'args'=>$args,'prefix'=>$prefix);
  }  

	/**
	 * join
	 *
	 * set the  active record join clause
	 *
	 * @access	public
	 * @param   string $clause
	 */    
  function join($join_table,$join_on,$join_type=null)
  {
    $clause = "JOIN {$join_table} ON {$join_on}";
    
    if(!empty($join_type))
      $clause = $join_type . ' ' . $clause;
    
    if(!isset($this->query_params['join']))
      $this->query_params['join'] = array();
      
    $this->query_params['join'][] = $clause;
  }  

	/**
	 * in
	 *
	 * set an active record IN clause
	 *
	 * @access	public
	 * @param   string $clause
	 */    
  function in($field,$elements,$list=false)
  {
    $this->_in($field,$elements,$list,'AND');
  }

	/**
	 * orin
	 *
	 * set an active record OR IN clause
	 *
	 * @access	public
	 * @param   string $clause
	 */    
  function orin($field,$elements,$list=false)
  {
    $this->_in($field,$elements,$list,'OR');
  }

  
	/**
	 * _in
	 *
	 * set an active record IN clause
	 *
	 * @access	public
	 * @param   string $clause
	 */    
  private function _in($field,$elements,$list=false,$prefix='AND')
  { 
    if(!$list)
    {
      if(!is_array($elements))
        $elements = explode(',',$elements);
        
      // quote elements for query
      foreach($elements as $idx => $element)
        $elements[$idx] = $this->pdo->quote($element);
      
      $clause = sprintf("{$field} IN (%s)", implode(',',$elements));
    }
    else
      $clause = sprintf("{$field} IN (%s)", $elements);
    
    $this->_where($clause,array(),$prefix);
  }  
  
	/**
	 * orderby
	 *
	 * set the  active record orderby clause
	 *
	 * @access	public
	 * @param   string $clause
	 */    
  function orderby($clause)
  {    
    $this->_set_clause('orderby',$clause);
  }  

	/**
	 * groupby
	 *
	 * set the active record groupby clause
	 *
	 * @access	public
	 * @param   string $clause
	 */    
  function groupby($clause)
  {    
    $this->_set_clause('groupby',$clause);
  }  

	/**
	 * limit
	 *
	 * set the active record limit clause
	 *
	 * @access	public
	 * @param   int    $limit
	 * @param   int    $offset
	 */    
  function limit($limit, $offset=0)
  {    
    if(!empty($offset))
      $this->_set_clause('limit',sprintf('%d,%d',(int)$offset,(int)$limit));
    else
      $this->_set_clause('limit',sprintf('%d',(int)$limit));
  }  
  
	/**
	 * _set_clause
	 *
	 * set an active record clause
	 *
	 * @access	public
	 * @param   string $clause
	 */    
  private function _set_clause($type, $clause, $args=array())
  {    
    // sanity check
    if(empty($type)||empty($clause))
      return false;
      
    $this->query_params[$type] = array('clause'=>$clause);
    
    if(isset($args))
      $this->query_params[$type]['args'] = $args;
      
  }  
  
	/**
	 * _query_assemble
	 *
	 * get an active record query
	 *
	 * @access	public
	 * @param   string $fetch_mode the PDO fetch mode
	 */    
  private function _query_assemble(&$params,$fetch_mode=null)
  {
  
    if(empty($this->query_params['from']))
    {
      throw new Exception("Unable to get(), set from() first");
      return false;
    }
    
    $query = array();
    $query[] = "SELECT {$this->query_params['select']}";
    $query[] = "FROM {$this->query_params['from']}";

    // assemble join clause
    if(!empty($this->query_params['join']))
      foreach($this->query_params['join'] as $cjoin)
        $query[] = $cjoin;
    
    // assemble where clause
    if($where = $this->_assemble_where($where_string,$params))
      $query[] = $where_string;

    // assemble groupby clause
    if(!empty($this->query_params['groupby']))
      $query[] = "GROUP BY {$this->query_params['groupby']['clause']}";
    
    // assemble orderby clause
    if(!empty($this->query_params['orderby']))
      $query[] = "ORDER BY {$this->query_params['orderby']['clause']}";
    
    // assemble limit clause
    if(!empty($this->query_params['limit']))
      $query[] = "LIMIT {$this->query_params['limit']['clause']}";
    
    $query_string = implode(' ',$query);
    $this->last_query = $query_string;
    
    $this->query_params = array('select' => '*');
    
    return $query_string;
    
  }  
  
	/**
	 * _assemble_where
	 *
	 * assemble where query
	 *
	 * @access	private
	 */    
  private function _assemble_where(&$where,&$params)
  {
    if(!empty($this->query_params['where']))
    {
      $where_init = false;
      $where_parts = array();
      $params = array();
      foreach($this->query_params['where'] as $cwhere)
      {
        $prefix = !$where_init ? 'WHERE' : $cwhere['prefix'];
        $where_parts[] = "{$prefix} {$cwhere['clause']}";
        $params = array_merge($params,(array) $cwhere['args']);
        $where_init = true;
      }
      $where = implode(' ',$where_parts);      
      return true;
    }
    return false;
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
  function query($query=null,$params=null,$fetch_mode=null)
  {
    if(!isset($query))
      $query = $this->_query_assemble($params,$fetch_mode);
  
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
  function query_all($query=null,$params=null,$fetch_mode=null)
  {
    if(!isset($query))
      $query = $this->_query_assemble($params,$fetch_mode);
  
    return $this->_query($query,$params,TMVC_SQL_ALL,$fetch_mode);
  }  

	/**
	 * query_one
	 *
	 * execute a database query, return one record
	 *
	 * @access	public
	 * @param   array $params an array of query params
	 * @param   int $fetch_mode the fetch formatting mode
	 */    
  function query_one($query=null,$params=null,$fetch_mode=null)
  {
    if(!isset($query))
    {
      $this->limit(1);
      $query = $this->_query_assemble($params,$fetch_mode);
    }
  
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
    try {
      $this->result = $this->pdo->prepare($query);
    } catch (PDOException $e) {
        throw new Exception(sprintf("PDO Error: %s Query: %s",$e->getMessage(),$query));
        return false;
    }      
    
    /* execute with params */
    try {
      $this->result->execute($params);  
    } catch (PDOException $e) {
        throw new Exception(sprintf("PDO Error: %s Query: %s",$e->getMessage(),$query));
        return false;
    }      
  
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
        return true;
        break;
    }
    
  }

	/**
	 * update
	 *
	 * update records
	 *
	 * @access	public
	 * @param   int $fetch_mode the fetch formatting mode
	 */    
  function update($table,$columns)
  {
    if(empty($table))
    {
      throw new Exception("Unable to update, table name required");
      return false;
    }
    if(empty($columns)||!is_array($columns))
    {
      throw new Exception("Unable to update, at least one column required");
      return false;
    }
    $query = array("UPDATE {$table} SET");
    $fields = array();
    $params = array();
    foreach($columns as $cname => $cvalue)
    {
      if(!empty($cname))
      {
        $fields[] = "{$cname}=?";
        $params[] = $cvalue;
      }
    }
    $query[] = implode(',',$fields);
    
    // assemble where clause
    if($this->_assemble_where($where_string,$where_params))
    {    
      $query[] = $where_string;
      $params = array_merge($params,$where_params);
    }

    $query = implode(' ',$query);
    
    $this->query_params = array('select' => '*');
    
    return $this->_query($query,$params);
  }

	/**
	 * insert
	 *
	 * update records
	 *
	 * @access	public
	 * @param   string $table
	 * @param   array  $columns
	 */    
  function insert($table,$columns)
  {
    if(empty($table))
    {
      throw new Exception("Unable to insert, table name required");
      return false;
    }
    if(empty($columns)||!is_array($columns))
    {
      throw new Exception("Unable to insert, at least one column required");
      return false;
    }
    
    $column_names = array_keys($columns);
    
    $query = array(sprintf("INSERT INTO `{$table}` (`%s`) VALUES",implode('`,`',$column_names)));
    $fields = array();
    $params = array();
    foreach($columns as $cname => $cvalue)
    {
      if(!empty($cname))
      {
        $fields[] = "?";
        $params[] = $cvalue;
      }
    }
    $query[] = '(' . implode(',',$fields) . ')';
    
    $query = implode(' ',$query);
    
    $this->_query($query,$params);
    return $this->last_insert_id();
  }

  
	/**
	 * delete
	 *
	 * delete records
	 *
	 * @access	public
	 * @param   string $table
	 * @param   array  $columns
	 */    
  function delete($table)
  {
    if(empty($table))
    {
      throw new Exception("Unable to delete, table name required");
      return false;
    }
    $query = array("DELETE FROM `{$table}`");
    $params = array();
    
    // assemble where clause
    if($this->_assemble_where($where_string,$where_params))
    {    
      $query[] = $where_string;
      $params = array_merge($params,$where_params);
    }

    $query = implode(' ',$query);
    
    $this->query_params = array('select' => '*');
    
    return $this->_query($query,$params);
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
    return $this->result->rowCount();
  }

	/**
	 * affected_rows
	 *
	 * get number of affected rows from previous insert/update/delete
	 *
	 * @access	public
	 * @return	int $id
	 */    
  function affected_rows()
  {
    return $this->result->rowCount();
  }
  
	/**
	 * last_query
	 *
	 * return last query executed
	 *
	 * @access	public
	 */    
  function last_query()
  {
    return $this->last_query;
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
