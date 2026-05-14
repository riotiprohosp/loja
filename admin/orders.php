<?php
$adminTitle = 'Pedidos';
$activeAdmin = 'orders';
require __DIR__ . '/includes/admin-header.php';
$orders = [
    ['id' => '#1028', 'client' => 'Clínica Vida', 'date' => 'Hoje', 'status' => 'Pago', 'class' => 'paid', 'total' => 'R$ 640,80'],
    ['id' => '#1027', 'client' => 'Laboratório Norte', 'date' => 'Ontem', 'status' => 'Separação', 'class' => 'pending', 'total' => 'R$ 1.289,30'],
    ['id' => '#1026', 'client' => 'Consultório Oral', 'date' => '12/05', 'status' => 'Orçamento', 'class' => 'quote', 'total' => 'R$ 388,10'],
    ['id' => '#1025', 'client' => 'Hospital Central', 'date' => '11/05', 'status' => 'Pago', 'class' => 'paid', 'total' => 'R$ 2.140,00'],
];
?>
<main class="admin-content">
    <section class="admin-page-head">
        <div>
            <span>Comercial</span>
            <h1>Pedidos e orçamentos</h1>
            <p>Base para acompanhar compras, separação, pagamento e solicitação de orçamento.</p>
        </div>
    </section>
    <section class="admin-card">
        <div class="admin-table-wrap">
            <table class="admin-table">
                <thead><tr><th>Pedido</th><th>Cliente</th><th>Data</th><th>Status</th><th>Total</th></tr></thead>
                <tbody>
                    <?php foreach ($orders as $order): ?>
                        <tr><td><?= e($order['id']) ?></td><td><?= e($order['client']) ?></td><td><?= e($order['date']) ?></td><td><span class="status <?= e($order['class']) ?>"><?= e($order['status']) ?></span></td><td><?= e($order['total']) ?></td></tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </section>
</main>
<?php require __DIR__ . '/includes/admin-footer.php'; ?>
