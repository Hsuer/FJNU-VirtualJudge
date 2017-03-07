<?php
function Submitter($status_id) {
    $row = getStatus($status_id);
    $created_at = strtotime($row['created_at']);
    $acc_rand = getAccount($row['origin_oj']);
    $row['account'] = trim($acc_rand['account']);
    $row['password'] = trim($acc_rand['password']);
    while(true) {
        switch ($row['origin_oj']) {
            case 'HDU':
                $run_id = Submitter_HDU($row);
                break;
            case 'FZU':
                $run_id = Submitter_FZU($row);
                break;
            case 'POJ':
                $run_id = Submitter_POJ($row);
                break;
            case 'HUST':
                $run_id = Submitter_HUST($row);
                break;
            case 'FJNU':
                $run_id = Submitter_FJNU($row);
                break;
            default:
                $run_id = 0;
                break;
        }
        var_dump($run_id);
        if($run_id != 0) {
            $row['run_id'] = $run_id;
            return $row;
        }
        else {
            sleep(2);
        }
        if(time() - $created_at > 1800) {
            return null;
        }
    }
}

function Submitter_HDU($post){  
    global $OJ;
    $OJ_URL = $OJ['HDU'];
    $cookie = SCRIPT_ROOT.'HDU_'.$post['account'].'.tmp';
    //Check if not Login, then login
    $data = getContent($OJ_URL, $cookie);
    if(!strstr($data, 'href="/userloginex.php?action=logout"')) {
        $url = $OJ_URL.'userloginex.php?action=login';
        $login['username'] = $post['account'];
        $login['userpass'] = $post['password'];
        getContent($url, $cookie, $login, 1);
    }
    //Submit
    $url = $OJ_URL.'submit.php?action=submit';
    $submit['check'] = 0;
    $submit['language'] = $post['language'];
    $submit['problemid'] = $post['origin_id'];
    $submit['usercode'] = $post['code'];
    getContent($url, $cookie, $submit);
    //Get MAX RUNID
    $url = $OJ_URL.'status.php?user='.$post['account'].'&pid='.$post['origin_id'];
    $data = getContent($url);
    $Pattern = "/<td height=22px>([\s\S]*?)<\/td>/";
    preg_match ($Pattern, $data, $RunID);
    $MaxRunID = intval($RunID[1]);
    return $MaxRunID;
}

function Submitter_POJ($post){
    global $OJ;
    $OJ_URL = $OJ['POJ'];
    $cookie = SCRIPT_ROOT.'POJ_'.$post['account'].'.tmp';
    //Check if not Login, then login
    $data = getContent($OJ_URL, $cookie);
    if(!strstr($data, '>Log Out</a>')) {
        $url = $OJ_URL.'login';
        $login['user_id1'] = $post['account'];
        $login['password1'] = $post['password'];
        $login['B1'] = 'login';
        $login['url'] = '.';
        getContent($url, $cookie, $login, 1);
    }
    //Submit
    $url = $OJ_URL.'submit';
    $submit['language'] = $post['language'];
    $submit['problem_id'] = $post['origin_id'];
    $submit['source'] = $post['code'];
    $submit['encoded'] = 0;
    getContent($url, $cookie, $submit);
    //Get MAX RUNID
    $url = $OJ_URL.'status?user_id='.$post['account'].'&problem_id='.$post['origin_id'];
    $data = getContent($url);
    $Pattern = "/<tr align=center><td>([\s\S]*?)<\/td><\/tr>/";
    preg_match ($Pattern, $data, $RunID);
    $MaxRunID = intval($RunID[1]);
    return $MaxRunID;
}

function Submitter_FZU($post){
    global $OJ;
    $OJ_URL = $OJ['FZU'];
    $cookie = SCRIPT_ROOT.'FZU_'.$post['account'].'.tmp';
    //Check if not Login, then login
    $data = getContent($OJ_URL, $cookie);
    if(!strstr($data, '>Logout</a>')) {
        $url = $OJ_URL.'login.php?act=1';
        $login['uname'] = $post['account'];
        $login['upassword'] = $post['password'];
        getContent($url, $cookie, $login, 1);
    }
    //Submit
    $url = $OJ_URL.'submit.php?act=5';
    $submit['lang'] = $post['language'];
    $submit['pid'] = $post['origin_id'];
    $submit['code'] = $post['code'];
    getContent($url, $cookie, $submit);
    //Get MAX RUNID
    $url = $OJ_URL.'log.php?user='.$post['account'].'&pid='.$post['origin_id'];
    $data = getContent($url);
    $Pattern = '/" >    <td>(\\d*)<\/td>/';
    preg_match ($Pattern, $data, $RunID);
    $MaxRunID = intval($RunID[1]);
    return $MaxRunID;
}

function Submitter_HUST($post){
    global $OJ;
    $OJ_URL = $OJ['HUST'];
    $cookie = SCRIPT_ROOT.'HUST_'.$post['account'].'.tmp';
    //Check if not Login, then login
    $data = getContent($OJ_URL, $cookie);
    if(!strstr($data, '/logout')) {
        $url = $OJ_URL.'user/login';
        $login['username'] = $post['account'];
        $login['pwd'] = $post['password'];
        $login['code'] = "";
        getContent($url, $cookie, $login, 1);
    }
    //Submit
    $url = $OJ_URL.'problem/submit';
    $submit['language'] = $post['language'];
    $submit['pid'] = $post['origin_id'];
    $submit['source'] = $post['code'];
    getContent($url, $cookie, $submit);
    //Get MAX RUNID
    $url = $OJ_URL.'status?uid='.$post['account'].'&pid='.$post['origin_id'];
    $data = getContent($url, $cookie);
    $Pattern = '/solution\/source\/(\\d*)/';
    preg_match ($Pattern, $data, $RunID);
    $MaxRunID = intval($RunID[1]);
    return $MaxRunID;
}

function Submitter_FJNU($post){
    global $OJ;
    $OJ_URL = $OJ['FJNU'];
    $cookie = SCRIPT_ROOT.'FJNU_'.$post['account'].'.tmp';
    //Check if not Login, then login
    $data = getContent($OJ_URL, $cookie);
    //if(strstr($data, 'Please Login')) {
        $url = $OJ_URL.'login.php';
        $login['user_id'] = 'nextver';
        $login['password'] = '123456';
        getContent($url, $cookie, $login, 1);
   // }
    //Submit
    $url = $OJ_URL.'submit.php';
    //$submit['check'] = 0;
    $submit['language'] = $post['language'];
    $submit['id'] = $post['origin_id'];
    $submit['source'] = $post['code'];
    getContent($url, $cookie, $submit);
    //Get MAX RUNID
    $url = $OJ_URL.'status.php?user_id='.$login['user_id'].'&problem_id='.$submit['id'];
    $data = getContent($url);
    $Pattern = "/<\/td><td>\t([\s\S]*?)<\/td><td>/";
    preg_match ($Pattern, $data, $RunID);
    $MaxRunID = intval($RunID[1]);
    return $MaxRunID;
}