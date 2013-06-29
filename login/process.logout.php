<?php
include('login.func.php');
sec_session_start();
// Zera todos os valores da sessão
$_SESSION = array();
// Pega os parâmetros da sessão 
$params = session_get_cookie_params();
// Deleta o cookie atual.
setcookie(session_name(), '', time() - 42000, $params["path"], $params["domain"], $params["secure"], $params["httponly"]);
// Destrói a sessão
session_destroy();
header('Location: ../blog_list.php');
?>
