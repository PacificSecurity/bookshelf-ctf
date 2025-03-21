<?php
require_once 'config.php';

// Limpar dados da sessão
session_unset();
session_destroy();

// Redirecionar para a página inicial
redirect('index.php');
?> 