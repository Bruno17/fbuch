<?php
$xpdo_meta_map['mvMimeType']= array (
  'package' => 'fbuch',
  'version' => '1.1',
  'table' => 'mv_mime_types',
  'extends' => 'xPDOSimpleObject',
  'tableMeta' => 
  array (
    'engine' => 'MyISAM',
  ),
  'fields' => 
  array (
    'name' => '',
    'description' => NULL,
    'mime_type' => NULL,
    'file_extensions' => NULL,
    'headers' => NULL,
    'binary' => 0,
  ),
  'fieldMeta' => 
  array (
    'name' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '255',
      'phptype' => 'string',
      'null' => false,
      'default' => '',
      'index' => 'unique',
    ),
    'description' => 
    array (
      'dbtype' => 'tinytext',
      'phptype' => 'string',
      'null' => true,
    ),
    'mime_type' => 
    array (
      'dbtype' => 'tinytext',
      'phptype' => 'string',
    ),
    'file_extensions' => 
    array (
      'dbtype' => 'tinytext',
      'phptype' => 'string',
    ),
    'headers' => 
    array (
      'dbtype' => 'mediumtext',
      'phptype' => 'array',
    ),
    'binary' => 
    array (
      'dbtype' => 'tinyint',
      'precision' => '1',
      'attributes' => 'unsigned',
      'phptype' => 'boolean',
      'null' => false,
      'default' => 0,
    ),
  ),
);
