<?php
require_once __DIR__ . '/../config/banco.php';
require_once __DIR__ . '/../includes/funcoes.php';
$token = $_GET['token'] ?? $_POST['token'] ?? '';
$erro = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    validar_csrf();
    $hash = hash('sha256', $token);
    $stmt = $pdo->prepare('SELECT * FROM recuperacoes_senha WHERE token_hash = ? AND usado = 0 AND expira_em > NOW() LIMIT 1');
    $stmt->execute([$hash]);
    $rec = $stmt->fetch();
    if ($rec && strlen($_POST['senha'] ?? '') >= 6) {
        $senhaHash = password_hash($_POST['senha'], PASSWORD_BCRYPT);
        $pdo->prepare('UPDATE usuarios SET senha_hash = ? WHERE id = ?')->execute([$senhaHash, $rec['usuario_id']]);
        $pdo->prepare('UPDATE recuperacoes_senha SET usado = 1 WHERE id = ?')->execute([$rec['id']]);
        header('Location: login.php'); exit;
    }
    $erro = 'Token inválido/expirado ou senha menor que 6 caracteres.';
}
?>
<!DOCTYPE html><html lang="pt-BR"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>Nova senha</title><link rel="stylesheet" href="<?= e(url_base('assets/css/style.css')) ?>"></head><body class="login-body"><div class="login-card"><h1>Nova senha</h1><?php if($erro): ?><div class="alert"><?= e($erro) ?></div><?php endif; ?><form method="post"><input type="hidden" name="csrf_token" value="<?= csrf_token() ?>"><input type="hidden" name="token" value="<?= e($token) ?>"><label>Nova senha<input type="password" name="senha" required minlength="6"></label><button class="btn primary full">Salvar senha</button></form></div></body></html>
