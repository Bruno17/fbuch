<?php
$xpdo_meta_map['fbuchBootsNutzergruppe']= array (
  'package' => 'fbuch',
  'version' => '1.1',
  'table' => 'fbuch_bootsnutzergruppen',
  'extends' => 'xPDOSimpleObject',
  'tableMeta' => 
  array (
    'engine' => 'MyISAM',
  ),
  'fields' => 
  array (
    'name' => '',
    'color' => '',
    'color_name' => '',
    'description' => '',
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
    'color' => 
    array (
      'dbtype' => 'varchar',
      'phptype' => 'string',
      'precision' => '255',
      'null' => false,
      'default' => '',
    ),
    'color_name' => 
    array (
      'dbtype' => 'varchar',
      'phptype' => 'string',
      'precision' => '255',
      'null' => false,
      'default' => '',
    ),
    'description' => 
    array (
      'dbtype' => 'text',
      'phptype' => 'string',
      'null' => false,
      'default' => '',
    ),
  ),
  'aggregates' => 
  array (
    'Boote' => 
    array (
      'class' => 'fbuchBoot',
      'local' => 'id',
      'foreign' => 'nutzergruppe',
      'cardinality' => 'many',
      'owner' => 'local',
    ),
  ),
);
