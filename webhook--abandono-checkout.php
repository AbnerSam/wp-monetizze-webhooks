<?php
/*! 
 * 2018
 * https://github.com/hmaesta/wp-monetizze-webhooks
 */
// ----------------------------------------------------
// ABANDONOU CHECKOUT
// ----------------------------------------------------

header($_SERVER["SERVER_PROTOCOL"]." 200 OK");
//ini_set('display_errors', 1);
//ini_set('display_startup_errors', 1);
//error_reporting(E_ALL);

header('Content-Type: text/html; charset=utf-8');
date_default_timezone_set('America/Sao_Paulo');

include('_monetizze-variaveis.php');
include('_monetizze-chaves.php');
include('_log.php');
include('_ip.php');
include('../wp-blog-header.php');

// log
registrar('---------- Novo registro ----------');
registrar('Abandono de checkout');
registrar('Conexão IP: ' . get_client_ip() );
registrar('Comprador: '. $nome_completo);
registrar('Email: '. $email);

// ----------------------------------------------------

$registros = array(
    "data_inicio_venda" => $data_inicio,
    "data_finalizacao_venda" => $data_finalizada,
    "data_registro" => $data_atual,
    "comprador_nome" => $nome_completo,
    "comprador_email" => $email,
    "comprador_nascimento" => $data_nascimento,
    //"comprador_cnpj_cpf" => $cnpj_cpf,
    "comprador_telefone" => $telefone,
    "comprador_cep" => $cep,
    //"comprador_bairro" => $bairro,
    "comprador_cidade" => $cidade,
    "comprador_estado" => $estado,
    //"comprador_wp_usuario" => $usuario,
    //"comprador_wp_id" => $insert_user,
    //"codigo_venda_transacao" => $codigo_da_transacao,
    //"forma_de_pagamento" => $forma_pagamento,
    //"meio_de_pagamento" => $meio_pagamento,
    //"garantia_restante" => $garantia_restante,
    "status_venda" => $status_venda,
    //"valor_venda" => $valor_venda,
    //"valor_recebido" => $valor_recebido,
    //"boleto_link" => $link_boleto,
    //"boleto_digitavel" => $linha_digitavel
);
$registrosJson = json_encode($registros);

// ----------------------------------------------------
// CADASTRO NO ZAPIER
// ----------------------------------------------------

// Transforma em json
$json = json_encode($registros, JSON_UNESCAPED_UNICODE);

// URL pra curl no Zapier
$urlZapier = "https://hooks.zapier.com/hooks/catch/__preencher__";

// curl
$ch = curl_init($urlZapier);
curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));

// Retorna resposta ao inves de imprimir na tela
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

// Envia request
$retorno = curl_exec($ch);
curl_close($ch);

// Retorna
if ($ch) {
    // Resposta do Zapier
    $statusJson = json_decode($retorno);
    $status['respostaZapier'] = $statusJson->{'status'};

    registrar('Registrado no Zapier');
} else {
    registrar('Erro ao registrar no Zapier', 'error');
}

// ----------------------------------------------------
// ENVIO DO E-MAIL
// ----------------------------------------------------

if( empty($primeiro_nome) || $primeiro_nome==='' ) {
    $complemento_titulo = "não deixe pra depois!";
} else {
    $complemento_titulo = $primeiro_nome;
}

$titulo = "Titulo do e-mail, ". $complemento_titulo;
$headers = array('Content-Type: text/html; charset=UTF-8');
$message =
    <<<HTML
<h1>Titulo do e-mail</h1>
<p>
Olá, {$primeiro_nome}. 
</p>
<p>Corpo do e-mail...</p>
HTML;

wp_mail($email, $titulo, $message, $headers);

registrar('Email enviado ao cliente');

echo "abandono-checkout - Executado ate o fim.";
http_response_code (200);
registrar( http_response_code() );


?>