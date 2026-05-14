<?php
$productImage = ($basePath ?? '') . $product['image'];
?>
<article class="product-card" data-category="<?= e($product['category']) ?>">
    <div class="product-image">
        <img src="<?= e($productImage) ?>" alt="<?= e($product['name']) ?>" loading="lazy">
        <span class="product-tag"><?= e($product['tag']) ?></span>
        <button class="quick-view" type="button"
            data-name="<?= e($product['name']) ?>"
            data-brand="<?= e($product['brand']) ?>"
            data-category="<?= e($product['category']) ?>"
            data-price="<?= e(moneyBR($product['price'])) ?>"
            data-old-price="<?= e(moneyBR($product['old_price'])) ?>"
            data-description="<?= e($product['description']) ?>"
            data-image="<?= e($productImage) ?>">
            Ver detalhes
        </button>
    </div>
    <div class="product-info">
        <div class="product-brand"><?= e($product['brand']) ?> • <?= e($product['category']) ?></div>
        <h3><?= e($product['name']) ?></h3>
        <div class="price-line">
            <span class="price"><?= e(moneyBR($product['price'])) ?></span>
            <span class="old-price"><?= e(moneyBR($product['old_price'])) ?></span>
        </div>
        <div class="product-actions">
            <button class="add-cart" type="button">Adicionar</button>
            <button class="favorite" type="button" aria-label="Adicionar aos favoritos">♡</button>
        </div>
    </div>
</article>
