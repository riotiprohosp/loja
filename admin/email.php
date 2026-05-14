<?php
require_once __DIR__ . '/includes/admin-header.php';
exigir_perfil(['administrador','gerencial']);

function smtp_read_response($fp): string {
    $response = '';
    while (($line = fgets($fp)) !== false) {
        $response .= $line;
        if (isset($line[3]) && $line[3] === ' ') {
            break;
        }
    }
    return trim($response);
}

function smtp_send_command($fp, string $command): string {
    fwrite($fp, $command);
    return smtp_read_response($fp);
}

function smtp_connect(array $cfg, array &$meta = []) {
    $host = trim($cfg['smtp_host'] ?? '');
    $port = intval($cfg['smtp_port'] ?? 0);
    $crypto = $cfg['smtp_criptografia'] ?? 'tls';
    if ($host === '' || $port <= 0) {
        $meta['error'] = 'Configuração SMTP incompleta.';
        return null;
    }

    $remote = ($crypto === 'ssl' ? 'ssl://' : '') . $host . ':' . $port;
    $context = stream_context_create(['ssl' => ['verify_peer' => false, 'verify_peer_name' => false]]);
    $flags = STREAM_CLIENT_CONNECT;
    $fp = @stream_socket_client($remote, $errno, $errstr, 6, $flags, $context);
    if (!$fp) {
        $meta['error'] = "Falha ao conectar: $errstr ($errno)";
        return null;
    }

    stream_set_timeout($fp, 8);
    $banner = smtp_read_response($fp);
    $meta['banner'] = $banner;
    if (strpos($banner, '220') !== 0) {
        fclose($fp);
        $meta['error'] = 'Resposta inicial inválida: ' . $banner;
        return null;
    }

    $helo = smtp_send_command($fp, "EHLO localhost\r\n");
    $meta['helo'] = $helo;
    if ($crypto === 'tls') {
        if (stripos($helo, 'STARTTLS') === false) {
            fclose($fp);
            $meta['error'] = 'Servidor não aceita STARTTLS.';
            return null;
        }
        $starttls = smtp_send_command($fp, "STARTTLS\r\n");
        if (strpos($starttls, '220') !== 0) {
            fclose($fp);
            $meta['error'] = 'Falha ao iniciar STARTTLS: ' . $starttls;
            return null;
        }
        if (!stream_socket_enable_crypto($fp, true, STREAM_CRYPTO_METHOD_TLS_CLIENT)) {
            fclose($fp);
            $meta['error'] = 'Falha ao ativar criptografia STARTTLS.';
            return null;
        }
        $meta['tls'] = 'ok';
        $helo = smtp_send_command($fp, "EHLO localhost\r\n");
        $meta['helo_after_tls'] = $helo;
    }

    return $fp;
}

function smtp_check_status(array $cfg, array &$meta = []): bool {
    if (empty($cfg['smtp_host']) || empty($cfg['smtp_port'])) {
        $meta['error'] = 'SMTP não configurado.';
        return false;
    }
    $fp = smtp_connect($cfg, $meta);
    if (!$fp) {
        return false;
    }
    smtp_send_command($fp, "QUIT\r\n");
    fclose($fp);
    return true;
}

function smtp_send_test(array $cfg, string $recipient, array &$meta = []): bool {
    $fp = smtp_connect($cfg, $meta);
    if (!$fp) {
        return false;
    }

    $user = trim($cfg['smtp_usuario'] ?? '');
    $pass = trim($cfg['smtp_senha'] ?? '');
    if ($user !== '') {
        $auth = smtp_send_command($fp, "AUTH LOGIN\r\n");
        if (strpos($auth, '334') !== 0) {
            $meta['error'] = 'Autenticação SMTP não suportada pelo servidor: ' . $auth;
            fclose($fp);
            return false;
        }
        $userResp = smtp_send_command($fp, base64_encode($user) . "\r\n");
        $passResp = smtp_send_command($fp, base64_encode($pass) . "\r\n");
        if (strpos($passResp, '235') !== 0) {
            $meta['error'] = 'Falha na autenticação SMTP: ' . $passResp;
            fclose($fp);
            return false;
        }
    }

    $from = $user ?: 'noreply@' . preg_replace('/^.*@/', '', $user ?: $cfg['smtp_host']);
    smtp_send_command($fp, "MAIL FROM:<" . $from . ">\r\n");
    $rcpt = smtp_send_command($fp, "RCPT TO:<" . $recipient . ">\r\n");
    if (strpos($rcpt, '250') !== 0 && strpos($rcpt, '251') !== 0) {
        $meta['error'] = 'Destinatário recusado: ' . $rcpt;
        fclose($fp);
        return false;
    }

    smtp_send_command($fp, "DATA\r\n");
    $body = "From: " . $from . "\r\n";
    $body .= "To: " . $recipient . "\r\n";
    $body .= "Subject: Teste SMTP PROHOSP\r\n";
    $body .= "Date: " . date('r') . "\r\n";
    $body .= "MIME-Version: 1.0\r\n";
    $body .= "Content-Type: text/plain; charset=UTF-8\r\n";
    $body .= "\r\n";
    $body .= "Este é um e-mail de teste enviado pelo painel PROHOSP.\r\n";
    $body .= ".\r\n";
    $dataResp = smtp_send_command($fp, $body);
    if (strpos($dataResp, '250') !== 0) {
        $meta['error'] = 'Falha ao enviar mensagem de teste: ' . $dataResp;
        fclose($fp);
        return false;
    }

    smtp_send_command($fp, "QUIT\r\n");
    fclose($fp);
    return true;
}

$statusMessage = '';
$statusOk = null;
$meta = [];
$testResult = null;
$testEmail = '';
$action = $_POST['action'] ?? '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    validar_csrf();
    $cfg = [
        'id' => $_POST['id'] ?? '',
        'imap_host' => $_POST['imap_host'] ?? '',
        'imap_port' => intval($_POST['imap_port'] ?? 993),
        'smtp_host' => $_POST['smtp_host'] ?? '',
        'smtp_port' => intval($_POST['smtp_port'] ?? 587),
        'smtp_usuario' => $_POST['smtp_usuario'] ?? '',
        'smtp_senha' => $_POST['smtp_senha'] ?? '',
        'smtp_criptografia' => $_POST['smtp_criptografia'] ?? 'tls',
        'ativo' => isset($_POST['ativo']) ? 1 : 0,
    ];

    if ($action === 'save') {
        $dados = [$cfg['imap_host'], $cfg['imap_port'], $cfg['smtp_host'], $cfg['smtp_port'], $cfg['smtp_usuario'], $cfg['smtp_senha'], $cfg['smtp_criptografia'], $cfg['ativo'] ? 1 : 0];
        if ($cfg['id']) {
            $pdo->prepare('UPDATE email_servidores SET imap_host=?,imap_port=?,smtp_host=?,smtp_port=?,smtp_usuario=?,smtp_senha=?,smtp_criptografia=?,ativo=? WHERE id=?')->execute([...$dados, $cfg['id']]);
        } else {
            $pdo->prepare('INSERT INTO email_servidores (imap_host,imap_port,smtp_host,smtp_port,smtp_usuario,smtp_senha,smtp_criptografia,ativo,criado_em) VALUES (?,?,?,?,?,?,?,?,NOW())')->execute($dados);
        }
        registrar_log($pdo, 'Servidor de e-mail', 'Salvou configurações IMAP/SMTP');
        $cfg = $pdo->query('SELECT * FROM email_servidores ORDER BY id DESC LIMIT 1')->fetch() ?: $cfg;
    }

    if ($action === 'smtp_test') {
        $testEmail = trim($_POST['test_email'] ?? '');
        if (!filter_var($testEmail, FILTER_VALIDATE_EMAIL)) {
            $testResult = 'Informe um e-mail de teste válido.';
        } else {
            $statusOk = smtp_check_status($cfg, $meta);
            if ($statusOk) {
                $testOk = smtp_send_test($cfg, $testEmail, $meta);
                $testResult = $testOk ? 'E-mail de teste enviado com sucesso para ' . e($testEmail) . '.' : 'Falha ao enviar e-mail de teste: ' . e($meta['error'] ?? 'Erro desconhecido');
            } else {
                $testResult = 'Não foi possível testar o SMTP: ' . e($meta['error'] ?? 'Erro de conexão');
            }
        }
    }
} else {
    $cfg = $pdo->query('SELECT * FROM email_servidores ORDER BY id DESC LIMIT 1')->fetch() ?: [];
    if (!empty($cfg)) {
        $statusOk = smtp_check_status($cfg, $meta);
    }
}

if ($statusOk === null && !empty($cfg)) {
    $statusOk = smtp_check_status($cfg, $meta);
}

if ($statusOk === true) {
    $statusMessage = 'Servidor SMTP disponível e respondendo corretamente.';
} elseif ($statusOk === false) {
    $statusMessage = 'Servidor SMTP indisponível: ' . ($meta['error'] ?? 'sem resposta.');
}
?>
<h1>Servidor de e-mail</h1>
<section class="panel">
  <form method="post" class="form-grid">
    <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
    <input type="hidden" name="id" value="<?= e($cfg['id'] ?? '') ?>">
    <input type="hidden" name="action" value="save">

    <label>IMAP host<input name="imap_host" value="<?= e($cfg['imap_host'] ?? '') ?>"></label>
    <label>IMAP porta<input type="number" name="imap_port" value="<?= e($cfg['imap_port'] ?? 993) ?>"></label>

    <label>SMTP host<input name="smtp_host" value="<?= e($cfg['smtp_host'] ?? '') ?>"></label>
    <label>SMTP porta<input type="number" name="smtp_port" value="<?= e($cfg['smtp_port'] ?? 587) ?>"></label>

    <label>SMTP usuário<input name="smtp_usuario" value="<?= e($cfg['smtp_usuario'] ?? '') ?>"></label>
    <label>SMTP senha<input type="password" name="smtp_senha" value="<?= e($cfg['smtp_senha'] ?? '') ?>"></label>

    <label>Criptografia
      <select name="smtp_criptografia">
        <option value="tls" <?= ($cfg['smtp_criptografia'] ?? '') === 'tls' ? 'selected' : '' ?>>TLS</option>
        <option value="ssl" <?= ($cfg['smtp_criptografia'] ?? '') === 'ssl' ? 'selected' : '' ?>>SSL</option>
        <option value="nenhuma" <?= ($cfg['smtp_criptografia'] ?? '') === 'nenhuma' ? 'selected' : '' ?>>Nenhuma</option>
      </select>
    </label>

    <label class="check"><input type="checkbox" name="ativo" <?= ($cfg['ativo'] ?? 1) ? 'checked' : '' ?>> Ativo</label>
    <button class="btn primary" type="submit">Salvar</button>
  </form>
</section>

<section class="panel">
  <h2>Status SMTP</h2>
  <p><strong>Host:</strong> <?= e($cfg['smtp_host'] ?? 'Não configurado') ?><br>
  <strong>Porta:</strong> <?= e($cfg['smtp_port'] ?? 'não definida') ?><br>
  <strong>Criptografia:</strong> <?= e(strtoupper($cfg['smtp_criptografia'] ?? 'tls')) ?><br>
  <strong>Ativo:</strong> <?= ($cfg['ativo'] ?? 0) ? 'Sim' : 'Não' ?></p>
  <div class="alert <?= $statusOk ? 'ok' : 'warning' ?>"><?= e($statusMessage ?: 'Nenhuma verificação realizada.') ?></div>

  <form method="post" class="form-grid">
    <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
    <input type="hidden" name="id" value="<?= e($cfg['id'] ?? '') ?>">
    <input type="hidden" name="imap_host" value="<?= e($cfg['imap_host'] ?? '') ?>">
    <input type="hidden" name="imap_port" value="<?= e($cfg['imap_port'] ?? 993) ?>">
    <input type="hidden" name="smtp_host" value="<?= e($cfg['smtp_host'] ?? '') ?>">
    <input type="hidden" name="smtp_port" value="<?= e($cfg['smtp_port'] ?? 587) ?>">
    <input type="hidden" name="smtp_usuario" value="<?= e($cfg['smtp_usuario'] ?? '') ?>">
    <input type="hidden" name="smtp_senha" value="<?= e($cfg['smtp_senha'] ?? '') ?>">
    <input type="hidden" name="smtp_criptografia" value="<?= e($cfg['smtp_criptografia'] ?? 'tls') ?>">
    <input type="hidden" name="ativo" value="<?= ($cfg['ativo'] ?? 0) ? '1' : '0' ?>">
    <input type="hidden" name="action" value="smtp_test">

    <label>E-mail de teste<input type="email" name="test_email" value="<?= e($testEmail) ?>" placeholder="destino@exemplo.com"></label>
    <button class="btn primary" type="submit">Enviar e-mail de teste</button>
  </form>

  <?php if ($testResult !== null): ?>
    <div class="alert <?= strpos($testResult, 'sucesso') !== false ? 'ok' : 'warning' ?>"><?= e($testResult) ?></div>
  <?php endif; ?>
</section>

<?php require_once __DIR__ . '/includes/admin-footer.php'; ?>
