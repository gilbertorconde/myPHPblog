<?php
include('../core/init.inc.php');
include('login.func.php');
sec_session_start($mysqli); // Nossa segurança personalizada para iniciar uma sessão php. 
if(isset($_POST['email'], $_POST['password'])) { 
   $email = $_POST['email'];
   $password = $_POST['p']; // A senha em hash.
   if(login($email, $password, $mysqli) == true) {
      // Login com sucesso
      echo 'Sucesso: Você efetuou login.';
      if (isset($_SESSION['URI'])){
          header('Location: '.$_SESSION['URI']);
      }
      else {
          header('Location: ../blog_list.php');
      }
   } else {
      // Falha de login
      header('Location: ./login.php?error=1');
   }
} else { 
   // As variáveis POST corretas não foram enviadas para esta página.
   echo 'Requisição Inválida';
}
?>
