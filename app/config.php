<?php
// Configuração do banco de dados
$db_host = 'db';
$db_user = 'bookshelf_user';
$db_pass = 'bookshelf_pass';
$db_name = 'bookshelf';

// Conexão com o banco de dados
$conn = new mysqli($db_host, $db_user, $db_pass, $db_name);
if ($conn->connect_error) {
    die("Conexão falhou: " . $conn->connect_error);
}

// Funções auxiliares
function sanitize_input($data) {
    global $conn;
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $conn->real_escape_string($data);
}

// Início da sessão
session_start();

// Função para verificar se o usuário está logado
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

// Função para verificar o papel do usuário
function getUserRole() {
    return isset($_SESSION['role']) ? $_SESSION['role'] : '';
}

// Função para verificar se o usuário é editor
function isEditor() {
    return getUserRole() === 'editor';
}

// Função para verificar se o usuário é administrador
function isAdmin() {
    return getUserRole() === 'admin';
}

// Função de redirecionamento
function redirect($url) {
    header("Location: $url");
    exit;
} 