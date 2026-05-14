<?php
require_once __DIR__ . '/../config/banco.php';
require_once __DIR__ . '/../includes/funcoes.php';
iniciar_sessao();
$erro = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    validar_csrf();
    $stmt = $pdo->prepare('SELECT * FROM usuarios WHERE email = ? AND ativo = 1 LIMIT 1');
    $stmt->execute([$_POST['email'] ?? '']);
    $usuario = $stmt->fetch();
    if ($usuario && password_verify($_POST['senha'] ?? '', $usuario['senha_hash'])) {
        $_SESSION['usuario_logado'] = ['id'=>$usuario['id'], 'nome'=>$usuario['nome'], 'email'=>$usuario['email'], 'perfil'=>$usuario['perfil']];
        registrar_log($pdo, 'Login', 'Usuário acessou o painel administrativo');
        header('Location: index.php'); exit;
    }
    $erro = 'E-mail ou senha inválidos.';
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Entrar - PROHOSP</title>
  <link rel="stylesheet" href="<?= e(url_base('assets/css/style.css')) ?>">
</head>
<body class="login-body">
  <main class="login-shell">
    <section class="login-hero">
      <h1>Controle completo da loja.</h1>
      <div class="login-metrics"><div><b>100%</b><span>Funcional</span></div><div><b>100%</b><span>Intuitivo</span></div><div><b>BackOffice</b><span>Auditável</span></div></div>
    </section>
    <section class="login-card">
      <div class="login-brand"><div><h2>PROHOSP Distribuidora de Medicamentos</h2><p>Pessoal Autorizado</p></div></div>
      <?php if($erro): ?><div class="alert"><?= e($erro) ?></div><?php endif; ?>
      <form method="post" class="login-form">
        <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
        <label>E-mail<input type="email" name="email" required autocomplete="email" placeholder="admin@prohosp.local"></label>
        <label>Senha<input type="password" name="senha" required autocomplete="current-password" placeholder="Digite sua senha"></label>
        <button class="btn primary full" type="submit">Entrar no painel</button>
        <a class="link" href="recuperar-senha.php">Recuperar senha</a>
      </form>
    </section>
  </main>
</body>
</html>
