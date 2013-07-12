<?php
function sec_session_start($mysqli) {
    $session_name = 'sec_session_id'; // Define um nome padrão de sessão
    $secure = false; // Defina como true (verdadeiro) caso esteja utilizando https.
    $httponly = true; // Isto impede que o javascript seja capaz de acessar a id de sessão. 
 
    ini_set('session.use_only_cookies', 1); // Força as sessões a apenas utilizarem cookies. 
    $cookieParams = session_get_cookie_params(); // Recebe os parâmetros atuais dos cookies.
    session_set_cookie_params($cookieParams["lifetime"], $cookieParams["path"], $cookieParams["domain"], $secure, $httponly); 
    session_name($session_name); // Define o nome da sessão como sendo o acima definido.
    session_start(); // Inicia a sessão php.
    session_regenerate_id(true); // regenerada a sessão, deleta a outra.
    makeSessionVariables($mysqli);
}

function passwd_verify($password, $email, $mysqli){
	if ($stmt = $mysqli->prepare("SELECT id, username, password, salt FROM members WHERE email = ? LIMIT 1")) { 
        $stmt->bind_param('s', $email); // Vincula "$email" ao parâmetro.
        $stmt->execute(); // Executa a query preparada.
        $stmt->store_result();
        $stmt->bind_result($user_id, $username, $db_password, $salt); // obtém variáveis do resultado.
        $stmt->fetch();
        $password = hash('sha512', $password.$salt); // confere o hash de "$password" e "$salt"
		if($stmt->num_rows == 1) { // se o usuário existe
		    // Nós checamos se a conta está bloqueada devido a várias tentativas de login
			if($db_password == $password) {
				return true;
			}
		}
	}
	return false;
}

function login($email, $password, $mysqli) {
    // utilizar declarações preparadas significa que a injeção de código SQL não será possível. 
    if ($stmt = $mysqli->prepare("SELECT id, username, password, salt FROM members WHERE email = ? LIMIT 1")) { 
        $stmt->bind_param('s', $email); // Vincula "$email" ao parâmetro.
        $stmt->execute(); // Executa a query preparada.
        $stmt->store_result();
        $stmt->bind_result($user_id, $username, $db_password, $salt); // obtém variáveis do resultado.
        $stmt->fetch();
        $password = hash('sha512', $password.$salt); // confere o hash de "$password" e "$salt"
        if($stmt->num_rows == 1) { // se o usuário existe
            // Nós checamos se a conta está bloqueada devido a várias tentativas de login
            if(checkbrute($user_id, $mysqli) == true) { 
                // Conta está bloqueada
                // Envia um email ao usuário comunicando que sua conta foi bloqueada
                return false;
            } else {
		echo $db_password."<br />".$password;
                if($db_password == $password) { // Checa se a senha na base de dados confere com a senha que o usuário digitou.
                    // Senha está correta!
 
                    $ip_address = $_SERVER['REMOTE_ADDR']; // Pega o endereço IP do usuário. 
                    $user_browser = $_SERVER['HTTP_USER_AGENT']; // Pega a string de agente do usuário.
 
                    $user_id = preg_replace("/[^0-9]+/", "", $user_id); // Proteção XSS conforme poderíamos imprimir este valor
                    $_SESSION['user_id'] = $user_id; 
                    $username = preg_replace("/[^a-zA-Z0-9_\-]+/", "", $username); // Proteção XSS conforme poderíamos imprimir este valor
                    $_SESSION['username'] = $username;
		    $_SESSION['email'] = $email;
                    $_SESSION['login_string'] = hash('sha512', $password.$ip_address.$user_browser);
		    $mysqli->query("DELETE FROM `login_attempts` WHERE `user_id` = '$user_id'");
                    // Login com sucesso.
                    return true;    
                } else {
                    // Senha não está correta
                    // Nós armazenamos esta tentativa na base de dados
                    $now = time();
                    $mysqli->query("INSERT INTO login_attempts (user_id, time) VALUES ('$user_id', '$now')");
                    return false;
                }
            }
        } else {
            // Nenhum usuário existe. 
            return false;
        }
    }
}

function checkbrute($user_id, $mysqli) {
    // Retorna a data atual
    $now = time();
    // Todas as tentativas de login são contadas pelas 2 últimas horas. 
    $valid_attempts = $now - (2 * 60 * 60); 
 
    if ($stmt = $mysqli->prepare("SELECT time FROM login_attempts WHERE user_id = ? AND time > '$valid_attempts'")) { 
        $stmt->bind_param('i', $user_id); 
        // Executa a query preparada.
        $stmt->execute();
        $stmt->store_result();
        // Se houver mais de 5 tentativas falhas de login
        if($stmt->num_rows > 5) {
            return true;
	    //return false;
        } else {
            return false;
        }
    }
}

function login_check($mysqli) {
   // Verifica se todas as variáveis das sessões foram definidas
   if(isset($_SESSION['user_id'], $_SESSION['username'], $_SESSION['login_string'])) {
     $user_id = $_SESSION['user_id'];
     $login_string = $_SESSION['login_string'];
     $username = $_SESSION['username'];
     $ip_address = $_SERVER['REMOTE_ADDR']; // Pega o endereço IP do usuário 
     $user_browser = $_SERVER['HTTP_USER_AGENT']; // Pega a string do usuário.
 
     if ($stmt = $mysqli->prepare("SELECT password FROM members WHERE id = ? LIMIT 1")) { 
        $stmt->bind_param('i', $user_id); // Atribui "$user_id" ao parâmetro
        $stmt->execute(); // Executa a tarefa atribuía
        $stmt->store_result();
 
        if($stmt->num_rows == 1) { // Caso o usuário exista
           $stmt->bind_result($password); // pega variáveis a partir do resultado
           $stmt->fetch();
           $login_check = hash('sha512', $password.$ip_address.$user_browser);
           if($login_check == $login_string) {
              // Logado!!!
              return true;
           } else {
              // Não foi logado
              return false;
           }
        } else {
            // Não foi logado
            return false;
        }
     } else {
        // Não foi logado
        return false;
     }
   } else {
     // Não foi logado
     return false;
   }
}
?>
