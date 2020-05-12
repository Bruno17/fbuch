<?php
$xpdo_meta_map['fbuchDateMailinglists']= array (
  'package' => 'fbuch',
  'version' => '1.1',
  'table' => 'fbuch_date_mailinglists',
  'extends' => 'xPDOSimpleObject',
  'tableMeta' => 
  array (
    'engine' => 'MyISAM',
  ),
  'fields' => 
  array (
    'date_id' => 0,
    'list_id' => 0,
    'createdby' => 0,
    'createdon' => NULL,
  ),
  'fieldMeta' => 
  array (
    'date_id' => 
    array (
      'dbtype' => 'int',
      'precision' => '10',
      'phptype' => 'integer',
      'null' => false,
      'default' => 0,
      'index' => 'index',
    ),
    'list_id' => 
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
    'Mailinglist' => 
    array (
      'class' => 'fbuchMailinglist',
      'local' => 'list_id',
      'foreign' => 'id',
      'cardinality' => 'one',
      'owner' => 'foreign',
    ),
    'Date' => 
    array (
      'class' => 'fbuchDate',
      'local' => 'date_id',
      'foreign' => 'id',
      'cardinality' => 'one',
      'owner' => 'foreign',
    ),
  ),
);
