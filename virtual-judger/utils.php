<?php
namespace Judger\Utils;
include 'config.php';
include 'account.php';
include 'conn.php';

use Judger\Conn;

function init() {
    if(!is_dir("./cookie")) {
        mkdir("./cookie", 0777, true);
        chmod("./cookie", 0777);
    }
}

function checkStatus($OJ, $str) {
    $str = trim($str);
    global $JUDGE_RESULT;
    $std_result_list = $JUDGE_RESULT[$OJ];
    foreach ($std_result_list as $key => $value) {
        if(strstr($str, $value)) return $key;
    }
    return false;
}

function checkCE($OJ, $str) {
    $str = trim($str);
    global $JUDGE_RESULT;
    if(strstr($str, $JUDGE_RESULT[$OJ]['CE'])) {
        return true;
    }
    else {
        return false;
    }
}

function getAccount($OJ) {
    global $account;
    $total_num = count($account[$OJ]);
    $rand = rand(0, $total_num - 1);
    return $account[$OJ][$rand];
}

function getStatus($status_id) {
    $conn = Conn\ConnectMysqli::getIntance();
    $sql = "select
            status.id,status.user_id,status.language,status.result,status.updated_at as created_at,
            status.problem_id,status.contest_id,
            problems.origin_oj,problems.origin_id,solutions.code 
            from status 
            left join problems on status.problem_id = problems.id 
            left join solutions on status.id = solutions.id 
            where status.id = '".$conn->real_escape_string($status_id)."'";
    $result = $conn->query($sql);
    $row = $result->fetch_array(MYSQLI_ASSOC);
    $result->free();
    return $row;
}

function setSubmitted($row) {
    $conn = Conn\ConnectMysqli::getIntance();
    $status_id = $row['id'];
    $sql = "update status set result = 'Submitted' where id = '".$conn->real_escape_string($status_id)."'";
    $conn->query($sql);
    $sql = "update problems set submit_num = submit_num + 1 where id = '".$conn->real_escape_string($row['problem_id'])."'";
    $conn->query($sql);
    $sql = "update users set submit = submit + 1 where id = '".$conn->real_escape_string($row['user_id'])."'";
    $conn->query($sql);
    if($row['contest_id'] != null) {
        $sql = "update contests_problems set submit_num = submit_num + 1 where contest_id = '".$conn->real_escape_string($row['contest_id'])."' and problem_id = '".$conn->real_escape_string($row['problem_id'])."'";
        $conn->query($sql);
    }
}

function setCeinfo($row, $ceinfo) {
    $conn = Conn\ConnectMysqli::getIntance();
    $status_id = $row['id'];
    $sql = "insert into compile_info(status_id, info) values ('".$conn->real_escape_string($status_id)."','".$conn->real_escape_string($ceinfo)."')";
    if(!$conn->query($sql)) {
        printf("Errormessage: %s\n", $conn->error);
    }
}

function setResult($row, $std_result, $result, $time, $memory) {
    $conn = Conn\ConnectMysqli::getIntance();
    $status_id = $row['id'];
    $user_id = $row['user_id'];
    $problem_id = $row['problem_id'];
    global $JUDGE_RESULT;
    $std_result_list = $JUDGE_RESULT[$row['origin_oj']];
    $sql = "update status set 
            result='".$conn->real_escape_string($result)."',
            time='".$conn->real_escape_string($time)."',
            memory='".$conn->real_escape_string($memory)."' 
            where id = '".$conn->real_escape_string($status_id)."'";
    $result = $conn->query($sql);
    if($std_result != null) {
        switch ($std_result) {
            case 'AC':
                $sql = "update users set ac = ac + 1 where id = '".$conn->real_escape_string($user_id)."'";
                $conn->query($sql);

                if($row['contest_id'] != null) {
                    $sql = "update contests_problems set ac_num = ac_num + 1 where contest_id = '".$conn->real_escape_string($row['contest_id'])."' and problem_id = '".$conn->real_escape_string($row['problem_id'])."'";
                    $conn->query($sql);
                }

                $sql = "select count(*) from status where user_id = '".$conn->real_escape_string($user_id)."' 
                        and result = '".$conn->real_escape_string($std_result_list['AC'])."' 
                        and problem_id like '".$conn->real_escape_string($problem_id)."'";
                $result = $conn->query($sql);
                $ret = $result->fetch_array(MYSQLI_NUM);
                if($ret[0] == 1) {
                    $sql = "update users set solve = solve + 1 where id = '".$conn->real_escape_string($user_id)."'";
                    $conn->query($sql);
                }
                break;
            case 'WA':
            case 'CE':
            case 'PE':
            case 'RE':
            case 'TLE':
            case 'MLE':
            case 'OLE':
                $new_result = strtolower($std_result);
                $sql = "update users set '$new_result' = '$new_result' + 1 where id = '".$conn->real_escape_string($user_id)."'";
                $conn->query($sql);
                break;
            default:
                $sql = "update users set other = other + 1 where id = '".$conn->real_escape_string($user_id)."'";
                $conn->query($sql);
                break;
        }
    }
}

function setProblem($problem) {
    $conn = Conn\ConnectMysqli::getIntance();
    $sql = "insert into problems(title, origin_oj, origin_id, time, memory, special_judge, description, input, output, sample_input, sample_output, hint, author, source, available, ac_num, submit_num, created_at, updated_at) 
        values (
        '".$conn->real_escape_string($problem['title'])."',
        '".$conn->real_escape_string($problem['origin_oj'])."',
        '".$conn->real_escape_string($problem['origin_id'])."',
        '".$conn->real_escape_string($problem['time'])."',
        '".$conn->real_escape_string($problem['memory'])."',
        '".$conn->real_escape_string($problem['special_judge'])."',
        '".$conn->real_escape_string($problem['description'])."',
        '".$conn->real_escape_string($problem['input'])."',
        '".$conn->real_escape_string($problem['output'])."',
        '".$conn->real_escape_string($problem['sample_input'])."',
        '".$conn->real_escape_string($problem['sample_output'])."',
        '".$conn->real_escape_string($problem['hint'])."',
        '".$conn->real_escape_string($problem['author'])."',
        '".$conn->real_escape_string($problem['source'])."',
        '".$conn->real_escape_string(0)."',
        '".$conn->real_escape_string(0)."',
        '".$conn->real_escape_string(0)."',
        '".$conn->real_escape_string(date('Y-m-d H:i:s', time()))."',
        '".$conn->real_escape_string(date('Y-m-d H:i:s', time()))."'
    )";
    if(!$conn->query($sql)) {
        printf("Errormessage: %s\n", $conn->error);
    }
}

function resetProblem($problem) {
    $conn = Conn\ConnectMysqli::getIntance();
    $origin_oj = $problem['origin_oj'];
    $origin_id = $problem['origin_id'];
    $sql = "select id from problems where origin_oj like '$origin_oj' and origin_id like '$origin_id'";
    $result = $conn->query($sql);
    $row = $result->fetch_array(MYSQLI_ASSOC);
    $result->free();
    if(isset($row)) {
        $id = $row['id'];
        $sql = "update problems set
                title = '".$conn->real_escape_string($problem['title'])."', 
                time = '".$conn->real_escape_string($problem['time'])."', 
                memory = '".$conn->real_escape_string($problem['memory'])."', 
                special_judge = '".$conn->real_escape_string($problem['special_judge'])."', 
                description = '".$conn->real_escape_string($problem['description'])."', 
                input = '".$conn->real_escape_string($problem['input'])."',
                output = '".$conn->real_escape_string($problem['output'])."', 
                sample_input = '".$conn->real_escape_string($problem['sample_input'])."', 
                sample_output = '".$conn->real_escape_string($problem['sample_output'])."', 
                hint = '".$conn->real_escape_string($problem['hint'])."', 
                author = '".$conn->real_escape_string($problem['author'])."', 
                source = '".$conn->real_escape_string($problem['source'])."', 
                updated_at = '".$conn->real_escape_string(date('Y-m-d H:i:s', time()))."' 
                where id = '".$conn->real_escape_string($id)."'";
        if(!$conn->query($sql)) {
            printf("Errormessage: %s\n", $conn->error);
        }
    }
}

function getContent($url, $cookie = '', $post = '', $returnCookie = 0) {
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_HEADER, 0);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($curl, CURLOPT_TIMEOUT, 60);
    curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 30);
    if($returnCookie) {
        curl_setopt($curl, CURLOPT_COOKIEJAR, $cookie);
    }
    else {
        if($cookie) {
            curl_setopt($curl, CURLOPT_COOKIEFILE, $cookie);
        }
    }
    if($post) {
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($post));
    }   
    $data = curl_exec($curl);
    curl_close($curl); 
    return $data;
}
