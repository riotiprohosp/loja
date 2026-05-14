<?php
require_once __DIR__ . '/../config/banco.php';
require_once __DIR__ . '/../includes/funcoes.php';
iniciar_sessao();
registrar_log($pdo, 'Logout', 'Usuário saiu do painel');
session_destroy();
header('Location: login.php');
