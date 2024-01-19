<?php
$xpdo_meta_map['fbuchCompetencyLevel']= array (
  'package' => 'fbuch',
  'version' => '1.1',
  'table' => 'fbuch_competency_levels',
  'extends' => 'xPDOSimpleObject',
  'tableMeta' => 
  array (
    'engine' => 'MyISAM',
  ),
  'fields' => 
  array (
    'name' => '',
    'level' => '',
    'color' => '',
    'color_name' => '',
    'description' => '',
    'technical_requirements' => '',
    'safety_requirements' => '',
    'technical_goals' => '',
    'permissions' => '',
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
    'level' => 
    array (
      'dbtype' => 'varchar',
      'phptype' => 'string',
      'precision' => '10',
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
    'technical_requirements' => 
    array (
      'dbtype' => 'text',
      'phptype' => 'string',
      'null' => false,
      'default' => '',
    ),
    'safety_requirements' => 
    array (
      'dbtype' => 'text',
      'phptype' => 'string',
      'null' => false,
      'default' => '',
    ),
    'technical_goals' => 
    array (
      'dbtype' => 'text',
      'phptype' => 'string',
      'null' => false,
      'default' => '',
    ),
    'permissions' => 
    array (
      'dbtype' => 'text',
      'phptype' => 'string',
      'null' => false,
      'default' => '',
    ),
  ),
  'aggregates' => 
  array (
    'Members' => 
    array (
      'class' => 'mvMember',
      'foreign' => 'competency_level_id',
      'local' => 'id',
      'cardinality' => 'many',
      'owner' => 'local',
    ),
  ),
);
