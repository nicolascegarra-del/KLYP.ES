<?php
/**
 * Helpers de seguridad: CSRF y rate limiting por sesión.
 * Requiere que la sesión esté iniciada antes de llamar a estas funciones.
 */

function csrf_token(): string {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function csrf_field(): string {
    return '<input type="hidden" name="_token" value="' . csrf_token() . '">';
}

function csrf_verify(): void {
    $token = $_POST['_token'] ?? '';
    if (!$token || !hash_equals($_SESSION['csrf_token'] ?? '', $token)) {
        http_response_code(403);
        die('Acción no permitida: token de seguridad inválido. Recarga la página e inténtalo de nuevo.');
    }
}

/**
 * Devuelve true si el intento está permitido, false si se ha superado el límite.
 * $max intentos en $window segundos.
 */
function rate_limit_check(string $key, int $max = 5, int $window = 300): bool {
    $now = time();
    if (!isset($_SESSION['rl'][$key])) {
        $_SESSION['rl'][$key] = ['n' => 0, 't' => $now];
    }
    $rl = &$_SESSION['rl'][$key];
    if ($now - $rl['t'] > $window) {
        $rl = ['n' => 0, 't' => $now];
    }
    if ($rl['n'] >= $max) {
        return false;
    }
    $rl['n']++;
    return true;
}

function rate_limit_reset(string $key): void {
    unset($_SESSION['rl'][$key]);
}

function rate_limit_remaining(string $key, int $max = 5, int $window = 300): int {
    $now = time();
    if (!isset($_SESSION['rl'][$key])) return $max;
    $rl = $_SESSION['rl'][$key];
    if ($now - $rl['t'] > $window) return $max;
    return max(0, $max - $rl['n']);
}
