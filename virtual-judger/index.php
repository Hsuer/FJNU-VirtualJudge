<?php
namespace Judger;
include 'utils.php';
include 'crawler.php';
include 'querier.php';
include 'submitter.php';
define('SCRIPT_ROOT',dirname(__FILE__).'/cookie/');

Utils\init();

$server = new \Swoole\Server("0.0.0.0", 9503, SWOOLE_BASE, SWOOLE_SOCK_TCP);
$server->set(array('task_worker_num' => 8));

$server->on('receive', function($server, $fd, $reactor_id, $data) {
    $json = json_decode($data, true);
    $task_id = $server->task($json);
});

$server->on('task', function ($server, $task_id, $reactor_id, $data) {
    switch($data['task']) {
        case "judge": 
            $status_id = intval($data['status_id']);
            $row = Submitter\Submitter($status_id);
            if($row != null) {
                Utils\setSubmitted($row);
                Querier\Querier($row);
            }
            break;
        case "crawl":
        case "recrawl":
            $origin_oj = $data['oj'];
            $origin_id = $data['id'];
            $problem = Crawler\Crawler($origin_oj, $origin_id);
            if($problem!=null) {
                if ($data['task'] == "crawl") {
                    Utils\setProblem($problem);
                }
                else {
                    Utils\resetProblem($problem);
                }
            }
            break;
        default:
            break;
    }
    $server->finish($data);
});

$server->on('finish', function ($server, $task_id, $data) {
});

$server->start();