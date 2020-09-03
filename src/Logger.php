<?php

require_once 'AES.php';

$pid = getmypid();
$hostname = gethostname();

class Logger {
    private $config;
    private $logname;
    private $conn;
    private $prefix;
    private $body;
    private $level;

    public function __construct($config, $logname, $level = '')
    {
        $this->config = $config;
        $this->logname = $logname;
        $this->conn = NULL;
        $this->prefix = '{time} {level} ';
        $this->body = '[{version}, pid={pid}, {initiator}] {message}';
        $this->level = $level;
    }


    public function send($level, $message)
    {
        $encryptor = new AES($this->config->private_hash);
        $data = array(
            "timestamp"=>json_encode(microtime(true) * 1e9),
            "hostname"=>$this->config->hostname,
            "logname"=>$this->logname,
            "level"=>$level,
            "pid"=>$this->config->pid,
            "version"=>"v1.0.14",
            "message"=>$message
        );
        $json = json_encode($data);
        echo $json;
        return $encryptor->encrypt($json);
    }
}