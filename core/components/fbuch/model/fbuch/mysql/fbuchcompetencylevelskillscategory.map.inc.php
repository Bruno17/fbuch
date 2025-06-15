<?php
$xpdo_meta_map['fbuchCompetencyLevelSkillsCategory']= array (
  'package' => 'fbuch',
  'version' => '1.1',
  'table' => 'fbuch_competency_level_skills_categories',
  'extends' => 'xPDOSimpleObject',
  'tableMeta' => 
  array (
    'engine' => 'MyISAM',
  ),
  'fields' => 
  array (
    'name' => '',
    'description' => '',
    'parent' => 0,
    'pos' => 0,
    'is_skills_category' => 0,
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
    'description' => 
    array (
      'dbtype' => 'text',
      'phptype' => 'string',
      'null' => false,
      'default' => '',
    ),
    'parent' => 
    array (
      'dbtype' => 'int',
      'precision' => '10',
      'phptype' => 'integer',
      'null' => false,
      'default' => 0,
      'index' => 'index',
    ),
    'pos' => 
    array (
      'dbtype' => 'int',
      'precision' => '10',
      'phptype' => 'integer',
      'null' => false,
      'default' => 0,
      'index' => 'index',
    ),
    'is_skills_category' => 
    array (
      'dbtype' => 'int',
      'precision' => '1',
      'phptype' => 'integer',
      'null' => false,
      'default' => 0,
      'index' => 'index',
    ),
  ),
  'aggregates' => 
  array (
    'Parent' => 
    array (
      'class' => 'fbuchCompetencyLevelSkillsCategory',
      'foreign' => 'id',
      'local' => 'parent',
      'cardinality' => 'one',
      'owner' => 'foreign',
    ),
    'Skills' => 
    array (
      'class' => 'fbuchCompetencyLevelSkill',
      'foreign' => 'category_id',
      'local' => 'id',
      'cardinality' => 'many',
      'owner' => 'local',
    ),
  ),
);
