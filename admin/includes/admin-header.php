<?php
require_once __DIR__ . '/../../config/banco.php';
require_once __DIR__ . '/../../includes/funcoes.php';
exigir_login();
$u = usuario_logado();
$current = basename($_SERVER['SCRIPT_NAME'] ?? 'index.php');
$nav = [
  ['index.php', 'Dashboard', '⌂'],
  ['usuarios.php', 'Usuários', '👥'],
  ['produtos.php', 'Produtos', '□'],
  ['email.php', 'E-mail', '✉'],
  ['pagina.php', 'Página', '⚙'],
  ['ceps.php', 'CEPs', '⌖'],
  ['notificacoes.php', 'Notificações', '🔔'],
  ['bancos.php', 'Bancos', '▣'],
  ['regras-pedido.php', 'Regras', '✓'],
  ['logs.php', 'Logs', '☰'],
];
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin - PROHOSP</title>
  <link rel="stylesheet" href="<?= e(url_base('assets/css/style.css')) ?>">
</head>
<body class="admin-body">
<div id="adminOverlay" class="admin-overlay"></div>
<aside class="admin-sidebar">
  <div class="admin-logo-wrap">
    <a class="admin-logo" href="index.php"><span class="admin-logo-icon">+</span><strong>PROHOSP</strong></a>
    <small>Painel administrativo</small>
  </div>
  <nav class="admin-nav">
    <span class="nav-label">Principal</span>
    <?php foreach ($nav as $item): ?>
      <a class="<?= $current === $item[0] ? 'active' : '' ?>" href="<?= e($item[0]) ?>"><span><?= e($item[2]) ?></span><em><?= e($item[1]) ?></em></a>
    <?php endforeach; ?>
    <span class="nav-label">Conta</span>
    <a href="<?= e(url_base('index.php')) ?>"><span>↗</span><em>Ver loja</em></a>
    <a href="sair.php"><span>⇥</span><em>Sair</em></a>
  </nav>
</aside>
<div class="admin-shell">
  <header class="admin-top">
    <button class="admin-menu-toggle" type="button" aria-label="Abrir menu">☰</button>
    <div class="admin-searchbar"><span>⌕</span><input type="search" placeholder="Pesquisar no painel..."></div>
    <div class="admin-userbox">
      <span class="admin-avatar"><?= e(strtoupper(substr($u['nome'] ?? 'U', 0, 1))) ?></span>
      <div><b><?= e($u['nome']) ?></b><small><?= e($u['perfil']) ?></small></div>
    </div>
  </header>
  <main class="admin-content">
