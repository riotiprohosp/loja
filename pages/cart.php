<?php
$storeName = 'MEDSTORE';
$basePath = '../';
$categories = require __DIR__ . '/../data/categories.php';
$pageTitle = 'Carrinho | MEDSTORE';
$pageDescription = 'Página de carrinho para loja de materiais hospitalares.';
require __DIR__ . '/../includes/header.php';
?>

<main id="main-content">
    <section class="page-hero page-hero--medical">
        <div class="container">
            <span class="badge">Pedido institucional</span>
            <h1>Carrinho</h1>
            <p>Estrutura pronta para integração com sessão PHP, orçamento, estoque e checkout.</p>
        </div>
    </section>

    <section class="page-content container">
        <div class="placeholder-card">
            <h2>Seu carrinho visual está ativo</h2>
            <p class="muted">Neste template, o contador do carrinho usa localStorage via JavaScript. Para produção completa, integre com sessão PHP, banco de dados, estoque e checkout seguro.</p>
            <br>
            <a class="btn btn-red" href="../index.php#products">Continuar comprando</a>
        </div>
    </section>
</main>

<?php require __DIR__ . '/../includes/footer.php'; ?>
