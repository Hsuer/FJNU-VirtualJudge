<?php
namespace Judger\Crawler;
include 'normalize_url.php';

function Crawler($origin_oj, $origin_id) {
    $problem = null;
    switch ($origin_oj) {
        case 'HDU':
            $problem = Crawler_HDU($origin_id);
            break;
        case 'FZU':
            $problem = Crawler_FZU($origin_id);
            break;
        case 'POJ':
            $problem = Crawler_POJ($origin_id);
            break;
        case 'FJNU':
            $problem = Crawler_FJNU($origin_id);
            break;
        default:
            break;
    }
    if(!empty($problem['title'])) {
        return $problem;
    }
    else {
        return null;
    }
}

function Crawler_HDU($pid) {
    global $OJ;
    $url=$OJ['HDU']."showproblem.php?pid=$pid";
    $content=file_get_contents($url);
    $content=iconv("gbk","UTF-8//IGNORE",$content);
    $ret=init_array();
    if (stripos($content,"Invalid Parameter")===false && stripos($content,"No such problem - <strong>Problem")===false) {
        $ret["origin_oj"] = "HDU"; $ret['origin_id'] = $pid;
        if (preg_match("/<h1 style='color:#1A5CC8'>(.*)<\\/h1>/sU", $content,$matches)) 
            $ret["title"]=trim($matches[1]);
        if (preg_match("/Time Limit:.*\\/(.*) MS/sU", $content,$matches)) 
            $ret["time"]=intval(trim($matches[1]));
        if (preg_match("/Memory Limit:.*\\/(.*) K/sU", $content,$matches)) 
            $ret["memory"]=intval(trim($matches[1]));
        if (preg_match("/Problem Description.*<div class=panel_content>(.*)<\\/div><div class=panel_bottom>/sU", $content,$matches)) 
            $ret["description"]=trim($matches[1]);
        if (preg_match("/<div class=panel_title align=left>Input.*<div class=panel_content>(.*)<\\/div><div class=panel_bottom>/sU", $content,$matches)) 
            $ret["input"]=trim($matches[1]);
        if (preg_match("/<div class=panel_title align=left>Output.*<div class=panel_content>(.*)<\\/div><div class=panel_bottom>/sU", $content,$matches)) 
            $ret["output"]=trim($matches[1]);
        if (preg_match("/Sample Input.*<pre><div.*>(.*)<\/div>/sU", $content,$matches)) 
            $ret["sample_input"]=trim($matches[1]);
        if (preg_match("/Sample Output.*<pre><div.*>(.*)<\/?div/sU", $content,$matches)) 
            $ret["sample_output"]=trim($matches[1]);
        if (preg_match("/<i>Hint<\/i>.*<\/i>(.*)<\/div><\/pre>/sU", $content,$matches)) 
            $ret["hint"]=trim($matches[1]);
        if (preg_match("/<div class=panel_title align=left>Source<\\/div> (.*)<div class=panel_bottom>/sU", $content,$matches)) 
            $ret["source"]=trim(strip_tags($matches[1]));
        if (strpos($content,"<font color=red>Special Judge</font>")!==false) 
            $ret["special_judge"]=1;
        else $ret["special_judge"]=0;
        return process('HDU', $pid, $ret);
    }
}

function Crawler_FZU($pid) {
    global $OJ;
    $url=$OJ['FZU']."problem.php?pid=$pid";
    $content=file_get_contents($url);
    $ret=init_array();
    if (stripos($content,"<font size=\"+3\">No Such Problem!</font>")===false) {
        $ret["origin_oj"] = "FZU"; $ret['origin_id'] = $pid;
        if (preg_match("/<b> Problem $pid(.*)<\\/b>/sU", $content,$matches)) $ret["title"]=trim($matches[1]);
        if (preg_match("/<br \\/>Time Limit:(.*) mSec/sU", $content,$matches)) $ret["time"]=intval(trim($matches[1]));
        // $ret["case_time_limit"]=$ret["time_limit"];
        if (preg_match("/Memory Limit : (.*) KB/sU", $content,$matches)) $ret["memory"]=intval(trim($matches[1]));
        if (preg_match("/Problem Description<\\/h2><\\/b>(.*)<h2>/sU", $content,$matches)) $ret["description"]=trim($matches[1]);
        if (preg_match("/> Input<\\/h2>(.*)<h2>/sU", $content,$matches)) $ret["input"]=trim($matches[1]);
        if (preg_match("/> Output<\\/h2>(.*)<h2>/sU", $content,$matches)) $ret["output"]=trim($matches[1]);
        if (preg_match("/<div class=\"data\">(.*)<\\/div>/sU", $content,$matches)) $ret["sample_input"]=trim($matches[1]);
        if ($ret["sample_input"]=="") {
            if (preg_match("/<div class=\"data\">(.*)<\\/div>/sU", $content,$matches)) $ret["sample_output"]=trim($matches[1]);
        }
        else if (preg_match("/<div class=\"data\">.*<div class=\"data\">(.*)<\\/div>/sU", $content,$matches)) $ret["sample_output"]=trim($matches[1]);
        if (preg_match("/Hint<\\/h2>(.*)<h2>/sU", $content,$matches)) $ret["hint"]=trim($matches[1]);
        if (preg_match("/Source<\\/h2>(.*)<\\/div>/sU", $content,$matches)) $ret["source"]=trim($matches[1]);
        if (strpos($content,"<font color=\"blue\">Special Judge</font>")!==false) $ret["special_judge"]=1;
        else $ret["special_judge"]=0;
        return process('FZU', $pid, $ret);
    }
}

function Crawler_POJ($pid){
    global $OJ;
    $url = $OJ['POJ']."problem?id=$pid";
    $content = file_get_contents($url);
    $ret=init_array();
    if (trim($content) == "") return "No problem called PKU $pid.<br>";
    if (stripos($content, "Can not find problem") === false){
        $ret["origin_oj"] = "POJ"; $ret['origin_id'] = $pid;
        if (preg_match('/<div class="ptt" lang="en-US">(.*)<\/div>/sU', $content, $matches)) $ret["title"] = trim($matches[1]);
        if (preg_match('/<td><b>Time Limit:<\/b> (.*)MS<\/td>/sU', $content, $matches)) $ret["time"] = intval(trim($matches[1]));
        // $ret["case_time_limit"] = $ret["time_limit"];
        if (preg_match('/<td><b>Memory Limit:<\/b> (.*)K<\/td>/sU', $content, $matches)) $ret["memory"] = intval(trim($matches[1]));
        if (preg_match('/<p class="pst">Description<\/p><div class="ptx" lang="en-US">(.*)<\/div><p class="pst">Input<\/p>/sU', $content, $matches)) $ret["description"] = trim($matches[1]);
        if (preg_match('/<p class="pst">Input<\/p><div class="ptx" lang="en-US">(.*)<\/div><p class="pst">Output<\/p>/sU', $content, $matches)) $ret["input"] = trim($matches[1]);
        if (preg_match('/<p class="pst">Output<\/p><div class="ptx" lang="en-US">(.*)<\/div><p class="pst">Sample Input<\/p>/sU', $content, $matches)) $ret["output"] = trim($matches[1]);
        if (preg_match('/<p class="pst">Sample Input<\/p><pre class="sio">(.*)<\/pre><p class="pst">Sample Output<\/p>/sU', $content, $matches)) $ret["sample_input"] = trim($matches[1]);
        if (preg_match('/<p class="pst">Sample Output<\/p><pre class="sio">(.*)<\/pre><p class="pst">Source<\/p>/sU', $content, $matches)) $ret["sample_output"] = trim($matches[1]);
        if (preg_match('/<p class="pst">Source<\/p><div class="ptx" lang="en-US">(.*)<\/div>/sU', $content, $matches)) $ret["source"] = trim(strip_tags($matches[1]));
        if (strpos($content, '<td style="font-weight:bold; color:red;">Special Judge</td>') !== false) $ret["special_judge"] = 1;
        else $ret["special_judge"] = 0;
        return process('POJ', $pid, $ret);
    }
}

function Crawler_FJNU($pid){
    global $OJ;
    $url = $OJ['FJNU']."problem.php?id=$pid";
    $content = file_get_contents($url);
    $ret=init_array();
    if (trim($content) == "") return "No problem called FJNU $pid.<br>";
    if (stripos($content, "Problem is not available") === false){
        $ret["origin_oj"] = "FJNU"; $ret['origin_id'] = $pid;
        if (preg_match('/<\/title><center><h2>\d+: (.*)<\/h2><span/sU', $content, $matches)) $ret["title"] = trim($matches[1]);
        if (preg_match('/Time Limit: <\/span>(.*) Sec&nbsp;&nbsp;<span class=green>/sU', $content, $matches)) $ret["time"] = intval(trim($matches[1])) * 1000;
        // $ret["case_time_limit"] = $ret["time_limit"];
        if (preg_match('/Memory Limit: <\/span>(.*) MB<br>/sU', $content, $matches)) $ret["memory"] = intval(trim($matches[1])) * 1024;
        if (preg_match('/<h3>Description<\/h3><div class=well>(.*)<\/div><h3>Input/sU', $content, $matches)) $ret["description"] = trim($matches[1]);
        if (preg_match('/<h3>Input<\/h3><div class=well>(.*)<\/div><h3>Output/sU', $content, $matches)) $ret["input"] = trim($matches[1]);
        if (preg_match('/<h3>Output<\/h3><div class=well>(.*)<\/div><h3>Sample Input/sU', $content, $matches)) $ret["output"] = trim($matches[1]);
        if (preg_match('/Sample Input<\/h3>\s<pre class=content><span class=sampledata>(.*)<\/span><\/pre><h3>Sample Output/sU', $content, $matches)) $ret["sample_input"] = trim($matches[1]);
        if (preg_match('/Sample Output<\/h3>\s<pre class=content><span class=sampledata>(.*)<\/span><\/pre><h3>HINT/sU', $content, $matches)) $ret["sample_output"] = trim($matches[1]);
        if (preg_match('/HINT<\/h3>\s<div class=content><p>(.*)<\/p><\/div><h3>Source<\/h3>/sU', $content, $matches)) $ret["hint"] = trim($matches[1]);
        if (preg_match('/Source<\/h3>(.*)<\/p><\/div><center>/sU', $content, $matches)) $ret["source"] = trim(strip_tags($matches[1]));
        // if (strpos($content, '<td style="font-weight:bold; color:red;">Special Judge</td>') !== false) $ret["special_judge"] = 1;
        // else $ret["special_judge"] = 0;
        $ret["special_judge"] = 0;
        return process('FJNU', $pid, $ret);
    }
}

function process($OJ_NAME, $pid, $ret) {
    $ret["description"]=getImage($OJ_NAME, $pid, $ret["description"], "D");
    $ret["input"]=getImage($OJ_NAME, $pid, $ret["input"], "I");
    $ret["output"]=getImage($OJ_NAME, $pid, $ret["output"], "O");
    $ret["hint"]=getImage($OJ_NAME, $pid, $ret["hint"], "H");
    return $ret;
}

function getImage($OJ_NAME, $pid, $str, $type) {
    global $OJ, $BASE_PATH, $URL_PATH;
    $OJ_URL = $OJ[$OJ_NAME];
    $reg="/< *im[a]?g[^>]*src *= *[\"\\']?([^\"\\'>]*)[^>]*>/si";
    preg_match_all($reg,$str,$match);
    for($i = 0; $i < count($match[0]); $i++){
        $img_path_name = $match[1][$i];
        $img_path_name = normalizeURL($img_path_name);
        // echo $img_path_name.PHP_EOL;
        $img_url = $OJ_URL.$img_path_name;
        // echo $img_url;
        $str = preg_replace_callback(
            $reg,
            function($matches) use($pid, $OJ_NAME, $type, $URL_PATH) {
                static $i=0;
                $matches[$i]="<img src=$URL_PATH/data/$OJ_NAME/$pid/$type-$i.jpg>";
                return $matches[$i++];
            },
            $str
        );
        ob_start();
        readfile($img_url);
        $img = ob_get_contents();
        ob_end_clean();
        mkdir($BASE_PATH."/data/$OJ_NAME/$pid/", 0777, true);
        chmod($BASE_PATH."/data/$OJ_NAME/$pid/", 0777);
        $fp = fopen($BASE_PATH."/data/$OJ_NAME/$pid/$type-$i.jpg","w");
        fwrite($fp,$img);
        fclose($fp);
    }
    return $str;
}

function init_array() {
    return array(
        "title"=>"",
        "origin_oj"=>"",
        "origin_id"=>"",
        "memory"=>0,
        "time"=>0,
        "description"=>"",
        "input"=>"",
        "output"=>"",
        "sample_input"=>"",
        "sample_output"=>"",
        "hint"=>"",
        "source"=>"",
        "author"=>"",
        "special_judge"=>0,
        "available"=>0,
    );
}