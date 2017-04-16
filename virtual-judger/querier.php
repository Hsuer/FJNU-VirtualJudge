<?php
function Querier($row) {
    $created_at = strtotime($row['created_at']);
    while(true) {
        switch ($row['origin_oj']) {
            case 'HDU':
                $statusInfo = Querier_HDU($row);
                break;
            case 'FZU':
                $statusInfo = Querier_FZU($row);
                break;
            case 'POJ':
                $statusInfo = Querier_POJ($row);
                break;
            case 'FJNU':
                $statusInfo = Querier_FJNU($row);
                break;
            default:
                $statusInfo = null;
                break;
        }
        if(!empty($statusInfo['result'])) {
            $std_result = checkStatus($row['origin_oj'], $statusInfo['result']);
            setResult($row, $std_result, $statusInfo['result'], $statusInfo['time'], $statusInfo['memory']);
            if($std_result != NULL) {
                if($statusInfo['ceinfo'] != NULL) {
                    setCeinfo($row, $statusInfo['ceinfo']);
                }
                break;
            }
        }
        if(time() - $created_at > 1800) {
            break;
        }
        sleep(2);
    }
}

function Querier_HDU($row) {
    global $OJ, $JUDGE_RESULT;
    $run_id = $row['run_id'];
    $OJ_URL = $OJ['HDU'];
    $url = $OJ_URL.'status.php?first='.$run_id;
    $data = getContent($url);
    preg_match ("/<td>(\\d*?)MS<\/td><td>(\\d*?)K<\/td>/", $data, $info);
    preg_match ('/<\/td><td>[\\s\\S]*?<\/td><td>[\\s\\S]*?<\/td><td>([\\s\\S]*?)<\/td><td>[\\s\\S]*?<\/td><td>(\\d*?)MS<\/td><td>(\\d*?)K<\/td>/', $data, $result);
    $statusInfo['time'] = intval($info[1]);
    $statusInfo['memory'] = intval($info[2]);
    $statusInfo['result'] = trim(strip_tags($result[1]));
    if(checkCE('HDU', $statusInfo['result'])) {
        $url = $OJ_URL.'viewerror.php?rid='.$run_id;
        $cedata = getContent($url);
        preg_match ("/<pre>([\\s\\S]*?)<\/pre>/", $cedata, $ceinfo);
        $statusInfo['ceinfo'] = trim(strip_tags($ceinfo[0]));
    }
    else {
        $statusInfo['ceinfo'] = NULL;
    }
    return $statusInfo;
}

function Querier_POJ($row) {
    global $OJ;
    $run_id = $row['run_id'];
    $OJ_URL = $OJ['POJ'];
    $cookie = SCRIPT_ROOT.'POJ_'.$row['account'].'.tmp';
    getContent($url, $cookie);
    $url = $OJ_URL.'showsource?solution_id='.$run_id;
    $data = getContent($url, $cookie);
    preg_match ("/<b>Memory:<\/b> (\\d*?)K<\/td><td width=10px><\/td><td><b>Time:<\/b> (\\d*?)MS<\/td><\/tr>/", $data, $info);
    preg_match ('/<font color=.*?>(.*?)<\/font>/', $data, $result);
    $statusInfo['time'] = intval($info[2]);
    $statusInfo['memory'] = intval($info[1]);
    $statusInfo['result'] = $result[1];
    if(checkCE('POJ', $statusInfo['result'])) {
        $url = $OJ_URL.'showcompileinfo?solution_id='.$run_id;
        $cedata = getContent($url);
        preg_match ("/<pre>([\\s\\S]*?)<\/pre>/", $cedata, $ceinfo);
        $statusInfo['ceinfo'] = trim(strip_tags($ceinfo[0]));
    }
    else {
        $statusInfo['ceinfo'] = NULL;
    }
    return $statusInfo;
}

function Querier_FZU($row) {
    global $OJ;
    $run_id = $row['run_id'];
    $pid = $row['origin_id'];
    $OJ_URL = $OJ['FZU'];
    $acc = $row['account'];
    $url = $OJ_URL.'log.php?pid=' . $pid . '&user='. $acc;
    $data = getContent($url);
    //Get Time&Memory Limit
    $Pattern =  '/<td>'.$run_id.'<\/td>\\s*'.
                '<td>.*?<\/td>\\s*'.
                '<td><font color=\\s*.*?>(.*?)<\/font><\/td>\\s*'.
                '<td>.*?<\/td>\\s*'.
                '<td>.*?<\/td>\\s*'.
                '<td>(.*?)<\/td>\\s*'.
                '<td>(.*?)<\/td>\\s*'.
                '/';
    preg_match ($Pattern, $data, $info);
    if(strlen($info[2]) == 0) {
        $statusInfo['time'] = 0;
        $statusInfo['memory'] = 0;
    }
    else {
        $statusInfo['time'] = substr($info[2],0,-3);
        $statusInfo['memory'] = substr($info[3],0,-2);
    }
    $statusInfo['result'] = $info[1];
    if(checkCE('FZU', $statusInfo['result'])) {
        $url = $OJ_URL.'ce.php?sid='.$run_id;
        $cedata = getContent($url);
        preg_match ('/<font color=\"blue\" size=\"-1\">([\\s\\S]*?)<\/font>/', $cedata, $ceinfo);
        $statusInfo['ceinfo'] = trim(strip_tags($ceinfo[0]));
    }
    else {
        $statusInfo['ceinfo'] = NULL;
    }
    return $statusInfo;
}

function Querier_FJNU($row) {
    global $OJ;
    $run_id = $row['run_id'];
    $OJ_URL = $OJ['FJNU'];
    $cookie = SCRIPT_ROOT.'FJNU_'.$row['account'].'.tmp';
    $url = $OJ_URL.'showsource.php?id='.$run_id;
    $data = getContent($url, $cookie);
    if(strstr($data, 'I am sorry')) {
        $url = $OJ_URL.'login.php';
        $login['user_id'] = $row['account'];
        $login['password'] = $row['password'];
        getContent($url, $cookie, $login, 1);
        $url = $OJ_URL.'showsource.php?id='.$run_id;
        $data = getContent($url, $cookie);
    }
    //Get Time & Memory Limit
    $Pattern = "/Time:(\\d*?) ms/";
    preg_match ($Pattern, $data, $time);
    $Pattern = "/Memory:(\\d*?) kb/";
    preg_match ($Pattern, $data, $memory);
    $Pattern = '/Result: (.*?)\n/';
    preg_match ($Pattern, $data, $result);
    $statusInfo['time'] = intval($time[1]);
    $statusInfo['memory'] = intval($memory[1]);
    $statusInfo['result'] = $result[1];
    if(checkCE('FJNU', $statusInfo['result'])) {
        $url = $OJ_URL.'ceinfo.php?sid='.$run_id;
        $cedata = getContent($url, $cookie);
        preg_match ('/<pre class=\"brush:c;\" id=\'errtxt\' >([\\s\\S]*?)<\/pre>/', $cedata, $ceinfo);
        $statusInfo['ceinfo'] = trim(strip_tags($ceinfo[0]));
    }
    else {
        $statusInfo['ceinfo'] = NULL;
    }
    return $statusInfo;
}