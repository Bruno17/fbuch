<?php
$filter_id = $modx->getOption('filtermailmember', $_REQUEST, '');
$member_id = $modx->getOption('member_id', $_REQUEST, '');
$singlemail = $modx->getOption('singlemail', $_REQUEST, '');

$filter_id = $modx->getOption('filter_id', $scriptProperties, $filter_id);
$member_id = $modx->getOption('member_id', $scriptProperties, $member_id);
$singlemail = $modx->getOption('singlemail', $scriptProperties, $singlemail);

$classname = 'mvMemberFilter';

if ($filter_id != 'all' && !empty($filter_id) && $object = $modx->getObject($classname, $filter_id)) {
    $where = $modx->fromJson($object->get('where'));

    //membergroups im Filter gefunden
    if (isset($where['membergroups'])) {
        $membergroups = explode(',', $where['membergroups']);
        unset($where['membergroups']);
        $classname = 'mvMemberRoleLink';
        $c = $modx->newQuery($classname);
        $jalias = 'Role';
        $joinclass = 'mvRole';
        $c->leftjoin($joinclass, $jalias);
        $c->where(array('Role.group_id:IN' => $membergroups));
        $c->groupby('member_id');
        $member_ids = array();
        if ($collection = $modx->getIterator($classname, $c)) {
            foreach ($collection as $object) {
                 $member_ids[] = $object->get('member_id');
            }
        }
        if (count($member_ids) > 0) {
            $where['id:IN'] = $member_ids;
        }

    }
  
    return $modx->toJson($where);
}

if (!empty($singlemail) && !empty($member_id)){
    $where = array();
    $where['id'] = $member_id;
    return $modx->toJson($where);
}


return '';