<?php 
$fbuchCorePath = realpath($modx->getOption('fbuch.core_path', null, $modx->getOption('core_path') . 'components/fbuch')) . '/';
$fbuch = $modx->getService('fbuch', 'Fbuch', $fbuchCorePath . 'model/fbuch/');
$result = $fbuch->getFormValues($hook, $scriptProperties);
$values = $hook->getValues();
$offset = (int) $modx->getOption('fbuch.endtime_offset',null,0);
if ($offset > 0 && 0 == $values['object_id']) {
  $currentTimeTs = time();
  // format strings see https://www.php.net/manual/de/datetime.format.php
  $values['date'] = date('d.m.Y', $currentTimeTs);
  $values['date_end'] = $values['date'];
  $values['start_time'] = date('H:i', $currentTimeTs);
  $values['end_time'] = date('H:i', $currentTimeTs + $offset * 60);
  $hook->setValues($values);
}

return $result;