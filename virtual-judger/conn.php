<?php
namespace Judger\Conn;
include 'config.php';

class ConnectMysqli{
    private static $dbcon=false;
    private $host;
    private $port;
    private $username;
    private $password;
    private $database;
    private $charset;
    private $link;

    private function __construct($config = array()){
        $this->host = $config['host'];
        $this->port = $config['port'];
        $this->username = $config['username'];
        $this->password = $config['password'];
        $this->database = $config['database'];
        $this->charset = 'utf8';
        $this->db_connect();
        $this->db_usedb();
        $this->db_charset();
    }

    private function db_connect(){
        $this->link = mysqli_connect($this->host.':'.$this->port,$this->username,$this->password);
    }

    private function db_charset(){
        mysqli_query($this->link, "set names {$this->charset}");
    }

    private function db_usedb(){
        mysqli_query($this->link, "use {$this->database}");
    }

    private function __clone(){
        die('clone is not allowed');
    }

    public static function getIntance(){
        if(self::$dbcon == false){
            global $DB_CONFIG;
            self::$dbcon = new self($DB_CONFIG);
        }
        return self::$dbcon;
    }

    public function real_escape_string($param) {
        return $this->link->real_escape_string($param);
    }

    public function query($sql){
        try {
            $res = mysqli_query($this->link,$sql);
        } catch (\Exception $e) {
            if ($e->getCode() == 'HY000') {
                $this->db_connect(); //重连
                $res = $this->query($sql);
            } else {
                throw $e;
            }
        }

        return $res;
    }
}
