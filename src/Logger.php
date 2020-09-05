<?php

require_once 'AES.php';
require_once 'levels.php';


class Logger
{
    private $config;
    private $logname;
    private $conn;
    private $prefix;
    private $body;
    private $level;

    static function colorize($level) {
        $map = [
            LEVEL_EMERG => "\033[31m",
            LEVEL_ALERT => "\033[31m",
            LEVEL_CRIT => "\033[31m",
            LEVEL_ERROR => "\033[91m",
            LEVEL_WARN => "\033[33m",
            LEVEL_NOTICE => "\033[92m",
            LEVEL_INFO => "\033[32m",
            LEVEL_DEBUG => "\033[34m",
        ];
        return $map[$level] ? $map[$level].$level."\033[0m" : $level;
    }

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
        $res = str_replace('{level}', self::colorize($level), $res);
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

    public function emerg($message)
    {
        $this->log(LEVEL_EMERG, $message);
    }

    public function alert($message)
    {
        $this->log(LEVEL_ALERT, $message);
    }

    public function crit($message)
    {
        $this->log(LEVEL_CRIT, $message);
    }

    public function error($message)
    {
        $this->log(LEVEL_ERROR, $message);
    }

    public function warn($message)
    {
        $this->log(LEVEL_WARN, $message);
    }

    public function notice($message)
    {
        $this->log(LEVEL_NOTICE, $message);
    }

    public function info($message)
    {
        $this->log(LEVEL_INFO, $message);
    }

    public function debug($message)
    {
        $this->log(LEVEL_DEBUG, $message);
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
        echo $json."\n";
        return $encryptor->encrypt($json);
    }
}