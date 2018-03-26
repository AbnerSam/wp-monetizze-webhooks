<?php
/*! 
 * 2018
 * https://github.com/hmaesta/wp-monetizze-webhooks
 */
// ----------------------------------------------------
// COMPRA CANCELADA
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
registrar('Compra cancelada');
registrar('Conexão IP: ' . get_client_ip() );
registrar('Comprador: '. $nome_completo);
registrar('Email: '. $email);


// ----------------------------------------------------
// ENVIO DO E-MAIL
// ----------------------------------------------------

$headers = array('Content-Type: text/html; charset=UTF-8');
$message =
    <<<HTML
<h1>Compra cancelada</h1>
<p>
Olá, {$primeiro_nome}. 
</p>
<p>Sua compra do curso <strong>...</strong> foi cancelada por falta de pagamento.</p>
<p style="text-align:center">
<a href="__link__" style="padding:10px 20px;background:#ffe228!important;color:#000!important;display:inline-block;text-decoration:none!important;border-radius:5px;font-size:1.35em;margin:30px auto;border-bottom:2px solid #e4ca20;">Comprar!</a>
</p>
HTML;

wp_mail($email, "Compra cancelada", $message, $headers);

registrar('E-mail enviado para o cliente');

echo "cancelada - Executado ate o fim.";
http_response_code (200);
registrar( http_response_code() );

?>