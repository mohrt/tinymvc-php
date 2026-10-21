<?php

class Smoke_Model extends TinyMVC_Model
{
  function setup()
  {
    $this->db->query('CREATE TABLE items (id INTEGER PRIMARY KEY, name TEXT, qty INTEGER)');
    foreach(array('apple' => 1, 'pear' => 2, 'plum' => 3) as $name => $qty)
      $this->db->insert('items', array('name' => $name, 'qty' => $qty));
  }
}

?>
