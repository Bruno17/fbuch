<?php
$xpdo_meta_map['mvMemberSkill']= array (
  'package' => 'fbuch',
  'version' => '1.1',
  'table' => 'mv_member_skills',
  'extends' => 'xPDOSimpleObject',
  'tableMeta' => 
  array (
    'engine' => 'MyISAM',
  ),
  'fields' => 
  array (
    'note' => '',
    'member_id' => 0,
    'skill_id' => 0,
    'grade' => 0,
    'createdby' => 0,
    'createdon' => NULL,
  ),
  'fieldMeta' => 
  array (
    'note' => 
    array (
      'dbtype' => 'text',
      'phptype' => 'string',
      'null' => false,
      'default' => '',
    ),
    'member_id' => 
    array (
      'dbtype' => 'int',
      'precision' => '10',
      'phptype' => 'integer',
      'null' => false,
      'default' => 0,
      'index' => 'index',
    ),
    'skill_id' => 
    array (
      'dbtype' => 'int',
      'precision' => '10',
      'phptype' => 'integer',
      'null' => false,
      'default' => 0,
      'index' => 'index',
    ),
    'grade' => 
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
  ),
  'aggregates' => 
  array (
    'Member' => 
    array (
      'class' => 'mvMember',
      'foreign' => 'id',
      'local' => 'member_id',
      'cardinality' => 'one',
      'owner' => 'foreign',
    ),
    'CreatorMember' => 
    array (
      'class' => 'mvMember',
      'foreign' => 'modx_user_id',
      'local' => 'createdby',
      'cardinality' => 'one',
      'owner' => 'foreign',
    ),
    'CreatorProfile' => 
    array (
      'class' => 'modUserProfile',
      'foreign' => 'internalKey',
      'local' => 'createdby',
      'cardinality' => 'one',
      'owner' => 'foreign',
    ),
    'Skill' => 
    array (
      'class' => 'fbuchCompetencyLevelSkill',
      'foreign' => 'id',
      'local' => 'skill_id',
      'cardinality' => 'one',
      'owner' => 'foreign',
    ),
  ),
);
