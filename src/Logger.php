<?php

require_once 'AES.php';


class Logger
{
    private $config;
    private $logname;
    private $conn;
    private $prefix;
    private $body;
    private $level;

    public function __construct(Logr $config, $logname, $level = '')
    {
        $this->config = $config;
        $this->logname = $logname;
        $this->conn = NULL;
        $this->prefix = '{time} {level} ';
        $this->body = '[{version}, pid={pid}, {initiator}] {message}';
        $this->level = $level;
    }


    public function getPrefix($level)
    {
        $res = $this->prefix;
        $res = str_replace('{time}', date("Y-m-d H:i:s"), $res);
        $res = str_replace('{level}', $level, $res);
        return $res;
    }

    public function getBody($message)
    {
        $res = $this->body;
        $res = str_replace('{version}', $this->config->getVersion(), $res);
        $res = str_replace('{pid}', $this->config->pid, $res);
        $res = str_replace('{initiator}', '', $res);
        $res = str_replace('{message}', $message, $res);
        return $res;
    }

    public function log($level, $message)
    {
        $prefix = $this->getPrefix($level);
        $body = $this->getBody($message);
        echo $prefix . $body . "\n";
        $this->send($level, $message);
    }

    public function send($level, $message)
    {
        $encryptor = new AES($this->config->private_hash);
        $data = array(
            "timestamp" => json_encode(microtime(true) * 1e9),
            "hostname" => $this->config->hostname,
            "logname" => $this->logname,
            "level" => $level,
            "pid" => $this->config->pid,
            "version" => $this->config->getVersion(),
            "message" => $message
        );
        $json = json_encode($data);
        echo $json;
        return $encryptor->encrypt($json);
    }
}