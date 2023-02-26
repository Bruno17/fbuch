<?php
$xpdo_meta_map['fbuchDateType']= array (
  'package' => 'fbuch',
  'version' => '1.1',
  'table' => 'fbuch_date_types',
  'extends' => 'xPDOSimpleObject',
  'tableMeta' => 
  array (
    'engine' => 'MyISAM',
  ),
  'fields' => 
  array (
    'name' => '',
    'description' => '',
    'registrationpage_text' => '',
    'person_tpl_extratext' => '',
    'registration_hooksnippet' => '',
    'exportrow_hooksnippet' => '',
    'show_at_rowinglogbook_page' => 0,
    'element_invite' => 0,
    'allow_foreign_guests' => 0,
    'colorstyle' => '',
    'linkto_bootsgattung_name' => '',
    'linkto_boot_id' => 0,
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
    'description' => 
    array (
      'dbtype' => 'text',
      'phptype' => 'string',
      'null' => false,
      'default' => '',
    ),
    'registrationpage_text' => 
    array (
      'dbtype' => 'text',
      'phptype' => 'string',
      'null' => false,
      'default' => '',
    ),
    'person_tpl_extratext' => 
    array (
      'dbtype' => 'text',
      'phptype' => 'string',
      'null' => false,
      'default' => '',
    ),
    'registration_hooksnippet' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '100',
      'phptype' => 'string',
      'null' => false,
      'default' => '',
    ),
    'exportrow_hooksnippet' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '100',
      'phptype' => 'string',
      'null' => false,
      'default' => '',
    ),
    'show_at_rowinglogbook_page' => 
    array (
      'dbtype' => 'tinyint',
      'precision' => '1',
      'attributes' => 'unsigned',
      'phptype' => 'integer',
      'null' => false,
      'default' => 0,
    ),
    'element_invite' => 
    array (
      'dbtype' => 'int',
      'precision' => '1',
      'phptype' => 'integer',
      'null' => false,
      'default' => 0,
    ),
    'allow_foreign_guests' => 
    array (
      'dbtype' => 'int',
      'precision' => '1',
      'phptype' => 'integer',
      'null' => false,
      'default' => 0,
    ),
    'colorstyle' => 
    array (
      'dbtype' => 'varchar',
      'phptype' => 'string',
      'precision' => '100',
      'null' => false,
      'default' => '',
    ),
    'linkto_bootsgattung_name' => 
    array (
      'dbtype' => 'varchar',
      'phptype' => 'string',
      'precision' => '255',
      'null' => false,
      'default' => '',
    ),
    'linkto_boot_id' => 
    array (
      'dbtype' => 'int',
      'precision' => '10',
      'phptype' => 'integer',
      'null' => false,
      'default' => 0,
    ),
  ),
);
