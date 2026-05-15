<?php require_once __DIR__ . '/includes/admin-header.php'; exigir_perfil(['administrador','gerencial']);
if ($_SERVER['REQUEST_METHOD'] === 'POST') { validar_csrf();
    if (isset($_POST['excluir'])) { exigir_perfil(['administrador']); $pdo->prepare('UPDATE usuarios SET ativo = 0 WHERE id = ?')->execute([$_POST['id']]); registrar_log($pdo,'Usuários','Desativou usuário ID '.$_POST['id']); }
    else {
      $id = $_POST['id'] ?? '';
      $dados = [$_POST['nome'], $_POST['usuario'], $_POST['email'], $_POST['perfil'], isset($_POST['ativo']) ? 1 : 0];
      if ($id) {
        if (!empty($_POST['senha'])) { $senha = password_hash($_POST['senha'], PASSWORD_BCRYPT); $stmt=$pdo->prepare('UPDATE usuarios SET nome=?, usuario=?, email=?, perfil=?, ativo=?, senha_hash=? WHERE id=?'); $stmt->execute([...$dados,$senha,$id]); }
        else { $stmt=$pdo->prepare('UPDATE usuarios SET nome=?, usuario=?, email=?, perfil=?, ativo=? WHERE id=?'); $stmt->execute([...$dados,$id]); }
        registrar_log($pdo,'Usuários','Atualizou usuário ID '.$id);
      } else {
        $senha = password_hash($_POST['senha'] ?: '123456', PASSWORD_BCRYPT);
        $stmt=$pdo->prepare('INSERT INTO usuarios (nome, usuario, email, perfil, ativo, senha_hash, criado_em) VALUES (?,?,?,?,?,?,NOW())'); $stmt->execute([...$dados,$senha]);
        registrar_log($pdo,'Usuários','Criou usuário '.$_POST['email']);
      }
    }
}
$editar = null; if(isset($_GET['editar'])) { $st=$pdo->prepare('SELECT * FROM usuarios WHERE id=?'); $st->execute([$_GET['editar']]); $editar=$st->fetch(); }
$usuarios=$pdo->query('SELECT * FROM usuarios ORDER BY id DESC')->fetchAll();
?>
<h1>Cadastro de usuários</h1>
<p class="muted"></p>

<div class="grid-2">


    <section class="panel">
        <h2>Usuários</h2>
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Nome</th>
                        <th>E-mail</th>
                        <th>Perfil</th>
                        <th>Status</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($usuarios as $u): ?>
                        <tr>
                            <td><?= e($u['nome']) ?></td>
                            <td><?= e($u['email']) ?></td>
                            <td><?= e($u['perfil']) ?></td>
                            <td><?= $u['ativo'] ? 'Ativo' : 'Inativo' ?></td>
                            <td>
                                <a href="?editar=<?= (int)$u['id'] ?>">Editar</a>
                                <form method="post" class="inline">
                                    <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
                                    <input type="hidden" name="id" value="<?= (int)$u['id'] ?>">
                                    <button name="excluir" onclick="return confirm('Desativar usuário?')">Excluir</button>
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