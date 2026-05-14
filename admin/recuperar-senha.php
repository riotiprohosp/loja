<?php
require_once __DIR__ . '/../config/banco.php';
require_once __DIR__ . '/../includes/funcoes.php';
$msg = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    validar_csrf();
    $email = $_POST['email'] ?? '';
    $stmt = $pdo->prepare('SELECT id, nome FROM usuarios WHERE email = ? AND ativo = 1');
    $stmt->execute([$email]);
    if ($user = $stmt->fetch()) {
        $token = bin2hex(random_bytes(32));
        $hash = hash('sha256', $token);
        $pdo->prepare('INSERT INTO recuperacoes_senha (usuario_id, token_hash, expira_em, usado, criado_em) VALUES (?, ?, DATE_ADD(NOW(), INTERVAL 1 HOUR), 0, NOW())')->execute([$user['id'], $hash]);
        $link = url_base('admin/redefinir-senha.php?token=' . $token);
        $srv = $pdo->query('SELECT * FROM email_servidores WHERE ativo = 1 ORDER BY id DESC LIMIT 1')->fetch();
        if ($srv) { @mail($email, 'Recuperação de senha PROHOSP', "Acesse para redefinir sua senha: $link"); }
        $msg = 'Caso o e-mail esteja cadastrado em nossa base, enviaremos as instruções.';
        registrar_log($pdo, 'Recuperação de senha', 'Solicitação para ' . $email);
    } else { $msg = 'Se o e-mail existir, enviaremos as instruções.'; }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Recuperar senha</title>
  <link rel="stylesheet" href="<?= e(url_base('assets/css/style.css')) ?>">
</head>
<body class="login-body">
  <main class="login-shell">
    <section class="login-card">
      <div class="login-brand">
        <div>
          <h2>Recuperar senha</h2>
          <p>Informe o e-mail cadastrado para receber o link de redefinição.</p>
        </div>
      </div>
      <?php if($msg): ?><div class="alert ok"><?= e($msg) ?></div><?php endif; ?>
      <form method="post" class="login-form">
        <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
        <label>E-mail<input type="email" name="email" required autocomplete="email" placeholder="seu@email.com"></label>
        <button class="btn primary full" type="submit">Enviar recuperação</button>
        <a class="link" href="login.php">Voltar ao login</a>
      </form>
    </section>
  </main>
</body>
</html>
