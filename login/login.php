<?php
$error = "";
if(isset($_GET['error'])) { 
   $error = 'Erro: palavra passe errada, utilizador não existe ou excedeu o numero máximo de tentativas.';
}
?>
<!DOCTYPE html>
<!--[if lt IE 7]>      <html class="no-js lt-ie9 lt-ie8 lt-ie7"> <![endif]-->
  <!--[if IE 7]>         <html class="no-js lt-ie9 lt-ie8"> <![endif]-->
  <!--[if IE 8]>         <html class="no-js lt-ie9"> <![endif]-->
  <!--[if gt IE 8]><!-->

  <html class="no-js">
  <!--<![endif]-->
  <head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
  <title>Pagina de Login</title>
<script type="text/javascript" src="sha512.js"></script>
<script type="text/javascript" src="forms.js"></script>

  <style type="text/css">
  body
  {
    font-family:Arial, Helvetica, sans-serif;
    font-size:14px;
	
  }
label
{
  font-weight:bold;
	
width:100px;
  font-size:14px;
	
}
.box
{
border:#666666 solid 1px;
	
}
</style>
</head>
<body bgcolor="#FFFFFF">
  <div align="center">
  <div style="width:300px; border: solid 1px #333333; " align="left">
  <div style="background-color:#333333; color:#FFFFFF; padding:3px;"><b>Iniciar Sess&atilde;o</b></div>
  <div style="margin:30px">
<form action="process.login.php" method="post" name="login_form" >
   Email: <input type="text" name="email" /><br />
   Password: <input type="password" name="password" id="password"/><br />
   <input type="submit" name="submit" value="Login" onclick="formhash(this.form, this.form.password, 'p'); return true;" >
   <!-- <input type="button" value="Login" onclick="formhash(this.form, this.form.password, 'p'); this.form.submit();" /> -->
</form>
<div style="font-size:11px; color:#cc0000; margin-top:10px"><?php echo $error; ?></div>
  </div>
  </div>
  </div>
 </body>
</html>
