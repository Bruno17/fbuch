<?php
$xpdo_meta_map['fbuchDestination']= array (
  'package' => 'fbuch',
  'version' => '1.1',
  'table' => 'fbuch_destinations',
  'extends' => 'xPDOSimpleObject',
  'tableMeta' => 
  array (
    'engine' => 'MyISAM',
  ),
  'fields' => 
  array (
    'destination' => '',
    'km' => 0.0,
  ),
  'fieldMeta' => 
  array (
    'destination' => 
    array (
      'dbtype' => 'varchar',
      'phptype' => 'string',
      'precision' => '190',
      'null' => false,
      'default' => '',
    ),
    'km' => 
    array (
      'dbtype' => 'decimal',
      'precision' => '8,1',
      'phptype' => 'float',
      'null' => false,
      'default' => 0.0,
      'index' => 'index',
    ),
  ),
);
