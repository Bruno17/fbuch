<?php
$xpdo_meta_map['mvDoorAccesscodeMember']= array (
  'package' => 'fbuch',
  'version' => '1.1',
  'table' => 'mv_door_accesscodes_members',
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
    'comment' => '',
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
    'comment' => 
    array (
      'dbtype' => 'text',
      'phptype' => 'string',
      'null' => false,
      'default' => '',
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
    'Code' => 
    array (
      'class' => 'mvDoorAccesscode',
      'local' => 'code',
      'foreign' => 'code',
      'cardinality' => 'one',
      'owner' => 'foreign',
    ),
  ),
);
