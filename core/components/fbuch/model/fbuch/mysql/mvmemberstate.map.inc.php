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
  ),
);
