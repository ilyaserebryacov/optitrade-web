<?php

namespace App\Security;

class Encryptor
{
    private string $key;
    private string $cipher;

    public function __construct(?string $key = null, string $cipher = 'AES-256-CBC')
    {
        $rawKey = $key ?? getenv('APP_KEY') ?: 'default-secret-key-for-local-zoo-32b!';
        if (str_starts_with($rawKey, 'base64:')) {
            $rawKey = base64_decode(substr($rawKey, 7));
        }
        $this->key = hash('sha256', $rawKey, true);
        $this->cipher = $cipher;
    }

    /**
     * Шифрование строкового значения
     */
    public function encrypt(string $plainText): string
    {
        $ivLength = openssl_cipher_iv_length($this->cipher);
        $iv = openssl_random_pseudo_bytes($ivLength);

        $cipherText = openssl_encrypt(
            $plainText,
            $this->cipher,
            $this->key,
            OPENSSL_RAW_DATA,
            $iv
        );

        return base64_encode($iv . $cipherText);
    }

    /**
     * Расшифровка зашифрованного значения
     */
    public function decrypt(string $encryptedPayload): string
    {
        $data = base64_decode($encryptedPayload);
        $ivLength = openssl_cipher_iv_length($this->cipher);

        $iv = substr($data, 0, $ivLength);
        $cipherText = substr($data, $ivLength);

        return (string) openssl_decrypt(
            $cipherText,
            $this->cipher,
            $this->key,
            OPENSSL_RAW_DATA,
            $iv
        );
    }
}
