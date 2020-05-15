<?php
$xpdo_meta_map['mvMemberGroup']= array (
  'package' => 'fbuch',
  'version' => '1.1',
  'table' => 'mv_membergroups',
  'extends' => 'xPDOSimpleObject',
  'tableMeta' => 
  array (
    'engine' => 'MyISAM',
  ),
  'fields' => 
  array (
    'name' => '',
  ),
  'fieldMeta' => 
  array (
    'name' => 
    array (
      'dbtype' => 'varchar',
      'phptype' => 'string',
      'precision' => '100',
      'null' => false,
      'default' => '',
    ),
  ),
  'aggregates' => 
  array (
    'Roles' => 
    array (
      'class' => 'mvRole',
      'local' => 'id',
      'foreign' => 'group_id',
      'cardinality' => 'many',
      'owner' => 'local',
    ),
  ),
);
