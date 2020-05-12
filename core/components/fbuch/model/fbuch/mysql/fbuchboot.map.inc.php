<?php
$xpdo_meta_map['fbuchBoot']= array (
  'package' => 'fbuch',
  'version' => '1.1',
  'table' => 'fbuch_boote',
  'extends' => 'xPDOSimpleObject',
  'tableMeta' => 
  array (
    'engine' => 'MyISAM',
  ),
  'fields' => 
  array (
    'name' => '',
    'gattung' => '',
    'seats' => 0,
    'bootid_old' => 0,
    'gewichtsklasse' => '',
    'nutzergruppe' => 0,
    'nutzernamen' => '',
    'riggerung' => '',
    'gesperrt' => 0,
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
    'name' => 
    array (
      'dbtype' => 'varchar',
      'phptype' => 'string',
      'precision' => '255',
      'null' => false,
      'default' => '',
      'index' => 'index',
    ),
    'gattung' => 
    array (
      'dbtype' => 'varchar',
      'phptype' => 'string',
      'precision' => '10',
      'null' => false,
      'default' => '',
    ),
    'seats' => 
    array (
      'dbtype' => 'tinyint',
      'precision' => '2',
      'attributes' => 'unsigned',
      'phptype' => 'integer',
      'null' => false,
      'default' => 0,
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
    'gewichtsklasse' => 
    array (
      'dbtype' => 'varchar',
      'phptype' => 'string',
      'precision' => '255',
      'null' => false,
      'default' => '',
    ),
    'nutzergruppe' => 
    array (
      'dbtype' => 'int',
      'precision' => '10',
      'phptype' => 'integer',
      'null' => false,
      'default' => 0,
      'index' => 'index',
    ),
    'nutzernamen' => 
    array (
      'dbtype' => 'text',
      'phptype' => 'string',
      'null' => false,
      'default' => '',
    ),
    'riggerung' => 
    array (
      'dbtype' => 'varchar',
      'phptype' => 'string',
      'precision' => '255',
      'null' => false,
      'default' => '',
      'index' => 'index',
    ),
    'gesperrt' => 
    array (
      'dbtype' => 'int',
      'precision' => '1',
      'phptype' => 'integer',
      'null' => false,
      'default' => 0,
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
  'aggregates' => 
  array (
    'Fahrten' => 
    array (
      'class' => 'fbuchFahrt',
      'local' => 'id',
      'foreign' => 'boot_id',
      'cardinality' => 'many',
      'owner' => 'local',
    ),
    'Gattung' => 
    array (
      'class' => 'fbuchBootsGattung',
      'local' => 'gattung',
      'foreign' => 'link_key',
      'cardinality' => 'one',
      'owner' => 'local',
    ),
    'Nutzergruppe' => 
    array (
      'class' => 'fbuchBootsNutzergruppe',
      'local' => 'nutzergruppe',
      'foreign' => 'id',
      'cardinality' => 'one',
      'owner' => 'local',
    ),
  ),
);
