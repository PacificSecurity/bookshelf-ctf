<?php
require_once '../config.php';

// Verificar se o usuário está logado e é um editor
if (!isLoggedIn() || !isEditor()) {
    redirect('../login.php');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $comment_id = (int)$_POST['comment_id'];
    
    $response = sanitize_input($_POST['response']);
    
    // Verificar se o comentário pertence a um e-book deste editor
    $publisher_id = $_SESSION['user_id'];
    $sql = "SELECT c.id 
            FROM comments c 
            JOIN ebooks e ON c.ebook_id = e.id 
            WHERE c.id = $comment_id AND e.publisher_id = $publisher_id";
    $result = $conn->query($sql);
    
    if ($result && $result->num_rows > 0) {
        // Atualizar comentário com a resposta do editor (sanitizada)
        $sql = "UPDATE comments SET publisher_response = '$response' WHERE id = $comment_id";
        
        if ($conn->query($sql) === TRUE) {
            // Sucesso
            $_SESSION['message'] = 'Resposta enviada com sucesso!';
        } else {
            // Erro
            $_SESSION['error'] = 'Erro ao enviar resposta: ' . $conn->error;
        }
    } else {
        $_SESSION['error'] = 'Você não tem permissão para responder a este comentário.';
    }
    
    redirect('index.php');
}
?> 