<?php

include('core/init.inc.php');
include('login/login.func.php');
sec_session_start($mysqli);

if(login_check($mysqli) == true) {

} else {
    $_SESSION['URI'] =  curPageURL();
    header("Location: login/login.php");
    die('Você não está autorizado a acessar esta página. Por favor, efetue login. <br/>');
}

$message = "";

if(isset($_POST['newpwd'], $_POST['oldpwd'])){

    if (passwd_verify($_POST['p'], $_SESSION['email'], $mysqli)){
        $password = $_POST['po']; 
        // Cria um salt randômico
        $random_salt = hash('sha512', uniqid(mt_rand(1, mt_getrandmax()), true));
        // Cria uma senha pós hash (Cuidado para não re-escrever)
        $password = hash('sha512', $password.$random_salt);
        // Adicione sua inserção ao script de base de dados aqui 
        // Certifique-se de utilizar declarações preparadas
        $query = "UPDATE `members` SET `password` = '".$password."', `salt` = '".$random_salt."' WHERE `members`.`id` = '".$_SESSION['user_id']."'";
        if ($insert_stmt = $mysqli->prepare($query)) {    
            $insert_stmt->execute();
            $message = "<h3 style=\"color: green;\">Palavra passe alterada.</h3>";
        }
    }
    else{
        $message = "<h3 style=\"color: red;\">Palavra passe introduzida não corresponde ao utilizador atual.</h3>";
    }
    
}
if (isset($_POST['about'], $_POST['userid'])){
	change_about($_POST['userid'], $_POST['about'], $mysqli);
	$message = "<h3 style=\"color: green;\">Sobre ".$_SESSION['username']." alterado.</h3>";
}

echo '<!DOCTYPE HTML>

<html>'; 

include('resources/head.php');

echo 
'
<body>
<div id="main">
';
include('resources/github_ribbons.php');
$current = 'user';
include('resources/header.php');

echo
'
<div id="site_content">
';

include('resources/sidebar.php');

echo
'
<div id="content">
<article>
<header>
<h1>Alterar dados de conta</h1>
'.$message.'
';

echo 
'<form action="" method="post" name="change_pass_form" >
<fieldset>
<legend>Palavra passe de '.$_SESSION['username'].':</legend>
<p style="text-align: left;">
<label for="oldpwd">Palavra passe atual</label>
<input type="password" name="oldpwd" id="oldpwd" required />
</p>
<p style="text-align: left;">
<label for="newpwd">Nova palavra passe</label>
<input type="password" name="newpwd" id="newpwd" required pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z])\w{6,}"
onchange=" this.setCustomValidity(this.validity.patternMismatch ? 
\'Password deve conter pelo menos 6 caracteres, incluindo MAIÚSCULAS/minúsculas e numero(s)\' : \'\');
if(this.checkValidity()) form.reppwd.pattern = this.value;" />
</p>
<p style="text-align: left;">
<label for="reppwd">Repetir a nova palavra passe</label>
<input type="password" name="reppwd" id="reppwd" required pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z])\w{6,}"
onchange=" this.setCustomValidity(this.validity.patternMismatch ? 
\'Introduza a mesma password que colocou acima\' : \'\');" />
</p>
<p>
<input type="submit" value="Enviar" onclick="sendForm(this.form, this.form.oldpwd, this.form.newpwd);" />
</p>
</fieldset>
</form>';

$user = get_user($_SESSION['user_id'], $mysqli);

echo
'<br />
<form action="" method="post" name="change_about_form" >
<fieldset>
<legend>Sobre '.$_SESSION['username'].':</legend>
<p>
<textarea name="about" id="about" >'.$user['about'].'</textarea>
<input type="hidden" name="userid" id="userid" value="'.$_SESSION['user_id'].'" />
</p>
<p>
<input type="submit" value="Enviar" />
</p>
</fieldset>
</form>
</header>
</article>
';

include('tinyMCE/init.php');
echo
'
<script type="text/javascript" src="login/sha512.js"></script>
<script type="text/javascript" src="login/forms.js"></script>
<script>
function sendForm(form, field1, field2) {
aux1 = field1.value;
aux2 = field2.value;
formhash(form, field1, \'p\');
formhash(form, field2, \'po\');
field1.value = aux1;
field2.value = aux2;
form.submit();
return true;
}
</script>
';

echo
'</div>
</div>
';
include('resources/footer.php');
echo
'
</div>
</body>
</html>';

?>
