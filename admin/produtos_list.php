<?php
require_once __DIR__ . '/includes/admin-header.php';

/**
 * Lógica para salvar a imagem do produto no diretório definido
 */
function salvar_imagem_produto(): string
{
    if (empty($_FILES['imagem']['name'])) {
        return '';
    }

    $ext = strtolower(pathinfo($_FILES['imagem']['name'], PATHINFO_EXTENSION));
    $permitidas = ['jpg', 'jpeg', 'png', 'webp', 'svg'];

    if (!in_array($ext, $permitidas, true)) {
        return '';
    }

    if (!is_dir(UPLOAD_PROD_DIR)) {
        mkdir(UPLOAD_PROD_DIR, 0755, true);
    }

    $nome = 'prod-' . date('YmdHis') . '-' . bin2hex(random_bytes(4)) . '.' . $ext;
    move_uploaded_file($_FILES['imagem']['tmp_name'], UPLOAD_PROD_DIR . $nome);

    return UPLOAD_PROD_URL . $nome;
}

/**
 * Processamento do Formulário (POST)
 */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    validar_csrf();

    if (isset($_POST['excluir'])) {
        // Desativação (Soft Delete)
        $pdo->prepare('UPDATE produtos SET ativo=0 WHERE id=?')->execute([$_POST['id']]);
        registrar_log($pdo, 'Produtos', 'Desativou produto ID ' . $_POST['id']);
        } else {
        $imagem = salvar_imagem_produto();
        $id = $_POST['id'] ?? '';

        // Normalize checkboxes/values
        $aviso = isset($_POST['aviso']) ? 1 : 0;
        $promocao_val = isset($_POST['promocao_ativa']) ? floatval($_POST['promocao'] ?? 0) : 0;
        $status = $_POST['status'] ?? 'Ativo';

        if ($id) {
            // Edição de Produto Existente
            $sql = 'UPDATE produtos SET codigo=?, codigo_interno=?, descricao=?, tipo=?, categoria=?, preco=?, status=?, aviso=?, promocao=?, ativo=?' . ($imagem ? ', imagem=?' : '') . ' WHERE id=?';
            
            $params = [
                $_POST['codigo'],
                $_POST['codigo_interno'],
                $_POST['descricao'],
                $_POST['tipo'],
                $_POST['categoria'],
                $_POST['preco'],
                $status,
                $aviso,
                $promocao_val,
                isset($_POST['ativo']) ? 1 : 0
            ];

            if ($imagem) $params[] = $imagem;
            $params[] = $id;

            $pdo->prepare($sql)->execute($params);
            registrar_log($pdo, 'Produtos', 'Atualizou produto ID ' . $id);
        } else {
            // Inserção de Novo Produto
            $pdo->prepare('INSERT INTO produtos (codigo, codigo_interno, descricao, tipo, categoria, preco, status, aviso, promocao, imagem, ativo, criado_em) VALUES (?,?,?,?,?,?,?,?,?,?,?,NOW())')
                ->execute([
                    $_POST['codigo'],
                    $_POST['codigo_interno'],
                    $_POST['descricao'],
                    $_POST['tipo'],
                    $_POST['categoria'],
                    $_POST['preco'],
                    $status,
                    $aviso,
                    $promocao_val,
                    $imagem,
                    isset($_POST['ativo']) ? 1 : 0
                ]);
            registrar_log($pdo, 'Produtos', 'Criou produto ' . $_POST['descricao']);
        }
    }
}

// Lógica de busca para edição
$editar = null;
if (isset($_GET['editar'])) {
    $st = $pdo->prepare('SELECT * FROM produtos WHERE id=?');
    $st->execute([$_GET['editar']]);
    $editar = $st->fetch();
}

// Listagem geral
$produtos = $pdo->query('SELECT * FROM produtos ORDER BY id DESC')->fetchAll();
$tipos = ['UN', 'CX', 'PC', 'BS'];
$cats = ['Medicamento', 'Material hospitalar'];
?>

<h1>Cadastro de produtos</h1>
<p></p>
<div style="margin-bottom:20px;">
    <button type="button" class="btn primary open-product-modal">Novo produto</button>
</div>

<section class="panel">
    <h2>Produtos</h2>
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Cód.</th>
                    <th>Descrição</th>
                    <th>Tipo</th>
                    <th>Categoria</th>
                    <th>Preço</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($produtos as $p): ?>
                    <tr>
                        <td><?= e($p['codigo']) ?></td>
                        <td><?= e($p['descricao']) ?></td>
                        <td><?= e($p['tipo']) ?></td>
                        <td><?= e($p['categoria']) ?></td>
                        <td><?= moeda($p['preco']) ?></td>
                        <td>
                            <a href="?editar=<?= (int)$p['id'] ?>">Editar</a>
                            <form method="post" class="inline">
                                <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
                                <input type="hidden" name="id" value="<?= (int)$p['id'] ?>">
                                <button name="excluir" onclick="return confirm('Confirmar exclusão?')">Excluir</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</section>

<div class="admin-modal<?= $editar ? ' show' : '' ?>" id="productModal" data-auto-open="<?= $editar ? 'true' : 'false' ?>" aria-hidden="<?= $editar ? 'false' : 'true' ?>">
    <div class="admin-modal-backdrop"></div>
    <div class="admin-modal-dialog">
        <div class="admin-modal-header">
            <h2><?= $editar ? 'Editar' : 'Novo' ?> produto</h2>
            <button type="button" class="modal-close" aria-label="Fechar">×</button>
        </div>

        <section class="panel">
            <form method="post" enctype="multipart/form-data" class="form-grid">
                <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
                <input type="hidden" name="id" value="<?= e($editar['id'] ?? '') ?>">

                <label>
                    Código
                    <input name="codigo" required value="<?= e($editar['codigo'] ?? '') ?>">
                </label>

                <label>
                    Cód. interno
                    <input name="codigo_interno" required value="<?= e($editar['codigo_interno'] ?? '') ?>">
                </label>

                <label class="wide">
                    Descrição
                    <input name="descricao" required value="<?= e($editar['descricao'] ?? '') ?>">
                </label>

                <label>
                    Tipo
                    <select name="tipo">
                        <?php foreach ($tipos as $t): ?>
                            <option <?= ($editar['tipo'] ?? '') === $t ? 'selected' : '' ?>><?= $t ?></option>
                        <?php endforeach; ?>
                    </select>
                </label>

                <label>
                    Categoria
                    <select name="categoria">
                        <?php foreach ($cats as $c): ?>
                            <option <?= ($editar['categoria'] ?? '') === $c ? 'selected' : '' ?>><?= $c ?></option>
                        <?php endforeach; ?>
                    </select>
                </label>

                    <label>
                        Preço
                        <input type="number" step="0.01" name="preco" value="<?= e($editar['preco'] ?? '0.00') ?>">
                    </label>

                    <label>
                        Status
                        <select name="status">
                            <option value="Ativo" <?= ($editar['status'] ?? '') === 'Ativo' ? 'selected' : '' ?>>Ativo</option>
                            <option value="Inativo" <?= ($editar['status'] ?? '') === 'Inativo' ? 'selected' : '' ?>>Inativo</option>
                        </select>
                    </label>

                    <label class="check">
                        <input type="checkbox" name="aviso" value="1" <?= !empty($editar['aviso']) ? 'checked' : '' ?>> Aviso
                    </label>

                    <label class="check">
                        <input type="checkbox" name="promocao_ativa" value="1" <?= !empty($editar['promocao']) ? 'checked' : '' ?>> Promoção
                    </label>

                    <label>
                        Valor promocional
                        <input type="number" step="0.01" name="promocao" value="<?= e($editar['promocao'] ?? '0.00') ?>" <?= empty($editar['promocao']) ? 'disabled' : '' ?>>
                    </label>

                <label>
                    Imagem
                    <input type="file" name="imagem" accept=".jpg,.jpeg,.png,.webp,.svg">
                </label>

                <label class="check">
                    <input type="checkbox" name="ativo" <?= ($editar['ativo'] ?? 1) ? 'checked' : '' ?>> Ativo
                </label>

                <button class="btn primary">Salvar produto</button>
            </form>
        </section>
    </div>
</div>

<?php require_once __DIR__ . '/includes/admin-footer.php'; ?>