<?php

namespace App\Service\Security;

class TokenService
{
    /**
     * @param array $data
     * @return string
     */
    public function encode(array $data): string
    {
        return base64_encode(json_encode($data, JSON_UNESCAPED_UNICODE));
    }

    /**
     * @param string $token
     * @return array
     */
    public function decode(string $token): array
    {
        return $data = base64_decode($token) ? json_decode($token, true) : [];
    }
}