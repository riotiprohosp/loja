<?php
$adminTitle = 'Configurações';
$activeAdmin = 'settings';
require __DIR__ . '/includes/admin-header.php';
?>
<main class="admin-content">
    <section class="admin-page-head">
        <div>
            <span>Preferências</span>
            <h1>Configurações da loja</h1>
            <p>Dados de atendimento, política comercial e preferências do catálogo.</p>
        </div>
    </section>

    <section class="admin-card">
        <form class="admin-form" action="#" method="post">
            <label>Nome da loja<input type="text" value="MEDSTORE"></label>
            <label>E-mail comercial<input type="email" value="comercial@medstore.com.br"></label>
            <label>WhatsApp<input type="text" value="(11) 99999-0000"></label>
            <label>Pedido mínimo<input type="text" value="R$ 150,00"></label>
            <label class="admin-form-full">Mensagem institucional<textarea>Materiais hospitalares para clínicas, consultórios e laboratórios com compra rápida e atendimento comercial.</textarea></label>
            <button class="btn btn-red" type="submit">Salvar configurações</button>
        </form>
    </section>
</main>
<?php require __DIR__ . '/includes/admin-footer.php'; ?>
