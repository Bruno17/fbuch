<?php
$xpdo_meta_map['fbuchDateInstructors']= array (
  'package' => 'fbuch',
  'version' => '1.1',
  'table' => 'fbuch_date_instructors',
  'extends' => 'xPDOSimpleObject',
  'fields' => 
  array (
    'name_id' => 0,
    'date_id' => 0,
    'createdby' => 0,
    'createdon' => NULL,
  ),
  'fieldMeta' => 
  array (
    'name_id' => 
    array (
      'dbtype' => 'int',
      'precision' => '10',
      'phptype' => 'integer',
      'null' => false,
      'default' => 0,
      'index' => 'index',
    ),
    'date_id' => 
    array (
      'dbtype' => 'int',
      'precision' => '10',
      'phptype' => 'integer',
      'null' => false,
      'default' => 0,
      'index' => 'index',
    ),
    'createdby' => 
    array (
      'dbtype' => 'int',
      'precision' => '10',
      'phptype' => 'integer',
      'null' => false,
      'default' => 0,
    ),
    'createdon' => 
    array (
      'dbtype' => 'datetime',
      'phptype' => 'datetime',
      'null' => true,
    ),
  ),
  'aggregates' => 
  array (
    'Date' => 
    array (
      'class' => 'fbuchDate',
      'local' => 'date_id',
      'foreign' => 'id',
      'cardinality' => 'one',
      'owner' => 'foreign',
    ),
    'Name' => 
    array (
      'class' => 'fbuchNames',
      'local' => 'name_id',
      'foreign' => 'id',
      'cardinality' => 'one',
      'owner' => 'foreign',
    ),
  ),
);
