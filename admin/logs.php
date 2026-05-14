<?php require_once __DIR__ . '/includes/admin-header.php'; exigir_perfil(['administrador','gerencial']);
$logs=$pdo->query('SELECT * FROM logs_acoes ORDER BY id DESC LIMIT 300')->fetchAll();
?>
<h1>Logs de ações</h1><p class="muted">Registra as principais ações feitas pelo usuário logado.</p><section class="panel"><div class="table-wrap"><table><thead><tr><th>Data</th><th>Usuário</th><th>Ação</th><th>Detalhes</th><th>IP</th></tr></thead><tbody><?php foreach($logs as $l): ?><tr><td><?= e($l['criado_em']) ?></td><td><?= e($l['usuario_nome']) ?></td><td><?= e($l['acao']) ?></td><td><?= e($l['detalhes']) ?></td><td><?= e($l['ip']) ?></td></tr><?php endforeach; ?></tbody></table></div></section><?php require_once __DIR__ . '/includes/admin-footer.php'; ?>
