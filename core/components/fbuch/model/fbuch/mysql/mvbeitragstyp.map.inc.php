<?php
$xpdo_meta_map['mvBeitragstyp']= array (
  'package' => 'fbuch',
  'version' => '1.1',
  'table' => 'mv_beitragstypen',
  'extends' => 'xPDOSimpleObject',
  'tableMeta' => 
  array (
    'engine' => 'MyISAM',
  ),
  'fields' => 
  array (
    'name' => '',
    'selectable' => 1,
    'beitrag' => '',
    'max_age' => 0,
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
    'selectable' => 
    array (
      'dbtype' => 'tinyint',
      'precision' => '1',
      'attributes' => 'unsigned',
      'phptype' => 'integer',
      'null' => false,
      'default' => 1,
    ),
    'beitrag' => 
    array (
      'dbtype' => 'varchar',
      'phptype' => 'string',
      'precision' => '10',
      'null' => false,
      'default' => '',
    ),
    'max_age' => 
    array (
      'dbtype' => 'int',
      'precision' => '2',
      'phptype' => 'integer',
      'null' => false,
      'default' => 0,
    ),
  ),
  'aggregates' => 
  array (
    'Members' => 
    array (
      'class' => 'mvMember',
      'local' => 'id',
      'foreign' => 'beitragstyp_id',
      'cardinality' => 'many',
      'owner' => 'local',
    ),
  ),
);
