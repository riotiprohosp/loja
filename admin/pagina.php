<?php
require_once __DIR__ . '/includes/admin-header.php';
exigir_perfil(['administrador','gerencial']);

$menuColumns = $pdo->query('SHOW COLUMNS FROM menus')->fetchAll(PDO::FETCH_COLUMN);
if (!in_array('parent_id', $menuColumns, true)) {
    $pdo->exec('ALTER TABLE menus ADD COLUMN parent_id INT NULL AFTER id, ADD INDEX idx_menus_parent (parent_id)');
}
if (!in_array('subordem', $menuColumns, true)) {
    $pdo->exec('ALTER TABLE menus ADD COLUMN subordem INT NOT NULL DEFAULT 1 AFTER ordem');
}

$editarMenu = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    validar_csrf();

    if (isset($_POST['salvar_ajustes'])) {
        $logoPath = $_POST['logo_atual'] ?? '';
        if (!empty($_FILES['logo']['name'])) {
            $ext = strtolower(pathinfo($_FILES['logo']['name'], PATHINFO_EXTENSION));
            if (in_array($ext, ['png','jpg','jpeg','webp','svg'], true)) {
                $nome = 'logo-' . time() . '.' . $ext;
                $destino = __DIR__ . '/../assets/img/' . $nome;
                if (move_uploaded_file($_FILES['logo']['tmp_name'], $destino)) {
                    chmod($destino, 0644);
                    $logoPath = 'assets/img/' . $nome;
                }
            }
        }

        $dados = [
            'nome_loja' => $_POST['nome_loja'],
            'titulo_pagina' => $_POST['titulo_pagina'],
            'contato_topo' => $_POST['contato_topo'],
            'contato_footer' => $_POST['contato_footer'],
            'texto_footer' => $_POST['texto_footer'],
            'logo' => $logoPath,
        ];

        foreach ($dados as $chave => $valor) {
            $pdo->prepare('INSERT INTO ajustes_pagina (chave,valor) VALUES (?,?) ON DUPLICATE KEY UPDATE valor=VALUES(valor)')->execute([$chave, $valor]);
        }
        registrar_log($pdo, 'Ajuste de página', 'Atualizou identidade e contatos');
    }

    if (isset($_POST['novo_menu']) || isset($_POST['salvar_menu'])) {
        $id = (int)($_POST['id'] ?? 0);
        $parentId = (int)($_POST['parent_id'] ?? 0);
        if ($parentId === $id) {
            $parentId = 0;
        }
        $parentValue = $parentId > 0 ? $parentId : null;
        $titulo = trim($_POST['titulo'] ?? '');
        $url = trim($_POST['url'] ?? '');
        $ordem = (int)($_POST['ordem'] ?? 1);
        $subordem = (int)($_POST['subordem'] ?? 1);

        if ($titulo !== '') {
            if (isset($_POST['salvar_menu']) && $id > 0) {
                $pdo->prepare('UPDATE menus SET parent_id=?, titulo=?, url=?, ordem=?, subordem=? WHERE id=?')->execute([$parentValue, $titulo, $url, $ordem, $subordem, $id]);
                registrar_log($pdo, 'Menus', 'Editou menu ID ' . $id);
            } else {
                $pdo->prepare('INSERT INTO menus (parent_id,titulo,url,ordem,subordem,ativo) VALUES (?,?,?,?,?,1)')->execute([$parentValue, $titulo, $url, $ordem, $subordem]);
                registrar_log($pdo, 'Menus', 'Criou menu ' . $titulo);
            }
        }
    }

    if (isset($_POST['excluir_menu'])) {
        $id = (int)$_POST['id'];
        $pdo->prepare('UPDATE menus SET parent_id=NULL WHERE parent_id=?')->execute([$id]);
        $pdo->prepare('DELETE FROM menus WHERE id=?')->execute([$id]);
        registrar_log($pdo, 'Menus', 'Excluiu menu ID ' . $id);
    }
}

if (isset($_GET['editar_menu'])) {
    $stmt = $pdo->prepare('SELECT * FROM menus WHERE id=?');
    $stmt->execute([(int)$_GET['editar_menu']]);
    $editarMenu = $stmt->fetch() ?: null;
}

$aj = buscar_ajustes($pdo);
$menus = $pdo->query('SELECT * FROM menus ORDER BY COALESCE(parent_id,id), parent_id IS NOT NULL, ordem, subordem, id')->fetchAll();
$menusPais = $pdo->query('SELECT id,titulo FROM menus WHERE parent_id IS NULL ORDER BY ordem,id')->fetchAll();
?>
<h1>Ajustes de Layout</h1>
<div class="grid-2">
  <section class="panel">
    <h2>Identidade</h2>
    <form method="post" enctype="multipart/form-data" class="form-grid">
      <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
      <input type="hidden" name="logo_atual" value="<?= e($aj['logo'] ?? '') ?>">
      <label>Nome da loja<input name="nome_loja" value="<?= e($aj['nome_loja'] ?? 'PROHOSP') ?>"></label>
      <label>Título da página<input name="titulo_pagina" value="<?= e($aj['titulo_pagina'] ?? 'PROHOSP') ?>"></label>
      <label>Logo da página<input type="file" name="logo"></label>
      <label>Contato topo<input name="contato_topo" value="<?= e($aj['contato_topo'] ?? '') ?>"></label>
      <label>Contato footer(rodapé)<input name="contato_footer" value="<?= e($aj['contato_footer'] ?? '') ?>"></label>
      <label class="wide">Texto footer(rodapé)<textarea name="texto_footer"><?= e($aj['texto_footer'] ?? '') ?></textarea></label>
      <button class="btn primary" name="salvar_ajustes">Salvar ajustes</button>
    </form>
  </section>

  <section class="panel">
    <h2>Menus Index</h2>
    <form method="post" class="form-grid compact">
      <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
      <?php if ($editarMenu): ?><input type="hidden" name="id" value="<?= (int)$editarMenu['id'] ?>"><?php endif; ?>
      <label>Título<input name="titulo" required value="<?= e($editarMenu['titulo'] ?? '') ?>"></label>
      <label>Aponta para página/item<input name="url" placeholder="index.php#produtos" value="<?= e($editarMenu['url'] ?? '') ?>"></label>
      <label>Menu pai
        <select name="parent_id">
          <option value="0">Menu principal</option>
          <?php foreach ($menusPais as $pai): ?>
            <?php if (!$editarMenu || (int)$pai['id'] !== (int)$editarMenu['id']): ?>
              <option value="<?= (int)$pai['id'] ?>" <?= (int)($editarMenu['parent_id'] ?? 0) === (int)$pai['id'] ? 'selected' : '' ?>><?= e($pai['titulo']) ?></option>
            <?php endif; ?>
          <?php endforeach; ?>
        </select>
      </label>
      <label>Ordem<input name="ordem" type="number" value="<?= e($editarMenu['ordem'] ?? '1') ?>"></label>
      <label>Subordem<input name="subordem" type="number" value="<?= e($editarMenu['subordem'] ?? '1') ?>"></label>
      <button class="btn primary" name="<?= $editarMenu ? 'salvar_menu' : 'novo_menu' ?>"><?= $editarMenu ? 'Salvar menu' : 'Criar menu' ?></button>
      <?php if ($editarMenu): ?><a class="link" href="pagina.php">Cancelar edição</a><?php endif; ?>
    </form>

    <div class="table-wrap">
      <table>
        <thead><tr><th>Título</th><th>URL</th><th>Ordem</th><th>Subordem</th><th>Tipo</th><th></th></tr></thead>
        <tbody>
          <?php foreach ($menus as $m): ?>
          <tr>
            <td><?= $m['parent_id'] ? '&nbsp;&nbsp;↳ ' : '' ?><?= e($m['titulo']) ?></td>
            <td><?= e($m['url']) ?></td>
            <td><?= (int)$m['ordem'] ?></td>
            <td><?= (int)$m['subordem'] ?></td>
            <td><?= $m['parent_id'] ? 'Submenu' : 'Menu' ?></td>
            <td>
              <a href="?editar_menu=<?= (int)$m['id'] ?>">Editar</a>
              <form method="post" class="inline">
                <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
                <input type="hidden" name="id" value="<?= (int)$m['id'] ?>">
                <button name="excluir_menu" onclick="return confirm('Excluir este menu?')">Excluir</button>
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
