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

    static function colorize($level)
    {
        $color = [
            LEVEL_EMERG => "\033[31m",
            LEVEL_ALERT => "\033[31m",
            LEVEL_CRIT => "\033[31m",
            LEVEL_ERROR => "\033[91m",
            LEVEL_WARN => "\033[33m",
            LEVEL_NOTICE => "\033[92m",
            LEVEL_INFO => "\033[32m",
            LEVEL_DEBUG => "\033[34m",
        ][$level];
        return $color ? $color . $level . "\033[0m" : $level;
    }

    static function direct($level)
    {
        $direct = [
            LEVEL_EMERG => STDERR,
            LEVEL_ALERT => STDERR,
            LEVEL_CRIT => STDERR,
            LEVEL_ERROR => STDERR,
            LEVEL_WARN => STDOUT,
            LEVEL_NOTICE => STDOUT,
            LEVEL_INFO => STDOUT,
            LEVEL_DEBUG => STDOUT,
        ][$level];
        return $direct ? $direct : STDERR;
    }

    public function __construct(Logr $config, $logname, $level = '')
    {
        $this->config = $config;
        $this->logname = $logname;
        $this->conn = NULL;
        $this->prefix = '{time} {level} ';
        $this->body = '[{version}, pid={pid}, {initiator}] {message}';
        $this->level = $level;
        $this->conn = socket_create(AF_INET, SOCK_DGRAM, SOL_UDP);
    }

    public function initiator()
    {
        $info = debug_backtrace()[3];
        $file = $info["file"];
        $line = $info["line"];
        return implode("/", array_slice(explode("/", $file), -2)).":".$line;
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
        $res = str_replace('{initiator}', $this->initiator(), $res);
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
        fwrite(self::direct($level), $prefix . $body . PHP_EOL);
        $this->send($level, $message);
    }

    public function send($level, $message)
    {
        $encryptor = new AES($this->config->private_hash);
        $payload = [
            "timestamp" => json_encode(microtime(true) * 1e9),
            "hostname" => $this->config->hostname,
            "logname" => $this->logname,
            "level" => $level,
            "pid" => $this->config->pid,
            "version" => $this->config->getVersion(),
            "message" => $message
        ];
        $json = json_encode($payload);
        $cipher_log = $encryptor->encrypt($json);
        $pack = [
            "public_key" => $this->config->public_key,
            "cipher_log" => $cipher_log,
        ];
        $address = explode(":", $this->config->udp);
        $msg = json_encode($pack);
        socket_sendto($this->conn, $msg, strlen($msg), 0, ...$address);
    }
}