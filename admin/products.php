<?php
$products = require __DIR__ . '/../data/products.php';
$adminTitle = 'Produtos';
$activeAdmin = 'products';
require __DIR__ . '/includes/admin-header.php';
?>
<main class="admin-content">
    <section class="admin-page-head">
        <div>
            <span>Catálogo</span>
            <h1>Produtos hospitalares</h1>
            <p>Modelo visual para listagem, edição e controle de estoque.</p>
        </div>
        <button class="btn btn-red" type="button">Novo produto</button>
    </section>

    <section class="admin-card">
        <div class="admin-toolbar">
            <input type="search" class="admin-search" placeholder="Buscar produto, marca ou categoria">
            <select><option>Todos os setores</option><option>EPIs</option><option>Equipamentos</option><option>Descartáveis</option></select>
        </div>
        <div class="admin-table-wrap">
            <table class="admin-table admin-products-table">
                <thead><tr><th>Produto</th><th>Categoria</th><th>Preço</th><th>Status</th><th>Ações</th></tr></thead>
                <tbody>
                    <?php foreach ($products as $product): ?>
                        <tr>
                            <td><div class="admin-product-cell"><img src="../<?= e($product['image']) ?>" alt=""><span><strong><?= e($product['name']) ?></strong><small><?= e($product['brand']) ?></small></span></div></td>
                            <td><?= e($product['category']) ?></td>
                            <td><?= e(moneyBR($product['price'])) ?></td>
                            <td><span class="status paid">Ativo</span></td>
                            <td><button class="admin-link-btn" type="button">Editar</button></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </section>
</main>
<?php require __DIR__ . '/includes/admin-footer.php'; ?>
