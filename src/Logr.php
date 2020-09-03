<?php

require_once 'Logger.php';

$pid = getmypid();
$hostname = gethostname();
$tag = '';
$commit = '';

class Logr
{
    public $udp;
    public $public_key;
    public $private_key;
    public $private_hash;
    public $hostname;
    public $version;
    public $pid;

    public function __construct($udp, $public_key, $private_key, $options = [])
    {
        global $pid, $hostname;
        $this->udp = $udp;
        $this->public_key = $public_key;
        $this->private_key = $private_key;
        $this->private_hash = hash('sha256', $private_key);
        $this->hostname = $options->hostname ? $options->hostname : $hostname;
        $this->version = $options->version;
        $this->pid = $pid;
    }

    public function getVersion()
    {
        global $tag, $commit;
        if ($this->version) {
            return $this->version;
        } else if ($tag) {
            return $tag;
        } else if ($commit) {
            return substr($commit, 0, 6);
        } else {
            return '';
        }
    }

    public function getLogger($logname, $level = '')
    {
        return new Logger($this, $logname, $level);
    }
}