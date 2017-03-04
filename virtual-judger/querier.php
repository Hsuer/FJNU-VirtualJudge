<?php
function Querier($row) {
	$created_at = strtotime($row['created_at']);
	while(true) {
		switch ($row['origin_oj']) {
			case 'HDU':
				$status = Querier_HDU($row);
				break;
			case 'FZU':
				$status = Querier_FZU($row);
				break;
			case 'POJ':
				$status = Querier_POJ($row);
				break;
			case 'HUST':
				$status =  Querier_HUST($row);
				break;
			case 'FJNU':
				$status =  Querier_FJNU($row);
				break;
			default:
				$status = null;
				break;
		}
		// echo $status_id . ":" . $status['result'] . PHP_EOL;
		if(!empty($status['result'])) {
			$std_result = checkStatus($row['origin_oj'], $status['result']);
			setResult($row, $std_result, $status['result'], $status['time'], $status['memory']);
			if($std_result != NULL) {
				break;
			}
		}
		if(time() - $created_at > 1800) {
			setJudgeError($row['id']);
			break;
		}
		sleep(2);
	}
}

function Querier_HDU($row) {
	global $OJ;
	$run_id = $row['run_id'];
	$OJ_URL = $OJ['HDU'];
	$url = $OJ_URL.'status.php?first='.$run_id;
    $data = GetContent($url);
    //Get Time & Memory Limit
	$Pattern = "/<td>(\\d*?)MS<\/td><td>(\\d*?)K<\/td>/";
	preg_match ($Pattern, $data, $Info);
	//Get Judge Result
	$Pattern = '/<\/td><td>[\\s\\S]*?<\/td><td>[\\s\\S]*?<\/td><td>([\\s\\S]*?)<\/td><td>[\\s\\S]*?<\/td><td>(\\d*?)MS<\/td><td>(\\d*?)K<\/td>/';
	preg_match ($Pattern, $data, $result);
	$StatusInfo['time'] = intval($Info[1]);
	$StatusInfo['memory'] = intval($Info[2]);
	$StatusInfo['result'] = strip_tags($result[1]);
	return $StatusInfo;
}

function Querier_POJ($row) {
	global $OJ;
	$run_id = $row['run_id'];
	$OJ_URL = $OJ['POJ'];
	$cookie = SCRIPT_ROOT.'POJ_'.$row['account'].'.tmp';
    GetContent($url, $cookie);
	$url = $OJ_URL.'showsource?solution_id='.$run_id;
    $data = GetContent($url, $cookie);
    //Get Time & Memory Limit
	$Pattern = "/<b>Memory:<\/b> (\\d*?)K<\/td><td width=10px><\/td><td><b>Time:<\/b> (\\d*?)MS<\/td><\/tr>/";
	preg_match ($Pattern, $data, $Info);
	//Get Judge Result
	$Pattern = '/<font color=.*?>(.*?)<\/font>/';
	preg_match ($Pattern, $data, $result);
	$StatusInfo['time'] = intval($Info[2]);
	$StatusInfo['memory'] = intval($Info[1]);
	$StatusInfo['result'] = $result[1];
	return $StatusInfo;
}

function Querier_FZU($row) {
	global $OJ;
	$run_id = $row['run_id'];
	$pid = $row['origin_id'];
	$OJ_URL = $OJ['FZU'];
	$acc = $row['account'];
	$url = $OJ_URL.'log.php?pid=' . $pid . '&user='. $acc;
    $data = GetContent($url);
    //Get Time&Memory Limit
    $Pattern =  '/<td>'.$run_id.'<\/td>\\s*'.
    			'<td>.*?<\/td>\\s*'.
    			'<td><font color=\\s*.*?>(.*?)<\/font><\/td>\\s*'.
    			'<td>.*?<\/td>\\s*'.
    			'<td>.*?<\/td>\\s*'.
    			'<td>(.*?)<\/td>\\s*'.
    			'<td>(.*?)<\/td>\\s*'.
    			'/';
	preg_match ($Pattern, $data, $Info);
	//var_dump($Info);
	if(strlen($Info[2]) == 0) {
		$StatusInfo['time'] = 0;
		$StatusInfo['memory'] = 0;
	}
	else {
		$StatusInfo['time'] = substr($Info[2],0,-3);
		$StatusInfo['memory'] = substr($Info[3],0,-2);
	}
	$StatusInfo['result'] = $Info[1];
	return $StatusInfo;
}

function Querier_HUST($row) {
	global $OJ;
	$run_id = $row['run_id'];
	$pid = $row['origin_id'];
	$OJ_URL = $OJ['HUST'];
	$cookie = SCRIPT_ROOT.'HUST_'.$row['account'].'.tmp';
    GetContent($url, $cookie);
	$url = $OJ_URL.'solution/source/'.$run_id;
    $data = GetContent($url, $cookie);
    //Get Time Limit
	$Pattern = "/<span class=\"badge\">(\\d+)ms<\/span>\\s*Time[\\s\\S]*?/";
	preg_match ($Pattern, $data, $time);
	//Get Memory Limit
	$Pattern = "/<span class=\"badge\">(\\d+)kb<\/span>\\s*Memory/";
	preg_match ($Pattern, $data, $memory);
	//Get Judge Result
	$Pattern = "/<span class=\"badge\">(.*?)<\/span>\\s*Result/";
	preg_match ($Pattern, $data, $result);
	$StatusInfo['time'] = intval($time[1]);
	$StatusInfo['memory'] = intval($memory[1]);
	$StatusInfo['result'] = $result[1];
	return $StatusInfo;
}

function Querier_FJNU($row) {
	global $OJ;
	$run_id = $row['run_id'];
	$OJ_URL = $OJ['FJNU'];
	$cookie = SCRIPT_ROOT.'FJNU_'.$row['account'].'.tmp';
	$url = $OJ_URL.'showsource.php?id='.$run_id;
    $data = GetContent($url, $cookie);
    if(strstr($data, 'I am sorry')) {
    	$url = $OJ_URL.'login.php';
	    $login['user_id'] = 'nextver';
	    $login['password'] = '123456';
	    GetContent($url, $cookie, $login, 1);
	    $url = $OJ_URL.'showsource.php?id='.$run_id;
    	$data = GetContent($url, $cookie);
    }
    //Get Time & Memory Limit
	$Pattern = "/Time:(\\d*?) ms/";
	preg_match ($Pattern, $data, $time);
	$Pattern = "/Memory:(\\d*?) kb/";
	preg_match ($Pattern, $data, $memory);
	$Pattern = '/Result: (.*?)\n/';
	preg_match ($Pattern, $data, $result);
	$StatusInfo['time'] = intval($time[1]);
	$StatusInfo['memory'] = intval($memory[1]);
	$StatusInfo['result'] = $result[1];
	return $StatusInfo;
}