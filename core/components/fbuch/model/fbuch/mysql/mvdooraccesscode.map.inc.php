<?php
$xpdo_meta_map['mvDoorAccesscode']= array (
  'package' => 'fbuch',
  'version' => '1.1',
  'table' => 'mv_door_accesscodes',
  'extends' => 'xPDOSimpleObject',
  'tableMeta' => 
  array (
    'engine' => 'MyISAM',
  ),
  'fields' => 
  array (
    'code' => '',
    'other_person' => '',
    'member_id' => 0,
    'assignedby' => 0,
    'assignedon' => NULL,
    'blocked' => 0,
  ),
  'fieldMeta' => 
  array (
    'code' => 
    array (
      'dbtype' => 'varchar',
      'phptype' => 'string',
      'precision' => '100',
      'null' => false,
      'default' => '',
    ),
    'other_person' => 
    array (
      'dbtype' => 'varchar',
      'phptype' => 'string',
      'precision' => '100',
      'null' => false,
      'default' => '',
    ),
    'member_id' => 
    array (
      'dbtype' => 'int',
      'precision' => '10',
      'phptype' => 'integer',
      'null' => false,
      'default' => 0,
      'index' => 'index',
    ),
    'assignedby' => 
    array (
      'dbtype' => 'int',
      'precision' => '10',
      'phptype' => 'integer',
      'null' => false,
      'default' => 0,
    ),
    'assignedon' => 
    array (
      'dbtype' => 'datetime',
      'phptype' => 'datetime',
      'null' => true,
    ),
    'blocked' => 
    array (
      'dbtype' => 'int',
      'precision' => '1',
      'phptype' => 'integer',
      'null' => false,
      'default' => 0,
    ),
  ),
  'aggregates' => 
  array (
    'Member' => 
    array (
      'class' => 'mvMember',
      'local' => 'member_id',
      'foreign' => 'id',
      'cardinality' => 'one',
      'owner' => 'foreign',
    ),
  ),
);
