<?php require_once __DIR__ . '/includes/header.php';
$where = 'WHERE ativo = 1';
$params = [];
if (!empty($_GET['busca'])) {
    $where .= ' AND (descricao LIKE ? OR codigo LIKE ? OR codigo_interno LIKE ?)';
    $b = '%' . $_GET['busca'] . '%';
    $params = [$b, $b, $b];
}
$stmt = $pdo->prepare("SELECT * FROM produtos $where ORDER BY id DESC LIMIT 24");
$stmt->execute($params);
$produtos = $stmt->fetchAll();
?>
<main>
  <section class="hero-modern">
    <div class="container hero-grid">
      <div class="hero-copy">
        <span class="eyebrow">Distribidora de materiais hospitalares</span>
        <h1>Materiais hospitalares com compra rápida, controle e confiança.</h1>
        <div class="hero-actions"><a class="btn primary" href="#produtos">Compre agora</a></div>
      </div>
      <div class="hero-panel"><div class="stat"><b>24h*</b><span>Entrega até 24h úteis</span></div><div class="stat"><b>Físico</b><span>entrega confiável</span></div><div class="stat"><b>Referência</b><span>Com anos de consistência</span></div></div>
    </div>
  </section>
  <section class="container trust-row"><div>Envio rápido</div><div>Pagamento seguro</div><div>Compra assistida</div><div>Suporte especializado</div></section>
  <section class="container section" id="produtos">
    <div class="section-head"><div><span class="eyebrow red">Catálogo</span><h2>Produtos hospitalares</h2></div></div>
    <div class="product-grid">
      <?php foreach ($produtos as $p): ?>
      <article class="product-card" data-id="<?= (int)$p['id'] ?>">
        <div class="product-img"><img loading="lazy" src="<?= e(url_base($p['imagem'] ?: 'assets/img/placeholder-med.svg')) ?>" alt="<?= e($p['descricao']) ?>"></div>
        <div class="product-body">
          <small><?= e($p['categoria']) ?> • <?= e($p['tipo']) ?></small>
          <h3><?= e($p['descricao']) ?></h3>
          <p>Cód. <?= e($p['codigo']) ?> | Interno <?= e($p['codigo_interno']) ?></p>
          <div class="price-row"><strong><?= moeda($p['preco']) ?></strong><button type="button" class="add-cart">Comprar</button></div>
        </div>
      </article>
      <?php endforeach; ?>
    </div>
  </section>
</main>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
