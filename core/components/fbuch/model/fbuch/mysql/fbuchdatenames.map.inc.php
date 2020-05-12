<?php
$xpdo_meta_map['fbuchDateNames']= array (
  'package' => 'fbuch',
  'version' => '1.1',
  'table' => 'fbuch_date_names',
  'extends' => 'xPDOSimpleObject',
  'tableMeta' => 
  array (
    'engine' => 'MyISAM',
  ),
  'fields' => 
  array (
    'name_id' => 0,
    'date_id' => 0,
    'createdby' => 0,
    'createdon' => NULL,
    'guestname' => '',
    'guestemail' => '',
    'registeredby' => 0,
  ),
  'fieldMeta' => 
  array (
    'name_id' => 
    array (
      'dbtype' => 'int',
      'precision' => '10',
      'phptype' => 'integer',
      'null' => false,
      'default' => 0,
      'index' => 'index',
    ),
    'date_id' => 
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
    'guestname' => 
    array (
      'dbtype' => 'varchar',
      'phptype' => 'string',
      'precision' => '255',
      'null' => false,
      'default' => '',
    ),
    'guestemail' => 
    array (
      'dbtype' => 'varchar',
      'phptype' => 'string',
      'precision' => '255',
      'null' => false,
      'default' => '',
    ),
    'registeredby' => 
    array (
      'dbtype' => 'int',
      'precision' => '10',
      'phptype' => 'integer',
      'null' => false,
      'default' => 0,
      'index' => 'index',
    ),
  ),
  'aggregates' => 
  array (
    'Date' => 
    array (
      'class' => 'fbuchDate',
      'local' => 'date_id',
      'foreign' => 'id',
      'cardinality' => 'one',
      'owner' => 'foreign',
    ),
    'Name' => 
    array (
      'class' => 'fbuchNames',
      'local' => 'name_id',
      'foreign' => 'id',
      'cardinality' => 'one',
      'owner' => 'foreign',
    ),
    'Registeredby' => 
    array (
      'class' => 'fbuchNames',
      'local' => 'registeredby',
      'foreign' => 'id',
      'cardinality' => 'one',
      'owner' => 'foreign',
    ),
    'Fahrtname' => 
    array (
      'class' => 'fbuchFahrtNames',
      'local' => 'id',
      'foreign' => 'datenames_id',
      'cardinality' => 'one',
      'owner' => 'local',
    ),
  ),
);
