<?php
$xpdo_meta_map['fbuchBootsGattung']= array (
  'package' => 'fbuch',
  'version' => '1.1',
  'table' => 'fbuch_bootsgattungen',
  'extends' => 'xPDOSimpleObject',
  'tableMeta' => 
  array (
    'engine' => 'MyISAM',
  ),
  'fields' => 
  array (
    'name' => '',
    'link_key' => '',
  ),
  'fieldMeta' => 
  array (
    'name' => 
    array (
      'dbtype' => 'varchar',
      'phptype' => 'string',
      'precision' => '255',
      'null' => false,
      'default' => '',
      'index' => 'index',
    ),
    'link_key' => 
    array (
      'dbtype' => 'varchar',
      'phptype' => 'string',
      'precision' => '255',
      'null' => false,
      'default' => '',
      'index' => 'index',
    ),
  ),
  'aggregates' => 
  array (
    'Boote' => 
    array (
      'class' => 'fbuchBoot',
      'local' => 'link_key',
      'foreign' => 'gattung',
      'cardinality' => 'many',
      'owner' => 'local',
    ),
  ),
);
