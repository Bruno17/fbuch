<?php

set_time_limit(0);

// Fetch params of CLI calls and merge with URL params (for universal usage)
if(isset($_SERVER['argc'])) {
    $argc = $_SERVER['argc'];
    if ($argc > 0) {
        for ($i=1; $i < $argc; $i++) {
            parse_str($argv[$i], $tmp);
            $_GET = array_merge($_GET, $tmp);
        }
    }
}

define('MODX_API_MODE', true);
require_once dirname(dirname(dirname(dirname(__FILE__)))).'/config.core.php';
require_once MODX_CORE_PATH.'model/modx/modx.class.php';
$modx = new modX();
$modx->initialize('mgr');
$modx->getService('error', 'error.modError', '', '');

// If set - connector script may only be continued if the correct security key is provided by cron (@param sid)
$sid = isset($_GET['sid']) ? $_GET['sid'] : '';
$securityKey = $modx->getOption('scheduler.cron_security_key', null, '');
if ($sid != $securityKey) {
    exit('[Scheduler] run2.php - Missing or wrong authentification!');
}

$corePath = $modx->getOption('scheduler.core_path',null,$modx->getOption('core_path').'components/scheduler/');
require_once $corePath.'model/scheduler/scheduler.class.php';
$scheduler = new Scheduler($modx);

$limit = (integer) $modx->getOption('scheduler.tasks_per_run', null, 1);
$limit = 50;

/**
 * Get the tasks we need to run right now.
 */
$c = $modx->newQuery('sTaskRun');
$c->where(array(
    'status' => sTaskRun::STATUS_SCHEDULED,
    'AND:timing:<=' => time(),
));
$c->sortby('timing ASC, id', 'ASC');
$c->limit($limit);

/**
 * @var sTaskRun $taskRun
 */
foreach ($modx->getIterator('sTaskRun', $c) as $taskRun) {
    $task = $taskRun->getOne('Task');
    if (!empty($task) && is_object($task) && $task instanceof sTask) {
        $task->run($taskRun);
    }
}

$modx->log(modX::LOG_LEVEL_INFO, '[Scheduler] Done!');


