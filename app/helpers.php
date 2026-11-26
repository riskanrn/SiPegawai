<?php
declare(strict_types=1);
function e($v): string { return htmlspecialchars((string)($v ?? ''), ENT_QUOTES, 'UTF-8'); }
function rupiah($v): string { return 'Rp' . number_format((float)$v, 0, ',', '.'); }
function page_url(string $page, array $params = []): string {
    return BASE_URL . '/index.php?' . http_build_query(array_merge(['page' => $page], $params));
}
function redirect_to(string $url): void { header('Location: ' . $url); exit; }
function set_flash(string $type, string $message): void { $_SESSION['flash'] = ['type' => $type, 'message' => $message]; }
function pull_flash(): ?array {
    if (!isset($_SESSION['flash'])) return null;
    $f = $_SESSION['flash']; unset($_SESSION['flash']); return $f;
}
function csrf_token(): string {
    if (empty($_SESSION['csrf_token'])) $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    return $_SESSION['csrf_token'];
}
function verify_csrf(): void {
    $sent = $_POST['csrf_token'] ?? ''; $saved = $_SESSION['csrf_token'] ?? '';
    if (!$sent || !$saved || !hash_equals($saved, $sent)) throw new RuntimeException('Token formulir tidak valid. Muat ulang halaman.');
}
function pagination_data(int $total, int $page, int $perPage): array {
    $pages = max(1, (int)ceil($total / $perPage));
    return ['total'=>$total, 'page'=>min(max(1,$page),$pages), 'pages'=>$pages, 'per_page'=>$perPage];
}
