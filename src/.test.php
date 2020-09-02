<?php

include 'aes.php';

$enc = new AES(openssl_random_pseudo_bytes(32));

$data = "Encrypt me, please!";
echo "Before encryption: $data\n";
$encrypted = $enc->encrypt($data);
echo "Encrypted: $encrypted\n";
$decrypted = $enc->decrypt($encrypted);
echo "Decrypted: $decrypted\n";