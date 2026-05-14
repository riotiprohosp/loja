<?php require_once __DIR__ . '/includes/admin-header.php';
$totais = [
 'usuarios'=>$pdo->query('SELECT COUNT(*) c FROM usuarios')->fetch()['c'],
 'produtos'=>$pdo->query('SELECT COUNT(*) c FROM produtos')->fetch()['c'],
 'ceps'=>$pdo->query('SELECT COUNT(*) c FROM ceps_entrega')->fetch()['c'],
 'logs'=>$pdo->query('SELECT COUNT(*) c FROM logs_acoes')->fetch()['c'],
];
$labels = ['usuarios'=>'Usuários', 'produtos'=>'Produtos', 'ceps'=>'Faixas de CEP', 'logs'=>'Logs'];
$icons = ['usuarios'=>'👥', 'produtos'=>'□', 'ceps'=>'⌖', 'logs'=>'☰'];
?>
<div class="page-title"><div><h1>Dashboard</h1><p class="muted">Resumo operacional da loja hospitalar.</p></div><a class="btn primary" href="produtos.php">Novo produto</a></div>
<div class="admin-cards">
<?php foreach($totais as $k=>$v): ?><div class="metric"><span class="metric-icon"><?= e($icons[$k]) ?></span><div><span><?= e($labels[$k]) ?></span><b><?= (int)$v ?></b><small>Atualizado em tempo real</small></div></div><?php endforeach; ?>
</div>
<section class="panel"><div class="panel-head"><div><h2>Ações rápidas</h2><p class="muted">Acesse as rotinas mais usadas.</p></div></div><div class="quick-grid"><a href="produtos.php">Cadastrar produto</a><a href="usuarios.php">Cadastrar usuário</a><a href="pagina.php">Ajustar página</a><a href="regras-pedido.php">Regras de pedido</a></div></section>
<?php require_once __DIR__ . '/includes/admin-footer.php'; ?>
