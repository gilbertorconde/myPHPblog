function formhash(form, password, name) {
   // Cria um novo elemento de entrada, como um campo de entrada de senha sem hash.
   var p = document.createElement("input");
   // Adiciona o novo elemento ao nosso formulário.
   form.appendChild(p);
   p.name = name;
   p.type = "hidden";
   p.value = hex_sha512(password.value);
   // Certifica que senhas em texto plano não sejam enviadas.
   password.value = "NADA";
   // Finalmente, submete o formulário.
}
