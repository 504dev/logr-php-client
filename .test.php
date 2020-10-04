<?php

require_once './src/Logr.php';

$logr = new Logr(
    'localhost:7776',
    'MCAwDQYJKoZIhvcNAQEBBQADDwAwDAIFAMg7IrMCAwEAAQ==',
    'MC0CAQACBQDIOyKzAgMBAAECBQCHaZwRAgMA0nkCAwDziwIDAL+xAgJMKwICGq0='
);
$logger = $logr->getLogger('hello.log');
$logger->debug('Hello!');
$logger->info('Hello!');
$logger->notice('Hello!');
$logger->warn('Hello!');
$logger->error('Hello!');
$logger->crit('Hello!');

$iv = '0123456789abcdef';
$key = '0123456789abcdef';
$key = hash('sha256', $key, false);
echo $key . PHP_EOL;
echo base64_encode(hex2bin($key)) . PHP_EOL;
$encrypted = openssl_encrypt('hello', 'aes-256-cfb', hex2bin($key), 3, $iv);
echo '========' . PHP_EOL;
echo base64_encode($iv) . PHP_EOL;
echo base64_encode($encrypted) . PHP_EOL;
echo base64_encode($iv . $encrypted) . PHP_EOL;