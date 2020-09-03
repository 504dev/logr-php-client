<?php

require_once 'AES.php';
require_once 'Logr.php';
require_once 'Logger.php';

$enc = new AES(hash('sha256', 'private'));

$data = "Encrypt me, please!";
echo "Before encryption: $data\n";
$encrypted = $enc->encrypt($data);
echo "Encrypted: $encrypted\n";
$decrypted = $enc->decrypt($encrypted);
echo "Decrypted: $decrypted\n";

$logr = new Logr('localhost:7776', 'public', 'private');
$logger = $logr->getLogger('hello.log');
echo $logger->send('info', 'Hello!');
