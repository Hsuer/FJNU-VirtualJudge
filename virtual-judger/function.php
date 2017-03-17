<?php
function connect() {
    global $conn, $DB_CONN;
    $conn = new mysqli($DB_CONN['server'], $DB_CONN['username'], $DB_CONN['password'], $DB_CONN['database']);
    if ($conn->connect_error) {
        die("Connect failed: " . $conn->connect_error);
    } 
    $conn->set_charset("utf8");
}

function ConnClose() {
    global $conn;
    $conn->close();
}

function checkConnAlive() {
    global $conn;
    if (!$conn->ping()) {
        connect();
        printf ("Connection is reconnected!\n");
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

function getStatus($status_id) {
    global $conn;
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

function getAccount($OJ) {
    global $account;
    $total_num = count($account[$OJ]);
    $rand = rand(0, $total_num - 1);
    return $account[$OJ][$rand];
}

function setSubmitted($row) {
    global $conn;
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
    global $conn;
    $status_id = $row['id'];
    $sql = "insert into compile_info(status_id, info) values ('".$conn->real_escape_string($status_id)."','".$conn->real_escape_string($ceinfo)."')";
    if(!$conn->query($sql)) {
        printf("Errormessage: %s\n", $conn->error);
    }
}

function setResult($row, $std_result, $result, $time, $memory) {
    $status_id = $row['id'];
    $user_id = $row['user_id'];
    $problem_id = $row['problem_id'];
    global $conn, $JUDGE_RESULT;
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
                $sql = "update users set wa = wa + 1 where id = '".$conn->real_escape_string($user_id)."'";
                $conn->query($sql);
                break;
            case 'CE':
                $sql = "update users set ce = ce + 1 where id = '".$conn->real_escape_string($user_id)."'";
                $conn->query($sql);
                break;
            case 'PE':
                $sql = "update users set pe = pe + 1 where id = '".$conn->real_escape_string($user_id)."'";
                $conn->query($sql);
                break;
            case 'RE':
                $sql = "update users set re = re + 1 where id = '".$conn->real_escape_string($user_id)."'";
                $conn->query($sql);
                break;
            case 'TLE':
                $sql = "update users set tle = tle + 1 where id = '".$conn->real_escape_string($user_id)."'";
                $conn->query($sql);
                break;
            case 'MLE':
                $sql = "update users set mle = mle + 1 where id = '".$conn->real_escape_string($user_id)."'";
                $conn->query($sql);
                break;
            case 'OLE':
                $sql = "update users set ole = ole + 1 where id = '".$conn->real_escape_string($user_id)."'";
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
    global $conn;
    $title = $problem['title'];
    $origin_oj = $problem['origin_oj'];
    $origin_id = $problem['origin_id'];
    $time = $problem['time'];
    $memory = $problem['memory'];
    $special_judge = $problem['special_judge'];
    $description = $problem['description'];
    $input = $problem['input'];
    $output = $problem['output'];
    $sample_input = $problem['sample_input'];
    $sample_output = $problem['sample_output'];
    $hint = $problem['hint'];
    $author = $problem['author'];
    $source = $problem['source'];
    $available = 0;
    $ac_num = 0;
    $submit_num = 0;
    $created_at = date('Y-m-d H:i:s', time());
    $updated_at = date('Y-m-d H:i:s', time());
    $sql = "insert into problems(title, origin_oj, origin_id, time, memory, special_judge, description, input, output, sample_input, sample_output, hint, author, source, available, ac_num, submit_num, created_at, updated_at) 
        values (
        '".$conn->real_escape_string($title)."',
        '".$conn->real_escape_string($origin_oj)."',
        '".$conn->real_escape_string($origin_id)."',
        '".$conn->real_escape_string($time)."',
        '".$conn->real_escape_string($memory)."',
        '".$conn->real_escape_string($special_judge)."',
        '".$conn->real_escape_string($description)."',
        '".$conn->real_escape_string($input)."',
        '".$conn->real_escape_string($output)."',
        '".$conn->real_escape_string($sample_input)."',
        '".$conn->real_escape_string($sample_output)."',
        '".$conn->real_escape_string($hint)."',
        '".$conn->real_escape_string($author)."',
        '".$conn->real_escape_string($source)."',
        '".$conn->real_escape_string($available)."',
        '".$conn->real_escape_string($ac_num)."',
        '".$conn->real_escape_string($submit_num)."',
        '".$conn->real_escape_string($created_at)."',
        '".$conn->real_escape_string($updated_at)."'
    )";
    if(!$conn->query($sql)) {
        printf("Errormessage: %s\n", $conn->error);
    }
}

function resetProblem($problem) {
    global $conn;
    $title = $problem['title'];
    $origin_oj = $problem['origin_oj'];
    $origin_id = $problem['origin_id'];
    $time = $problem['time'];
    $memory = $problem['memory'];
    $special_judge = $problem['special_judge'];
    $description = $problem['description'];
    $input = $problem['input'];
    $output = $problem['output'];
    $sample_input = $problem['sample_input'];
    $sample_output = $problem['sample_output'];
    $hint = $problem['hint'];
    $author = $problem['author'];
    $source = $problem['source'];
    $updated_at = date('Y-m-d H:i:s', time());
    $sql = "select id from problems where origin_oj like '$origin_oj' and origin_id like '$origin_id'";
    $result = $conn->query($sql);
    $row = $result->fetch_array(MYSQLI_ASSOC);
    $result->free();
    if(isset($row)) {
        $id = $row['id'];
        $sql = "update problems set
                title = '".$conn->real_escape_string($title)."', 
                time = '".$conn->real_escape_string($time)."', 
                memory = '".$conn->real_escape_string($memory)."', 
                special_judge = '".$conn->real_escape_string($special_judge)."', 
                description = '".$conn->real_escape_string($description)."', 
                input = '".$conn->real_escape_string($input)."',
                output = '".$conn->real_escape_string($output)."', 
                sample_input = '".$conn->real_escape_string($sample_input)."', 
                sample_output = '".$conn->real_escape_string($sample_output)."', 
                hint = '".$conn->real_escape_string($hint)."', 
                author = '".$conn->real_escape_string($author)."', 
                source = '".$conn->real_escape_string($source)."', 
                updated_at = '".$conn->real_escape_string($updated_at)."' 
                where id = '".$conn->real_escape_string($id)."'";
        if(!$conn->query($sql)) {
            printf("Errormessage: %s\n", $conn->error);
        }
    }
}

function _mkdir() {
    if(!is_dir("./cookie")) {
        mkdir("./cookie");
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