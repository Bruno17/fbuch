<?php
$xpdo_meta_map['fbuchDate']= array (
  'package' => 'fbuch',
  'version' => '1.1',
  'table' => 'fbuch_dates',
  'extends' => 'xPDOSimpleObject',
  'tableMeta' => 
  array (
    'engine' => 'MyISAM',
  ),
  'fields' => 
  array (
    'date' => NULL,
    'date_end' => NULL,
    'title' => '',
    'type' => '',
    'description' => '',
    'max_reservations' => 0,
    'autoduplicate' => 0,
    'autoduplicated' => 0,
    'autoduplicate_invited' => 0,
    'autoduplicate_names' => 0,
    'riot_room_id' => '',
    'matrix_space' => 0,
    'parent' => 0,
    'matrix_members_kicked' => 0,
    'last_matrix_start_token' => '',
    'start_time' => '',
    'end_time' => '',
    'instructor_id' => 0,
    'instructor_member_id' => 0,
    'mailinglist_id' => 0,
    'locked' => 0,
    'lock_password' => '',
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
    'title' => 
    array (
      'dbtype' => 'varchar',
      'phptype' => 'string',
      'precision' => '255',
      'null' => false,
      'default' => '',
    ),
    'type' => 
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
    'max_reservations' => 
    array (
      'dbtype' => 'int',
      'precision' => '10',
      'phptype' => 'integer',
      'null' => false,
      'default' => 0,
    ),
    'autoduplicate' => 
    array (
      'dbtype' => 'tinyint',
      'precision' => '1',
      'attributes' => 'unsigned',
      'phptype' => 'integer',
      'null' => false,
      'default' => 0,
    ),
    'autoduplicated' => 
    array (
      'dbtype' => 'tinyint',
      'precision' => '1',
      'attributes' => 'unsigned',
      'phptype' => 'integer',
      'null' => false,
      'default' => 0,
    ),
    'autoduplicate_invited' => 
    array (
      'dbtype' => 'tinyint',
      'precision' => '1',
      'attributes' => 'unsigned',
      'phptype' => 'integer',
      'null' => false,
      'default' => 0,
    ),
    'autoduplicate_names' => 
    array (
      'dbtype' => 'tinyint',
      'precision' => '1',
      'attributes' => 'unsigned',
      'phptype' => 'integer',
      'null' => false,
      'default' => 0,
    ),
    'riot_room_id' => 
    array (
      'dbtype' => 'text',
      'phptype' => 'string',
      'null' => false,
      'default' => '',
    ),
    'matrix_space' => 
    array (
      'dbtype' => 'int',
      'precision' => '10',
      'phptype' => 'integer',
      'null' => false,
      'default' => 0,
      'index' => 'index',
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
    'matrix_members_kicked' => 
    array (
      'dbtype' => 'tinyint',
      'precision' => '1',
      'attributes' => 'unsigned',
      'phptype' => 'integer',
      'null' => false,
      'default' => 0,
    ),
    'last_matrix_start_token' => 
    array (
      'dbtype' => 'text',
      'phptype' => 'string',
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
    'instructor_id' => 
    array (
      'dbtype' => 'int',
      'precision' => '10',
      'phptype' => 'integer',
      'null' => false,
      'default' => 0,
    ),
    'instructor_member_id' => 
    array (
      'dbtype' => 'int',
      'precision' => '10',
      'phptype' => 'integer',
      'null' => false,
      'default' => 0,
    ),
    'mailinglist_id' => 
    array (
      'dbtype' => 'int',
      'precision' => '10',
      'phptype' => 'integer',
      'null' => false,
      'default' => 0,
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
    'lock_password' => 
    array (
      'dbtype' => 'varchar',
      'phptype' => 'string',
      'precision' => '50',
      'null' => false,
      'default' => '',
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
      'class' => 'fbuchDateNames',
      'local' => 'id',
      'foreign' => 'date_id',
      'cardinality' => 'many',
      'owner' => 'local',
    ),
    'Invited' => 
    array (
      'class' => 'fbuchDateInvited',
      'local' => 'id',
      'foreign' => 'date_id',
      'cardinality' => 'many',
      'owner' => 'local',
    ),
    'Comments' => 
    array (
      'class' => 'fbuchDateComment',
      'local' => 'id',
      'foreign' => 'date_id',
      'cardinality' => 'many',
      'owner' => 'local',
    ),
    'Mailinglist' => 
    array (
      'class' => 'fbuchMailinglist',
      'local' => 'mailinglist_id',
      'foreign' => 'id',
      'cardinality' => 'one',
      'owner' => 'foreign',
    ),
  ),
  'aggregates' => 
  array (
    'Instructor' => 
    array (
      'class' => 'mvMember',
      'local' => 'instructor_member_id',
      'foreign' => 'id',
      'cardinality' => 'one',
      'owner' => 'foreign',
    ),
    'Type' => 
    array (
      'class' => 'fbuchDateType',
      'local' => 'type',
      'foreign' => 'name',
      'cardinality' => 'one',
      'owner' => 'foreign',
    ),
  ),
);
