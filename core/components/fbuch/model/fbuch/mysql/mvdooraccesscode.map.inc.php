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
    'time_setting' => 0,
    'blocked' => 0,
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
    'time_setting' => 
    array (
      'dbtype' => 'int',
      'precision' => '1',
      'phptype' => 'integer',
      'null' => false,
      'default' => 0,
    ),
    'blocked' => 
    array (
      'dbtype' => 'int',
      'precision' => '1',
      'phptype' => 'integer',
      'null' => false,
      'default' => 0,
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
    'CodeMember' => 
    array (
      'class' => 'mvDoorAccesscodeMember',
      'local' => 'code',
      'foreign' => 'code',
      'cardinality' => 'many',
      'owner' => 'local',
    ),
  ),
);
