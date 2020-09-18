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
