<?php
require_once __DIR__ . '/../config/config.php';

function iniciar_sessao(): void {
    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_name(SESSION_NAME);
        session_start();
    }
}

function e($valor): string {
    return htmlspecialchars((string)$valor, ENT_QUOTES, 'UTF-8');
}

function moeda($valor): string {
    return 'R$ ' . number_format((float)$valor, 2, ',', '.');
}

function base_instalacao(): string {
    if (defined('APP_URL') && trim((string) APP_URL) !== '') {
        return rtrim((string) APP_URL, '/');
    }

    $script = str_replace('\\', '/', $_SERVER['SCRIPT_NAME'] ?? '');
    $dir = rtrim(str_replace('\\', '/', dirname($script)), '/');

    // Quando o script estiver em /admin ou /pages, volta para a raiz do projeto.
    if (preg_match('~/admin$~', $dir) || preg_match('~/pages$~', $dir)) {
        $dir = rtrim(dirname($dir), '/');
    }

    if ($dir === '/' || $dir === '.' || $dir === '') {
        return '';
    }

    return $dir;
}

function url_base(string $path = ''): string {
    if (preg_match('#^[a-z][a-z0-9+\-.]*://#i', $path)) {
        return $path;
    }

    $base = base_instalacao();
    $path = ltrim($path, '/');

    if ($path === '') {
        return $base === '' ? '/' : $base . '/';
    }

    return ($base === '' ? '' : $base) . '/' . $path;
}

function csrf_token(): string {
    iniciar_sessao();
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function validar_csrf(): void {
    iniciar_sessao();
    $token = $_POST['csrf_token'] ?? '';
    if (!$token || !hash_equals($_SESSION['csrf_token'] ?? '', $token)) {
        http_response_code(403);
        exit('Token de segurança inválido.');
    }
}

function usuario_logado(): ?array {
    iniciar_sessao();
    return $_SESSION['usuario_logado'] ?? null;
}

function exigir_login(): void {
    if (!usuario_logado()) {
        header('Location: login.php');
        exit;
    }
}

function exigir_perfil(array $perfis): void {
    $usuario = usuario_logado();
    if (!$usuario || !in_array($usuario['perfil'], $perfis, true)) {
        http_response_code(403);
        exit('Acesso negado.');
    }
}

function registrar_log(PDO $pdo, string $acao, string $detalhes = ''): void {
    $usuario = usuario_logado();
    $stmt = $pdo->prepare('INSERT INTO logs_acoes (usuario_id, usuario_nome, acao, detalhes, ip, criado_em) VALUES (?, ?, ?, ?, ?, NOW())');
    $stmt->execute([
        $usuario['id'] ?? null,
        $usuario['nome'] ?? 'Sistema',
        $acao,
        $detalhes,
        $_SERVER['REMOTE_ADDR'] ?? ''
    ]);
}

function buscar_ajustes(PDO $pdo): array {
    $stmt = $pdo->query('SELECT chave, valor FROM ajustes_pagina');
    $dados = [];
    foreach ($stmt->fetchAll() as $row) {
        $dados[$row['chave']] = $row['valor'];
    }
    return $dados;
}

function cep_para_int(string $cep): int {
    return (int) preg_replace('/\D+/', '', $cep);
}
