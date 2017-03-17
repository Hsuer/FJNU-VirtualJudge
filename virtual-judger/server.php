<?php
require_once("config.php");
require_once("function.php");
require_once("account.php");
require_once("submitter.php");
require_once("querier.php");
require_once("crawler.php");
require_once("normalize_url.php");
error_reporting(0);
_mkdir();
define('SCRIPT_ROOT',dirname(__FILE__).'/cookie/');
$conn = null;
connect();

$serv = new Swoole\Server("0.0.0.0", 9503, SWOOLE_SOCK_TCP);
$serv->set(array('task_worker_num' => 8));
$serv->on('Receive', function($serv, $fd, $from_id, $data) {
    $json = json_decode($data, true);
    $task_id = $serv->task($json);
});
$serv->on('Task', function ($serv, $task_id, $from_id, $data) {
    if($data['task'] == "judge") {
        checkConnAlive();
        $status_id = intval($data['status_id']);
        $row = Submitter($status_id);
        if($row != null) {
            setSubmitted($row);
            Querier($row);
        }
    }
    else if($data['task'] == "crawl") {
        checkConnAlive();
        $origin_oj = $data['oj'];
        $origin_id = $data['id'];
        $problem = Crawler($origin_oj, $origin_id);
        if($problem!=null) {
            setProblem($problem);
        }
    }
    else if($data['task'] == "recrawl") {
        checkConnAlive();
        $origin_oj = $data['oj'];
        $origin_id = $data['id'];
        $problem = Crawler($origin_oj, $origin_id);
        if($problem!=null) {
            resetProblem($problem);
        }
    }
    $serv->finish("$data -> OK");
});
$serv->on('Finish', function ($serv, $task_id, $data) {
    // echo "AsyncTask[$task_id] Finish: $data".PHP_EOL;
});
$serv->start();