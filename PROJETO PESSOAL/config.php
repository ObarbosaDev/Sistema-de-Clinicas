<?php
// Definições de conexão ao banco de dados
define('HOST', 'localhost');   // Host do banco de dados
define('USER', 'root');        // Usuário do banco de dados
define('PASS', '');            // Senha do banco de dados
define('BASE', 'clinica');     // Nome do banco de dados

// Conexão com o banco de dados
$conn = new mysqli(HOST, USER, PASS, BASE);

// Verifica se houve erro na conexão
if ($conn->connect_error) {
    die("Erro na conexão com o banco de dados: " . $conn->connect_error);
}

// Ativa suporte a sessões (necessário para o sistema de login)
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Função para proteger entradas (evita injeção de SQL)
function protegerEntrada($input) {
    global $conn;
    return htmlspecialchars(mysqli_real_escape_string($conn, trim($input)));
}

// Função para redirecionar usuários não autenticados
function verificarLogin() {
    if (!isset($_SESSION['id_usuario'])) {
        header("Location: login.php");
        exit;
    }
}
?>