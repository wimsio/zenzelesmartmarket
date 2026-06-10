<?php
// ===== ZENZELE — JWT HELPER =====
class JWT {
    private static function base64url_encode($data) {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }
    private static function base64url_decode($data) {
        return base64_decode(strtr($data, '-_', '+/') . str_repeat('=', 3 - (3 + strlen($data)) % 4));
    }

    public static function encode(array $payload, string $secret): string {
        $header  = self::base64url_encode(json_encode(['typ'=>'JWT','alg'=>'HS256']));
        $payload = self::base64url_encode(json_encode($payload));
        $sig     = self::base64url_encode(hash_hmac('sha256', "$header.$payload", $secret, true));
        return "$header.$payload.$sig";
    }

    public static function decode(string $token, string $secret): ?array {
        $parts = explode('.', $token);
        if (count($parts) !== 3) return null;
        [$header, $payload, $sig] = $parts;
        $expected = self::base64url_encode(hash_hmac('sha256', "$header.$payload", $secret, true));
        if (!hash_equals($expected, $sig)) return null;
        $data = json_decode(self::base64url_decode($payload), true);
        if (isset($data['exp']) && $data['exp'] < time()) return null;
        return $data;
    }
}
