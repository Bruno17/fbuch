<?php
$group_id = $modx->getOption('object_id', $_REQUEST, '');
$allowed = $modx->getOption('ngm_allowed', $_REQUEST, '');
$where = '';

$allowed = $allowed == 'all' ? 0 : $allowed;

if (!empty($allowed)) {
    $c = $modx->newQuery('fbuchBootsNutzergruppenMembers');
    $c->where(array('group_id' => $group_id));

    if ($collection = $modx->getCollection('fbuchBootsNutzergruppenMembers', $c)) {
        foreach ($collection as $object) {
            $ids[] = $object->get('member_id');
        }
        $where = array();
        $where['id:IN'] = $ids;
        $where = json_encode($where);
    } else {
        $where = array();
        $where['id'] = 0;
        $where = json_encode($where);        
    }
}

return $where;