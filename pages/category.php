<?php
$storeName = 'MEDSTORE';
$basePath = '../';
$products = require __DIR__ . '/../data/products.php';
$categories = require __DIR__ . '/../data/categories.php';
require_once __DIR__ . '/../includes/functions.php';

$query = trim($_GET['q'] ?? '');
$category = trim($_GET['category'] ?? '');
$brand = trim($_GET['brand'] ?? '');
$model = trim($_GET['model'] ?? '');

$filteredProducts = array_filter($products, function ($product) use ($query, $category, $brand, $model) {
    $haystack = mb_strtolower($product['name'] . ' ' . $product['brand'] . ' ' . $product['category'] . ' ' . $product['description']);

    if ($query !== '' && strpos($haystack, mb_strtolower($query)) === false) {
        return false;
    }

    if ($category !== '' && mb_strtolower($product['category']) !== mb_strtolower($category)) {
        return false;
    }

    if ($brand !== '' && mb_strtolower($product['brand']) !== mb_strtolower($brand)) {
        return false;
    }

    if ($model !== '' && strpos($haystack, mb_strtolower($model)) === false) {
        return false;
    }

    return true;
});

$pageTitle = 'Catálogo Hospitalar | MEDSTORE';
$pageDescription = 'Catálogo de materiais hospitalares, EPIs, descartáveis, curativos e equipamentos clínicos.';
require __DIR__ . '/../includes/header.php';
?>

<main id="main-content">
    <section class="page-hero page-hero--medical">
        <div class="container">
            <span class="badge">Catálogo hospitalar</span>
            <h1>Produtos para clínicas, laboratórios e consultórios</h1>
            <p>Encontre descartáveis, EPIs, curativos, equipamentos e materiais de higiene com navegação rápida.</p>
        </div>
    </section>

    <section class="section container">
        <div class="section-head">
            <div class="section-title">
                <span>Resultado da busca</span>
                <h2><?= count($filteredProducts) ?> produto(s) encontrado(s)</h2>
            </div>
            <div class="filter-bar">
                <a class="filter-btn active" href="category.php">Limpar filtros</a>
                <a class="filter-btn" href="category.php?category=Descartáveis">Descartáveis</a>
                <a class="filter-btn" href="category.php?category=EPIs">EPIs</a>
                <a class="filter-btn" href="category.php?category=Equipamentos">Equipamentos</a>
                <a class="filter-btn" href="category.php?category=Curativos">Curativos</a>
                <a class="filter-btn" href="category.php?category=Higiene">Higiene</a>
            </div>
        </div>

        <?php if (count($filteredProducts) > 0): ?>
            <div class="product-grid">
                <?php foreach ($filteredProducts as $product): ?>
                    <?php require __DIR__ . '/../includes/product-card.php'; ?>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="placeholder-card">
                <h2>Nenhum produto encontrado</h2>
                <p class="muted">Tente buscar por outro termo, setor ou categoria hospitalar.</p>
            </div>
        <?php endif; ?>
    </section>
</main>

<?php require __DIR__ . '/../includes/footer.php'; ?>
