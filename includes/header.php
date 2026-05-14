<?php
require_once __DIR__ . '/../config/banco.php';
require_once __DIR__ . '/funcoes.php';

function url_normalize(string $path): string {
    if ($path === '' || $path === '/') {
        return '/index.php';
    }
    if (substr($path, -1) === '/') {
        return $path . 'index.php';
    }
    return $path;
}

function request_path(): string {
    $uri = $_SERVER['REQUEST_URI'] ?? '/';
    $uri = strtok($uri, '#');
    $parts = parse_url($uri);
    $path = $parts['path'] ?? '/';
    return url_normalize($path);
}

function request_query(): string {
    $uri = $_SERVER['REQUEST_URI'] ?? '/';
    $uri = strtok($uri, '#');
    $parts = parse_url($uri);
    return $parts['query'] ?? '';
}

$currentPath = request_path();
$currentQuery = request_query();
$ajustes = buscar_ajustes($pdo);
$menus = $pdo->query('SELECT titulo, url FROM menus WHERE ativo = 1 ORDER BY ordem, id')->fetchAll();
$logo = $ajustes['logo'] ?? '';
$defaultLogo = 'assets/img/logo.png';
if ($logo !== '' && !preg_match('#^[a-z][a-z0-9+\-.]*://#i', $logo)) {
  $logoLocal = __DIR__ . '/../' . ltrim(parse_url($logo, PHP_URL_PATH) ?? $logo, '/');
  if (!file_exists($logoLocal)) {
    $logo = $defaultLogo;
  }
}
if ($logo === '') {
  $logo = $defaultLogo;
}
$titulo = $ajustes['titulo_pagina'] ?? 'PROHOSP';
$nomeLoja = $ajustes['nome_loja'] ?? 'PROHOSP';
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="description" content="Loja hospitalar B2B/B2C com catálogo moderno para medicamentos, descartáveis, EPIs, curativos, higiene e equipamentos.">
  <meta name="theme-color" content="#08235f">
  <title><?= e($titulo) ?></title>
  <link rel="icon" href="<?= e(url_base('assets/img/favico.ico')) ?>" type="image/png">
  <link rel="stylesheet" href="<?= e(url_base('assets/css/style.css')) ?>">
</head>
<body class="storefront-body">
<header class="site-header">
  <div class="topline">
    <div class="container topgrid">
      <span>Entrega mais rápida do Rio.</span>
      <span><?= e($ajustes['contato_topo'] ?? 'Atendimento: contato@PROHOSP.local') ?></span>
    </div>
  </div>

  <div class="container header-main">
    <a class="brand" href="<?= e(url_base('index.php')) ?>" aria-label="Página inicial">
      <?php if ($logo): ?><img src="<?= e(url_base($logo)) ?>" alt="<?= e($nomeLoja) ?>"><?php else: ?><span class="brand-mark">+</span><?php endif; ?>
    </a>

    <form class="search" action="<?= e(url_base('index.php')) ?>" method="get">
      <input name="busca" placeholder="Buscar produto, medicamento, código..." value="<?= e($_GET['busca'] ?? '') ?>">
      <button type="submit">Buscar</button>
    </form>

    <div class="actions-right">
      <a class="action-link" href="#produtos">Comprar</a>
      <a class="action-link" href="<?= e(url_base('admin/login.php')) ?>">Entrar</a>
      <a class="cart-pill" href="<?= e(url_base('carrinho.php')) ?>"><span>Meu carrinho</span><b class="cart-count">0</b></a>
    </div>

    <button class="mobile-toggle" type="button" aria-label="Abrir menu">☰</button>
  </div>

  <nav id="main-nav" class="main-nav" role="navigation" aria-label="Menu principal">
    <div class="container nav-scroll">
      <?php foreach ($menus as $menu): ?>
      <?php
        $menuUrl = trim($menu['url'] ?? '');
        $menuHref = url_base($menuUrl);
        $menuParts = parse_url($menuHref);
        $menuPath = url_normalize($menuParts['path'] ?? '/');
        $menuQuery = $menuParts['query'] ?? '';
        $menuActive = !isset($menuParts['fragment']) && $menuPath === $currentPath && $menuQuery === $currentQuery;
      ?>
      <a class="nav-link<?= $menuActive ? ' active' : '' ?>" href="<?= e($menuHref) ?>"><?= e($menu['titulo']) ?></a>
      <?php endforeach; ?>
    </div>
  </nav>
</header>
