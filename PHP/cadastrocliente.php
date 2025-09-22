<?php
// Conectando este arquivo ao banco de dados
require_once __DIR__ . "/conexao.php";

// função para capturar os dados passados de uma página a outra
function redirecWith($url,$params=[]){
    // verifica se os parametros nao vieram vazios
    if(!empty($params)){
        // separar os parametros em espaços diferentes
    $qs= http_build_query($params);
    $sep = (strpos($url,'?') === false) ? '?' : '&';
    $url .= $sep . $qs;
}
// joga a url para o cabeçalho no navegador
header(Location: $url);
// fecha o script
exit;
}

// capturando os dados e jogando em variaveis
try{
    // SE O METODO DE ENVIO FOR DIFERENTE DE POST
    if($_SERVER['REQUEST_METHOD'] != 'POST'){
        // VOLTAR À TELA DE CADASTRO E EXIBIR ERRO
        redirecWith("../paginas/cadastro.html",
        ["erro"=> "Metodo inválido"]);
    }
    // jogando os dados dentro de variáveis
    $nome = $_POST["nome"];
    $email = $_POST["email"];
    $senha = $_POST["senha"];
    $telefone = $_POST["telefone"];
    $confirmarsenha = $_POST["confirmar"];

    // VALIDANDO OS CAMPOS
    // criar uma variável para receber os erros de validação
    $erros_validacao=[];
    if($nome === "" || $email === "" || $senha === "" || $telefone === "" || $confirmarsenha === ""){
        $erros_validacao[]="Preencha todos os campos";
    }
     // validação para verificar se o email tem o @
     if(!filter_var($email, FILTER_VALIDATE_EMAIL)){
        $erros_validacao[]= "E-mail inválido";
}
// verificar se senha e confirmar senha são diferentes
if($senha !== $confirmarsenha){
    $erros_validacao[]= "As senhas não conferem";
}
// verificar se a senha tem mais de 8 dígitos
if(strlen($senha)<8){
    $erros_validacao[]= "A senha deve ter pelo menos 8 caracteres";
} 
// verificar se o telefone tem pelo menos 11 dígitos
if(strlen($telefone)<11){
    $erros_validacao[]= "Telefone incorreto";
}
// agora é enviar os erros para a tela de cadastro
if($erros_validacao){
    redirecWith("../paginas/cadastro.html",
    ["erro" => urlencode($erros_validacao[0])]);
}
}

// verificar se o cpf ja foi cadastrado no banco de dados
$stmt = $pdo->prepare("SELECT * From Cliente
 Where cpf= :cpf LIMIT 1");
// joga o cpf digitado dentro do banco de dados
$stmt ->execute([':cpf =>$cpf']);
if($stmt->fetch()){
    redirecWith("../paginas/cadastro.html",
    ["erro" => urldecode("CPF já cadastrado")]);
}

?>