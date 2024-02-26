<?php
$xpdo_meta_map['mvMemberState']= array (
  'package' => 'fbuch',
  'version' => '1.1',
  'table' => 'mv_member_states',
  'extends' => 'xPDOSimpleObject',
  'tableMeta' => 
  array (
    'engine' => 'MyISAM',
  ),
  'fields' => 
  array (
    'state' => '',
    'description' => '',
    'add_to_usergroups' => '',
    'remove_from_usergroups' => '',
    'can_be_added_to_entry' => 0,
    'can_be_invited' => 0,
    'can_self_register' => 0,
    'option_for_web_memberform' => 0,
  ),
  'fieldMeta' => 
  array (
    'state' => 
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
    'add_to_usergroups' => 
    array (
      'dbtype' => 'text',
      'phptype' => 'string',
      'null' => false,
      'default' => '',
    ),
    'remove_from_usergroups' => 
    array (
      'dbtype' => 'text',
      'phptype' => 'string',
      'null' => false,
      'default' => '',
    ),
    'can_be_added_to_entry' => 
    array (
      'dbtype' => 'tinyint',
      'precision' => '1',
      'attributes' => 'unsigned',
      'phptype' => 'integer',
      'null' => false,
      'default' => 0,
    ),
    'can_be_invited' => 
    array (
      'dbtype' => 'tinyint',
      'precision' => '1',
      'attributes' => 'unsigned',
      'phptype' => 'integer',
      'null' => false,
      'default' => 0,
    ),
    'can_self_register' => 
    array (
      'dbtype' => 'tinyint',
      'precision' => '1',
      'attributes' => 'unsigned',
      'phptype' => 'integer',
      'null' => false,
      'default' => 0,
    ),
    'option_for_web_memberform' => 
    array (
      'dbtype' => 'tinyint',
      'phptype' => 'integer',
      'precision' => '1',
      'null' => false,
      'default' => 0,
    ),
  ),
);
