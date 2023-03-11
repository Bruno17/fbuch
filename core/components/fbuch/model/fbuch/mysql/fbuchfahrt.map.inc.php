<?php
$xpdo_meta_map['fbuchFahrt']= array (
  'package' => 'fbuch',
  'version' => '1.1',
  'table' => 'fbuch_fahrten',
  'extends' => 'xPDOSimpleObject',
  'tableMeta' => 
  array (
    'engine' => 'MyISAM',
  ),
  'fields' => 
  array (
    'date' => NULL,
    'date_end' => NULL,
    'boot_id' => 0,
    'date_id' => 0,
    'km' => 0.0,
    'destination' => '',
    'kmstand_start' => 0.0,
    'kmstand_end' => 0.0,
    'purpose' => '',
    'wfahrt' => 0,
    'note' => '',
    'direction' => '',
    'start_time' => '',
    'end_time' => '',
    'fahrtid_old' => 0,
    'bootid_old' => 0,
    'locked' => 0,
    'finished' => 0,
    'lock_password' => '',
    'finishedby' => 0,
    'finishedon' => NULL,
    'createdby' => 0,
    'createdon' => NULL,
    'editedby' => 0,
    'editedon' => NULL,
    'deleted' => 0,
    'deletedon' => NULL,
    'deletedby' => 0,
  ),
  'fieldMeta' => 
  array (
    'date' => 
    array (
      'dbtype' => 'datetime',
      'phptype' => 'datetime',
      'null' => true,
    ),
    'date_end' => 
    array (
      'dbtype' => 'datetime',
      'phptype' => 'datetime',
      'null' => true,
    ),
    'boot_id' => 
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
    'km' => 
    array (
      'dbtype' => 'decimal',
      'precision' => '8,1',
      'phptype' => 'float',
      'null' => false,
      'default' => 0.0,
      'index' => 'index',
    ),
    'destination' => 
    array (
      'dbtype' => 'varchar',
      'phptype' => 'string',
      'precision' => '190',
      'null' => false,
      'default' => '',
    ),
    'kmstand_start' => 
    array (
      'dbtype' => 'decimal',
      'precision' => '8,1',
      'phptype' => 'float',
      'null' => false,
      'default' => 0.0,
      'index' => 'index',
    ),
    'kmstand_end' => 
    array (
      'dbtype' => 'decimal',
      'precision' => '8,1',
      'phptype' => 'float',
      'null' => false,
      'default' => 0.0,
      'index' => 'index',
    ),
    'purpose' => 
    array (
      'dbtype' => 'varchar',
      'phptype' => 'string',
      'precision' => '50',
      'null' => false,
      'default' => '',
    ),
    'wfahrt' => 
    array (
      'dbtype' => 'tinyint',
      'precision' => '1',
      'attributes' => 'unsigned',
      'phptype' => 'integer',
      'null' => false,
      'default' => 0,
    ),
    'note' => 
    array (
      'dbtype' => 'text',
      'phptype' => 'string',
      'null' => false,
      'default' => '',
    ),
    'direction' => 
    array (
      'dbtype' => 'varchar',
      'phptype' => 'string',
      'precision' => '20',
      'null' => false,
      'default' => '',
    ),
    'start_time' => 
    array (
      'dbtype' => 'varchar',
      'phptype' => 'string',
      'precision' => '20',
      'null' => false,
      'default' => '',
    ),
    'end_time' => 
    array (
      'dbtype' => 'varchar',
      'phptype' => 'string',
      'precision' => '20',
      'null' => false,
      'default' => '',
    ),
    'fahrtid_old' => 
    array (
      'dbtype' => 'int',
      'precision' => '10',
      'phptype' => 'integer',
      'null' => false,
      'default' => 0,
      'index' => 'index',
    ),
    'bootid_old' => 
    array (
      'dbtype' => 'int',
      'precision' => '10',
      'phptype' => 'integer',
      'null' => false,
      'default' => 0,
      'index' => 'index',
    ),
    'locked' => 
    array (
      'dbtype' => 'tinyint',
      'precision' => '1',
      'attributes' => 'unsigned',
      'phptype' => 'integer',
      'null' => false,
      'default' => 0,
    ),
    'finished' => 
    array (
      'dbtype' => 'tinyint',
      'precision' => '1',
      'attributes' => 'unsigned',
      'phptype' => 'integer',
      'null' => false,
      'default' => 0,
    ),
    'lock_password' => 
    array (
      'dbtype' => 'varchar',
      'phptype' => 'string',
      'precision' => '50',
      'null' => false,
      'default' => '',
    ),
    'finishedby' => 
    array (
      'dbtype' => 'int',
      'precision' => '10',
      'phptype' => 'integer',
      'null' => false,
      'default' => 0,
    ),
    'finishedon' => 
    array (
      'dbtype' => 'datetime',
      'phptype' => 'datetime',
      'null' => true,
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
    'editedby' => 
    array (
      'dbtype' => 'int',
      'precision' => '10',
      'phptype' => 'integer',
      'null' => false,
      'default' => 0,
    ),
    'editedon' => 
    array (
      'dbtype' => 'datetime',
      'phptype' => 'datetime',
      'null' => true,
    ),
    'deleted' => 
    array (
      'dbtype' => 'tinyint',
      'precision' => '1',
      'attributes' => 'unsigned',
      'phptype' => 'integer',
      'null' => false,
      'default' => 0,
    ),
    'deletedon' => 
    array (
      'dbtype' => 'datetime',
      'phptype' => 'datetime',
      'null' => true,
    ),
    'deletedby' => 
    array (
      'dbtype' => 'int',
      'precision' => '10',
      'phptype' => 'integer',
      'null' => false,
      'default' => 0,
    ),
  ),
  'composites' => 
  array (
    'Names' => 
    array (
      'class' => 'fbuchFahrtNames',
      'local' => 'id',
      'foreign' => 'fahrt_id',
      'cardinality' => 'many',
      'owner' => 'local',
    ),
  ),
  'aggregates' => 
  array (
    'Boot' => 
    array (
      'class' => 'fbuchBoot',
      'local' => 'boot_id',
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
