<?php
require_once __DIR__ . '/includes/admin-header.php';

$tipos = ['pedido_criado', 'pedido_pago', 'pedido_enviado', 'pedido_cancelado', 'recuperacao_senha'];
$canais = ['email', 'whatsapp', 'sms'];
$variaveis = [
    '{{nome}}' => 'Nome do usuário.',
    '{{pedido}}' => 'Número ou código do pedido.',
    '{{link}}' => 'Link de acesso ou recuperação.',
    '{{email}}' => 'E-mail do usuário.',
    '{{telefone}}' => 'Telefone do usuário.',
    '{{status}}' => 'Status atual do pedido.',
    '{{total}}' => 'Valor total do pedido.',
    '{{data}}' => 'Data relacionada à notificação.',
    '{{codigo_rastreio}}' => 'Código de rastreio do pedido.',
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    validar_csrf();

    if (isset($_POST['excluir'])) {
        $pdo->prepare('DELETE FROM notificacoes_clientes WHERE id=?')->execute([$_POST['id']]);
        registrar_log($pdo, 'Notificações', 'Excluiu notificação ID ' . $_POST['id']);
    } elseif (isset($_POST['salvar'])) {
        $id = (int)($_POST['id'] ?? 0);
        $dados = [
            $_POST['tipo'],
            $_POST['canal'],
            $_POST['assunto'],
            $_POST['mensagem_padrao'],
            isset($_POST['ativo']) ? 1 : 0,
        ];

        if ($id > 0) {
            $stmt = $pdo->prepare('UPDATE notificacoes_clientes SET tipo=?, canal=?, assunto=?, mensagem_padrao=?, ativo=? WHERE id=?');
            $stmt->execute([...$dados, $id]);
            registrar_log($pdo, 'Notificações', 'Editou notificação ID ' . $id);
        } else {
            $stmt = $pdo->prepare('INSERT INTO notificacoes_clientes (tipo,canal,assunto,mensagem_padrao,ativo,criado_em) VALUES (?,?,?,?,?,NOW())');
            $stmt->execute($dados);
            registrar_log($pdo, 'Notificações', 'Criou tipo ' . $_POST['tipo']);
        }
    }
}

$editar = null;
if (isset($_GET['editar'])) {
    $stmt = $pdo->prepare('SELECT * FROM notificacoes_clientes WHERE id=?');
    $stmt->execute([(int)$_GET['editar']]);
    $editar = $stmt->fetch() ?: null;
}

$rows = $pdo->query('SELECT * FROM notificacoes_clientes ORDER BY id DESC')->fetchAll();
?>
<h1>Notificações dos clientes</h1>
<div class="grid-2">
  <section class="panel">
    <h2><?= $editar ? 'Editar modelo' : 'Nova mensagem padrão' ?></h2>
    <form method="post" class="form-grid">
      <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
      <input type="hidden" name="id" value="<?= e($editar['id'] ?? '') ?>">

      <label>Tipo
        <select name="tipo">
          <?php foreach ($tipos as $tipo): ?>
            <option value="<?= e($tipo) ?>" <?= ($editar['tipo'] ?? '') === $tipo ? 'selected' : '' ?>><?= e($tipo) ?></option>
          <?php endforeach; ?>
        </select>
      </label>

      <label>Canal
        <select name="canal">
          <?php foreach ($canais as $canal): ?>
            <option value="<?= e($canal) ?>" <?= ($editar['canal'] ?? '') === $canal ? 'selected' : '' ?>><?= e($canal) ?></option>
          <?php endforeach; ?>
        </select>
      </label>

      <label class="wide">Assunto<input name="assunto" required value="<?= e($editar['assunto'] ?? '') ?>"></label>
      <div class="wide field-with-tools">
        <div class="field-head">
          <span>Mensagem padrão</span>
          <div class="variable-picker">
            <button class="variable-toggle" type="button" aria-expanded="false">Variáveis</button>
            <div class="variable-list">
              <?php foreach ($variaveis as $variavel => $descricao): ?>
                <button type="button" data-variable="<?= e($variavel) ?>"><strong><?= e($variavel) ?></strong><small><?= e($descricao) ?></small></button>
              <?php endforeach; ?>
            </div>
          </div>
        </div>
        <textarea id="mensagemPadrao" name="mensagem_padrao" required><?= e($editar['mensagem_padrao'] ?? 'Olá {{nome}}, seu pedido {{pedido}} foi atualizado.') ?></textarea>
      </div>
      <label class="check"><input type="checkbox" name="ativo" <?= (int)($editar['ativo'] ?? 1) === 1 ? 'checked' : '' ?>> Ativo</label>
      <button class="btn primary" name="salvar">Salvar</button>
      <?php if ($editar): ?><a class="link" href="notificacoes.php">Cancelar edição</a><?php endif; ?>
    </form>
  </section>

  <section class="panel">
    <h2>Modelos</h2>
    <div class="table-wrap">
      <table>
        <thead><tr><th>Tipo</th><th>Canal</th><th>Assunto</th><th>Status</th><th></th></tr></thead>
        <tbody>
          <?php foreach ($rows as $r): ?>
          <tr>
            <td><?= e($r['tipo']) ?></td>
            <td><?= e($r['canal']) ?></td>
            <td><?= e($r['assunto']) ?></td>
            <td><?= $r['ativo'] ? 'Ativo' : 'Inativo' ?></td>
            <td>
              <a href="?editar=<?= (int)$r['id'] ?>">Editar</a>
              <form method="post" class="inline">
                <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
                <input type="hidden" name="id" value="<?= (int)$r['id'] ?>">
                <button name="excluir">Excluir</button>
              </form>
            </td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </section>
</div>
<?php require_once __DIR__ . '/includes/admin-footer.php'; ?>
