<?php

class Smoke_Controller extends TinyMVC_Controller
{
  function index()
  {
    $this->view->assign('greeting','hello');
    $this->view->assign(array('name' => 'world'));
    $this->view->display('smoke_view');
  }

  function routed()
  {
    echo 'ROUTED OK';
  }

  function db()
  {
    $this->load->model('Smoke_Model','items');
    $this->items->setup();
    $db = $this->items->db;

    $db->from('items');
    $db->where('name','apple');
    $db->orwhere('qty=?',3);
    $db->orderby('id');
    $this->_check(array('apple','plum'), $this->_names($db->query_all()), 'where/orwhere');

    $db->from('items');
    $db->in('name',array('pear','plum'));
    $db->orderby('id');
    $db->limit(1,1);
    $this->_check(array('plum'), $this->_names($db->query_all()), 'in/limit');

    $db->where('name','pear');
    $db->update('items',array('qty' => 20));
    $this->_check(1, $db->affected_rows(), 'update');

    $db->from('items');
    $db->where('name','pear');
    $pear = $db->query_one();
    $this->_check('20', (string) $pear['qty'], 'query_one');

    $db->where('name','apple');
    $db->delete('items');

    $db->select('COUNT(*) AS n');
    $db->from('items');
    $count = $db->query_one();
    $this->_check('2', (string) $count['n'], 'delete/count');

    echo 'DB OK';
  }

  function uri()
  {
    $this->_check('a', $this->uri->segment(4), 'segment');
    $this->_check(array('a' => '1', 'b' => '2'), $this->uri->uri_to_assoc(4), 'uri_to_assoc');
    $this->_check(array('a','1','b','2'), $this->uri->uri_to_array(2), 'uri_to_array');
    echo 'URI OK';
  }

  function helper()
  {
    $out = TinyMVC_Script_Helper::debug(array('<x>'), 'dbg', true);
    $this->_check(true, strpos($out, '&lt;x&gt;') !== false, 'debug escaping');
    echo 'HELPER OK';
  }

  function warning()
  {
    $missing = array();
    return $missing['nope'];
  }

  function fatal()
  {
    throw new Error('engine error');
  }

  function _names($rows)
  {
    $names = array();
    foreach($rows as $row)
      $names[] = $row['name'];
    return $names;
  }

  function _check($expected, $actual, $label)
  {
    if($expected !== $actual)
      throw new Exception("{$label} failed: expected " . var_export($expected, true) . ', got ' . var_export($actual, true));
  }
}

?>
