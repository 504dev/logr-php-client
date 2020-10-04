<?php

require_once 'AES.php';
require_once 'Logger.php';
require_once 'utils.php';

$pid = getmypid();
$hostname = gethostname();
$tag = gettag();
$commit = getcommit();


class Logr
{
    public $udp;
    public $public_key;
    private $private_key;
    private $private_hash;
    public $hostname;
    public $version;
    public $pid;
    public $cipher;

    public function __construct($udp, $public_key, $private_key, $options = [])
    {
        global $pid, $hostname;
        $this->udp = $udp;
        $this->public_key = $public_key;
        $this->private_key = $private_key;
        $this->private_hash = hash('sha256', base64_decode($private_key), true);
        $this->cipher = new AES($this->private_hash);
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