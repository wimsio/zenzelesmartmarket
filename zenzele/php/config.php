<?php
// ===== ZENZELE SMART MARKET — CONFIG =====

// Database settings — these are correct for XAMPP default installation
define('DB_HOST', 'localhost');
define('DB_NAME', 'zenzele_db');
define('DB_USER', 'root');
define('DB_PASS', '');           // XAMPP default = empty password
define('DB_PORT', 3306);

define('JWT_SECRET', 'zenzele_jwt_secret_change_this_on_live_server_2026');
define('APP_URL',    'http://localhost/zenzele-smart-market');
define('TOKEN_EXPIRY', 60 * 60 * 24 * 7); // 7 days
define('CARDANO_NETWORK', 'testnet');

// ── CORS headers (allow frontend to call this API) ──────────────────────
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');
header('Content-Type: application/json; charset=utf-8');

// Handle browser preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

// ── Database connection ──────────────────────────────────────────────────
try {
    $dsn = "mysql:host=" . DB_HOST . ";port=" . DB_PORT . ";dbname=" . DB_NAME . ";charset=utf8mb4";
    $pdo = new PDO($dsn, DB_USER, DB_PASS, [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error'   => 'Database connection failed.',
        'detail'  => $e->getMessage(),
        'fix'     => 'Make sure MySQL is running in XAMPP and zenzele_db exists in phpMyAdmin.'
    ]);
    exit;
}

// ── Response helpers ─────────────────────────────────────────────────────
function jsonOk($data, int $code = 200): void {
    http_response_code($code);
    echo json_encode(['success' => true, 'data' => $data]);
    exit;
}

function jsonErr(string $msg, int $code = 400, array $extra = []): void {
    http_response_code($code);
    echo json_encode(array_merge(['success' => false, 'error' => $msg], $extra));
    exit;
}

function getBody(): array {
    $raw = file_get_contents('php://input');
    $decoded = json_decode($raw, true);
    return is_array($decoded) ? $decoded : [];
}

function sanitize(string $s): string {
    return htmlspecialchars(trim($s), ENT_QUOTES, 'UTF-8');
}

function requireMethod(string ...$methods): void {
    if (!in_array($_SERVER['REQUEST_METHOD'], $methods, true)) {
        jsonErr('Method not allowed. Expected: ' . implode(' or ', $methods), 405);
    }
}

// ── Auth middleware ───────────────────────────────────────────────────────
function requireAuth(PDO $pdo): array {
    $header = $_SERVER['HTTP_AUTHORIZATION'] ?? '';
    $token  = str_replace('Bearer ', '', $header);
    if (!$token) {
        jsonErr('Unauthorized — no token provided. Please log in.', 401);
    }
    require_once __DIR__ . '/jwt.php';
    $payload = JWT::decode($token, JWT_SECRET);
    if (!$payload) {
        jsonErr('Unauthorized — token is invalid or expired. Please log in again.', 401);
    }
    $stmt = $pdo->prepare('SELECT * FROM users WHERE id = ?');
    $stmt->execute([$payload['sub']]);
    $user = $stmt->fetch();
    if (!$user) {
        jsonErr('Unauthorized — user not found.', 401);
    }
    return $user;
}
