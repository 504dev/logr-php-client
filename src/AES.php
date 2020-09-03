<?php
define('AES_256_CFB', 'aes-256-cfb');

class AES
{
    private $encryption_key;
    private $block_size;

    function __construct($encryption_key)
    {
        $this->encryption_key = $encryption_key;
        $this->block_size = openssl_cipher_iv_length(AES_256_CFB);
    }

    function encrypt(string $data)
    {
        $iv = openssl_random_pseudo_bytes($this->block_size);
        $encrypted = openssl_encrypt($data, AES_256_CFB, $this->encryption_key, 0, $iv);
        return base64_encode($iv . base64_decode($encrypted));
    }

    function decrypt(string $data)
    {
        $iv = base64_encode(substr(base64_decode($data), 0, $this->block_size));
        $enc = base64_encode(substr(base64_decode($data), $this->block_size, strlen($data) - $this->block_size));
        return openssl_decrypt($enc, AES_256_CFB, $this->encryption_key, 0, base64_decode($iv));
    }
}