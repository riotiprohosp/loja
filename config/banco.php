<?php
// Configure o acesso ao banco de dados MySQL/MariaDB.
$db_host = 'localhost';
$db_nome = 'loja';
$db_usuario = 'ti';
$db_senha = 'abcd1234';
$db_charset = 'utf8mb4';

$dsn = "mysql:host={$db_host};dbname={$db_nome};charset={$db_charset}";
$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES => false,
];

try {
    $pdo = new PDO($dsn, $db_usuario, $db_senha, $options);
} catch (PDOException $e) {
    http_response_code(500);
    exit('Erro ao conectar ao banco de dados. Verifique config/banco.php');
}
