<?php
/*! 
 * 2018
 * https://github.com/hmaesta/wp-monetizze-webhooks
 */
// ----------------------------------------------------
// COMPRA AGUARDANDO PAGAMENTO
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
registrar('Aguardando pagamento');
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
    "comprador_cnpj_cpf" => $cnpj_cpf,
    "comprador_telefone" => $telefone,
    "comprador_cep" => $cep,
    "comprador_bairro" => $bairro,
    "comprador_cidade" => $cidade,
    "comprador_estado" => $estado,
    //"comprador_wp_usuario" => $usuario,
    //"comprador_wp_id" => $insert_user,
    "codigo_venda_transacao" => $codigo_da_transacao,
    "forma_de_pagamento" => $forma_pagamento,
    "meio_de_pagamento" => $meio_pagamento,
    "garantia_restante" => $garantia_restante,
    "status_venda" => $status_venda,
    "valor_venda" => $valor_venda,
    "valor_recebido" => $valor_recebido,
    "boleto_link" => $link_boleto,
    "boleto_digitavel" => $linha_digitavel
);
$registrosJson = json_encode($registros);


// ----------------------------------------------------
// CADASTRO NO ZAPIER
// ----------------------------------------------------

// Transforma em json
$json = json_encode($registros, JSON_UNESCAPED_UNICODE);

// URL pra curl no Zapier
$urlZapier = "https://hooks.zapier.com/hooks/catch/__prencher__";

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

    registrar('Cadastrado no Zapier');
    //echo json_encode($status) . "<br><br><pre>" . json_encode($registros) . "</pre>";
} else {
    registrar('Erro ao cadastrar no zapier','error');
}

// ----------------------------------------------------
// ENVIO DO E-MAIL
// ----------------------------------------------------

$headers = array('Content-Type: text/html; charset=UTF-8');
$message =
    <<<HTML
<h1>Aguardando pagamento</h1>
<p>
Olá, {$primeiro_nome}. 
</p>
<p>
Sua compra do curso <strong>...</strong> foi feita com sucesso, agora estamos <strong>aguardando o pagamento</strong> para liberar seu acesso ao curso.</p>
<p>&nbsp;</p>
<p style="font-weight:bold;font-size:1.1em; text-align:center">
O código para pagamento do seu boleto é:<br/>
<span style="background-color:#f4f4f4;padding:4px 8px;font-family: monospace;font-weight: normal;display: inline-block;border-radius:3px;font-size:1.1em">
$linha_digitavel
</span>
</p>
<p style="text-align:center">
<a href="$link_boleto" style="padding:10px 20px;background:#2E4057!important;color:#FFF!important;display:inline-block;text-decoration:none!important;border-radius:5px;letter-spacing: 0.05em;font-size:1.1em;margin:8px auto;border-bottom:2px solid #152131;">Imprimir boleto (PDF)</a>
</p>
<p>&nbsp;</p>
<hr/>
<br/>
<br/>
<h3>Acesso imediato ao curso</h3>
<p>Não quer esperar para assistir ao curso? <strong>Pague com cartão de crédito e tenha acesso imediato!</strong></p>
<p>Basta realizar uma nova compra e pagar com cartão. A compra atual (boleto) será cancelada.</p>
<a href="__link__" style="padding:10px 20px;background:#ffe228!important;color:#000!important;display:inline-block;text-decoration:none!important;border-radius:5px;font-size:1.1em;margin:8px auto;border-bottom:2px solid #e4ca20;">Pagar com cartão de crédito</a>
HTML;

wp_mail($email, "Aguardando pagamento", $message, $headers);

registrar('E-mail enviado para cliente');

echo "aguardando-pagamento - Executado ate o fim.";
http_response_code (200);
registrar( http_response_code() );


?>