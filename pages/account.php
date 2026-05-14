<?php
$storeName = 'MEDSTORE';
$basePath = '../';
$categories = require __DIR__ . '/../data/categories.php';
$pageTitle = 'Entrar | MEDSTORE';
$pageDescription = 'Área de login de clientes da loja de materiais hospitalares.';
require __DIR__ . '/../includes/header.php';
?>

<main id="main-content">
    <section class="page-hero page-hero--medical">
        <div class="container">
            <span class="badge">Área do cliente</span>
            <h1>Entrar na conta</h1>
            <p>Acesse pedidos, orçamentos, condições comerciais e compras recorrentes.</p>
        </div>
    </section>

    <section class="page-content container">
        <div class="auth-card">
            <div>
                <h2>Acesso de cliente</h2>
                <p class="muted">Modelo visual pronto para integração com autenticação real.</p>
            </div>
            <form class="form-grid" action="#" method="post">
                <label>E-mail
                    <input type="email" placeholder="compras@clinica.com.br" required>
                </label>
                <label>Senha
                    <input type="password" placeholder="Sua senha" required>
                </label>
                <button class="btn btn-red" type="submit">Entrar</button>
            </form>
        </div>
    </section>
</main>

<?php require __DIR__ . '/../includes/footer.php'; ?>
