<?php
require_once __DIR__ . '/../config/banco.php';
require_once __DIR__ . '/../includes/funcoes.php';
iniciar_sessao();
$erro = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    validar_csrf();
    $email = trim((string)($_POST['email'] ?? ''));
    $stmt = $pdo->prepare('SELECT * FROM usuarios WHERE ativo = 1 AND LOWER(email) = LOWER(?) LIMIT 1');
    $stmt->execute([$email]);
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
  <title>PROHOSP</title>
  <link rel="stylesheet" href="<?= e(url_base('assets/css/style.css')) ?>?v=<?= (int)filemtime(__DIR__ . '/../assets/css/style.css') ?>">
</head>
<body class="login-body">
  <main class="login-shell">
  
    <section class="login-card">
      <div class="login-brand"><div><h2>PROHOSP Distribuidora de Medicamentos LTDA</h2><p>Pessoal Autorizado</p></div></div>
      <?php if($erro): ?><div class="alert"><?= e($erro) ?></div><?php endif; ?>
      <form method="post" class="login-form">
        <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
        <label>E-mail<input type="email" name="email" required autocomplete="email" placeholder="email@dominio.com.br"></label>
        <label>Senha
          <span class="password-field">
            <input id="loginSenha" type="password" name="senha" required autocomplete="current-password" placeholder="Digite sua senha">
            <button class="password-toggle" type="button" aria-label="Mostrar senha" aria-pressed="false">◉</button>
          </span>
        </label>
        <button class="btn primary full" type="submit">Entrar</button>
        <a class="link" href="recuperar-senha.php">Recuperar senha</a>
      </form>
    </section>
  </main>
  <script>
    document.querySelector('.password-toggle')?.addEventListener('click', function () {
      const input = document.getElementById('loginSenha');
      const show = input.type === 'password';
      input.type = show ? 'text' : 'password';
      this.setAttribute('aria-label', show ? 'Ocultar senha' : 'Mostrar senha');
      this.setAttribute('aria-pressed', show ? 'true' : 'false');
      this.textContent = show ? '◎' : '◉';
      input.focus();
    });
  </script>
</body>
</html>
