<?php
require_once __DIR__ . '/../../config/banco.php';
require_once __DIR__ . '/../../includes/funcoes.php';
exigir_login();
$u = usuario_logado();
$current = basename($_SERVER['SCRIPT_NAME'] ?? 'index.php');
$nav = [
  ['index.php', 'Dashboard', '⌂'],
  
];
$nav2 = [

  ['usuarios.php', 'Usuários', '👥'],
  ['produtos.php', 'Produtos', '□'],
  ['ceps.php', 'CEPs', '⌖'],
  ['notificacoes.php', 'Notificações', '🔔'],
];
$nav3 = [
  ['pagina.php', 'Ajustes do Site', '⚙'],
  ['email.php', 'Servidor de E-mail', '✉'],
  ['bancos.php', 'Banco de Dados', '▣'],
  ['logs.php', 'Logs', '☰'],
];
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Administração</title>
  <link rel="stylesheet" href="<?= e(url_base('assets/css/style.css')) ?>">
</head>
<body class="admin-body">
<div id="adminOverlay" class="admin-overlay"></div>
<aside class="admin-sidebar">
  <div class="admin-logo-wrap">
    <a class="admin-logo" href="index.php"><strong>PROHOSP</strong></a>
    <button id="sidebarCollapse" class="sidebar-collapse" type="button" aria-label="Recolher painel">⋘</button>
  </div>
  <div class="admin-nav-section">
    <div class="admin-nav-group">
      <button class="nav-group-toggle" type="button" aria-expanded="false"><span>PRINCIPAL</span><em>⯈</em></button>
      <nav class="admin-nav nav-group-list">
        <?php foreach ($nav as $item): ?>
          <a class="<?= $current === $item[0] ? 'active' : '' ?>" href="<?= e($item[0]) ?>"><span><?= e($item[2]) ?></span><em><?= e($item[1]) ?></em></a>
        <?php endforeach; ?>
      </nav>
    </div>
    <div class="admin-nav-group">
      <button class="nav-group-toggle" type="button" aria-expanded="false"><span>CADASTROS</span><em>⯈</em></button>
      <nav class="admin-nav nav-group-list">
        <?php foreach ($nav2 as $item): ?>
          <a class="<?= $current === $item[0] ? 'active' : '' ?>" href="<?= e($item[0]) ?>"><span><?= e($item[2]) ?></span><em><?= e($item[1]) ?></em></a>
        <?php endforeach; ?>
      </nav>
    </div>
    <div class="admin-nav-group">
      <button class="nav-group-toggle" type="button" aria-expanded="false"><span>SERVIÇOS</span><em>⯈</em></button>
      <nav class="admin-nav nav-group-list">
        <?php foreach ($nav3 as $item): ?>
          <a class="<?= $current === $item[0] ? 'active' : '' ?>" href="<?= e($item[0]) ?>"><span><?= e($item[2]) ?></span><em><?= e($item[1]) ?></em></a>
        <?php endforeach; ?>
      </nav>
    </div>
    <div class="admin-nav-group">
      <button class="nav-group-toggle" type="button" aria-expanded="false"><span>Conta</span><em>⯈</em></button>
      <nav class="admin-nav nav-group-list">
        <a href="<?= e(url_base('index.php')) ?>"><span>↗</span><em>Ver loja</em></a>
        <a href="sair.php"><span>⇥</span><em>Sair</em></a>
      </nav>
    </div>
  </div>
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
