<?php
namespace Judger\config;
$BASE_PATH = "/Applications/XAMPP/xamppfiles/htdocs/vjudge/public"; //direct to /var/www/html/OnlineJudge/public
$URL_PATH = "/vjudge/public";  //direct to http://www.xxx.com/(.../public)

$DB_CONFIG = array(
    'host'      => "127.0.0.1",
    'port'      => 3306,
    'username'  => "root",
    'password'  => "123456",
    'database'  => "vjudge"
);

$OJ = array(
    'HDU'        => 'http://acm.hdu.edu.cn/', 
    'POJ'        => 'http://poj.org/', 
    'FZU'        => 'http://acm.fzu.edu.cn/', 
    'FJNU'       => 'http://acm.fjnu.edu.cn/'
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
    ],
    'FJNU' => [
        'AC'    => 'Accepted',
        'PE'    => 'Presentation Error',
        'TLE'   => 'Time Limit Exceeded',
        'MLE'   => 'Memory Limit Exceeded',
        'WA'    => 'Wrong Answer',
        'RE'    => 'Runtime Error',
        'OLE'   => 'Output Limit Exceeded',
        'CE'    => 'Compile Error'
    ],
);
?>