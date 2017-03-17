<?php
$BASE_PATH = "/var/www/html/OnlineJudge/public/";
$URL_PATH = "http://localhost/OnlineJudge/public/";

$DB_CONN = array(
    'server'    => "localhost",
    'port'      => 3306,
    'username'  => "root",
    'password'  => "123456",
    'database'  => "test"
);

$OJ = array(
    'HDU'        => 'http://acm.hdu.edu.cn/', 
    'POJ'        => 'http://poj.org/', 
    'FZU'        => 'http://acm.fzu.edu.cn/', 
    'HUST'       => 'http://acm.hust.edu.cn/', 
    'FJNU'       => 'http://acm.fjnu.edu.cn/',
    'Codeforces' => 'http://codeforces.com/'
);

$JUDGE_RESULT = array(
    'HDU' => [
        'AC'    => 'Accepted',
        'WA'    => 'Wrong Answer',
        'PE'    => 'Presentation Error',
        'CE'    => 'Compilation Error',
        'RE'    => 'Runtime Error',
        'TLE'   => 'Time Limit Exceeded',
        'MLE'   => 'Memory Limit Exceeded',
        'OLE'   => 'Output Limit Exceeded'
    ],
    'POJ' => [
        'AC'    => 'Accepted',
        'PE'    => 'Presentation Error',
        'TLE'   => 'Time Limit Exceeded',
        'MLE'   => 'Memory Limit Exceeded',
        'WA'    => 'Wrong Answer',
        'RE'    => 'Runtime Error',
        'OLE'   => 'Output Limit Exceeded',
        'CE'    => 'Compile Error'
    ],
    'FZU' => [
        'AC'    => 'Accepted',
        'PE'    => 'Presentation Error',
        'WA'    => 'Wrong Answer',
        'RE'    => 'Runtime Error',
        'TLE'   => 'Time Limit Exceed',
        'MLE'   => 'Memory Limit Exceed',
        'OLE'   => 'Output Limit Exceed',
        'RFC'   => 'Restrict Function Call',
        'CE'    => 'Compile Error'
    ]
);
?>