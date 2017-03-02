<?php
function checkStatus($String) {
    if(strstr($String, 'Accept')) return true;
    if(strstr($String, 'Wrong Answer'))  return true;
    if(strstr($String, 'Error')) return true;
    if(strstr($String, 'Exceed')) return true;
    return false; 
}

function getStatus($status_id) {
	global $conn;
	$sql = "select
			status.id,status.user_id,status.language,status.result,status.created_at,
			problems.origin_oj,problems.origin_id,solutions.code
			from status 
			left join problems 
			on status.problem_id = problems.id 
			left join solutions 
			on status.id = solutions.id
			where status.id = '".$conn->real_escape_string($status_id)."'";
	$result = $conn->query($sql);
	$row = $result->fetch_array(MYSQLI_ASSOC);
	$result->free();
	return $row;
}

function getAccount($OJ) {
	global $account;
	$total_num = count($account[$OJ]);
	echo 'tot:' . $total_num;
	$rand = rand(0, $total_num - 1);
	return $account[$OJ][$rand];
}

function setSubmitted($status_id) {
	global $conn;
	$sql = "update status set result = 'Submitted' where id = '".$conn->real_escape_string($status_id)."'";
	$conn->query($sql);
}

function setSubmitError($status_id) {
	global $conn;
	$sql = "update status set result = 'Submit Error' where id = '".$conn->real_escape_string($status_id)."'";
	$conn->query($sql);
}

function setJudgeError($status_id) {
	global $conn;
	$sql = "update status set result = 'Judge Error' where id = '".$conn->real_escape_string($status_id)."'";
	$conn->query($sql);
}

function setResult($status_id, $result, $time, $memory) {
	global $conn;
	$sql = "update status set 
			result='".$conn->real_escape_string($result)."',
			time='".$conn->real_escape_string($time)."',
			memory='".$conn->real_escape_string($memory)."' 
			where id = '".$conn->real_escape_string($status_id)."'";
    $result = $conn->query($sql);
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
	$sql = "insert into problems(title, origin_oj, origin_id, time, memory, special_judge, description, input, output, sample_input, sample_output, hint, author, source, available, ac_num, submit_num, created_at, updated_at) values (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";
	if ($stmt = $conn->prepare($sql)) {
		$stmt->bind_param("sssiiissssssssiiiss", $title, $origin_oj, $origin_id, $time, $memory, $special_judge, $description, $input, $output, $sample_input, $sample_output, $hint, $author, $source, $available, $ac_num, $submit_num, $created_at, $updated_at);
		$stmt->execute();
		$stmt->close();
	} else {
	    die("Errormessage: ". $conn->error);
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
		if(!$conn->query($sql))
			printf("Errormessage: %s\n", $conn->error);
	}
}

function getContent($url, $cookie = '', $post = '', $returnCookie = 0) {
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_HEADER, 0);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($curl, CURLOPT_TIMEOUT, 60);
    curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 10);
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

// function sendmsg($status_id) {
// 	$client = new swoole_client(SWOOLE_SOCK_TCP);
// 	if (!$client->connect('127.0.0.1', 9503, -1))
// 	{
// 	    exit("connect failed. Error: {$client->errCode}\n");
// 	}
// 	$data = $status_id;
// 	$client->send($data);
// 	echo "Send Successfully: ".$client->recv().PHP_EOL;
// 	$client->close();
// }

// function getNewStatus() {
// 	global $conn;
// 	$sql = "select id from status where result = 'Pending'";
// 	$result = mysqli_query($conn, $sql);
// 	if (!$result) {
// 		printf("Error: %s\n", mysqli_error($conn));
// 		exit();
// 	}
// 	$row = mysqli_fetch_all($result, MYSQLI_ASSOC);
// 	return $row;
// }

// function setRunid($status_id, $run_id) {
// 	global $conn;
// 	$sql = "update status set run_id = '$run_id' where id = '$status_id'";
// 	$conn->query($sql);
// }