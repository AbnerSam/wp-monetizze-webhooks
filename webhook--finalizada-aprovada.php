<?php
/*! 
 * 2018
 * https://github.com/hmaesta/wp-monetizze-webhooks
 */
// ----------------------------------------------------
// COMPRA FINALIZADA APROVADA
//
// PODE ENVIAR O PRODUTO!
// ----------------------------------------------------

header($_SERVER["SERVER_PROTOCOL"]." 200 OK");
//ini_set('display_errors', 1);
//ini_set('display_startup_errors', 1);
//error_reporting(E_ALL);

header('Content-Type: text/html; charset=utf-8');
date_default_timezone_set('America/Sao_Paulo');

// log
include('_monetizze-variaveis.php');
include('_monetizze-chaves.php');
include('_log.php');
include('_ip.php');
include('../wp-blog-header.php');

registrar('---------- Novo registro ----------');

// ----------------------------------------------------
// VARIAVEIS USUÁRIO
// ----------------------------------------------------

$usuario = time();
$senha = wp_generate_password(10, false);
$capacidade = "monetizze";

// ----------------------------------------------------

// Verifica se o usuário já existe
if (!username_exists($usuario)) {

    registrar('Compra Finalizada-Aprovada');
    registrar('Conexão IP: ' . get_client_ip() );
    registrar('Comprador: '. $nome_completo);
    registrar('Email: '. $email);

    $registroJson = array(
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
        "comprador_wp_usuario" => $usuario,
        "comprador_wp_id" => $insert_user,
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
    $registroEmail = json_encode($registroJson);

    // ----------------------------------------------------
    // CADASTRO DO USUÁRIO NO WORDPRESS
    // ----------------------------------------------------

    // Se não houver cadastro com esse e-mail...
    if (!email_exists($email)) {

        // Dados do usuário WordPress
        $usuario_wordpress = array(
            "user_pass" => $senha,
            "user_login" => $usuario,
            "user_email" => $email,
            "display_name" => $primeiro_nome . ' ' . $ultimo_nome,
            "first_name" => $primeiro_nome,
            "last_name" => $ultimo_nome,
            "role" => 's2member_level1'
        );

        // Cadastra no WordPress
        $insert_user = wp_insert_user($usuario_wordpress);

        // Delega capacidades
        $setar_capacidade = new WP_User($insert_user);
        $setar_capacidade->add_cap($capacidade);

        // ----------------------------------------------------
        // ERRO AO CADASTRAR...
        // ----------------------------------------------------
        if (is_wp_error($insert_user)) {
            registrar('Erro WordPress','error');
            $erro_registrado = $insert_user->get_error_message();
            registrar($erro_registrado);

            $headers = array('Content-Type: text/html; charset=UTF-8');
            $message =
                <<<HTML
<p>
Erro ao cadastrar usuário de COMPRA FINALIZADA APROVADA no WordPress
</p>

<p>WordPress retornou: <strong>$erro_registrado</strong></p>

<pre style="max-width:550px;">$registroEmail</pre>

<p>&nbsp;</p>
HTML;

            wp_mail($email_admin, "Erro em compra - usuário existente", $message, $headers);

            registrar('E-mail enviado para administrador.');
        } else {
            // ----------------------------------------------------
            // SUCESSO AO CADASTRAR...
            // CADASTRO NO ZAPIER E ENVIO DO E-MAIL PARA USUARIO
            // ----------------------------------------------------

            // Transforma em json
            $json = json_encode($registroJson, JSON_UNESCAPED_UNICODE);

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

                registrar('Usuário criado com sucesso: ' . $usuario);
                registrar('ID WordPress: ' . $insert_user);

                /*
                $dados_recebidos = json_encode($dados);
                $dados_recebidos_html = "<pre>" . print_r($dados_recebidos, true) . "</pre>";
                wp_mail(@email_admin, "Novo postback Monetizze", $dados_recebidos_html);
                */
            } else {
                registrar('Usuário criado com sucesso, mas erro ao enviar para o Zapier');

                $erro_mail = "Usuário criado com sucesso, mas erro ao enviar para o Zapier: $email";
                wp_mail(@email_admin, "Erro ao enviar usuário para Zapier", $erro_mail);
            }

            $headers = array('Content-Type: text/html; charset=UTF-8');
            $message =
                <<<HTML
<h1>Curso ...</h1>
<p>
Olá, {$primeiro_nome}. 
</p>
<p>
Sua compra do curso (...) foi concluída e agora você já pode assistir as aulas e ler todo o conteúdo exclusivo para assinantes.
<p> Abaixo estão os dados de acesso:
</p>
<p style="font-weight:bold">
Usuário: <span style="background-color:#f4f4f4;padding:2px 6px;font-family: monospace;font-weight: normal;display: inline-block;border-radius:3px;"><a href="mailto:{$email}" style="color:#333!important;text-decoration:none!important;">{$email}</a></span><br/>
Senha: <span style="background-color:#f4f4f4;padding:2px 6px;font-family: monospace;font-weight: normal;display: inline-block;border-radius:3px;">{$senha}</span>
</p>
<p style="text-align:center">
<a href="https://__site__/wp-login.php" style="padding:10px 25px;background:#27ae60!important;color:#FFF!important;display:inline-block;text-decoration:none!important;border-radius:5px;letter-spacing: 0.05em;font-size:1.1em;margin:8px auto;border-bottom:2px solid #1d924f;">Fazer login</a>
</p>
<p style="color:#999">Para sua segurança, por favor <strong>troque sua senha</strong> após o 1º acesso.</p>
<p>&nbsp;</p>
HTML;

            wp_mail($email, "Dados de acesso ao curso", $message, $headers);

            registrar('E-mail enviado para cliente.');

        }
    } else {
        // ----------------------------------------------------
        // SE O E-MAIL JÁ TIVER CADASTRO NO WORDPRESS
        // ----------------------------------------------------
        registrar('E-mail já cadastrado.');
        $usuario_cadastrado = get_user_by('email', $email);
        $usuario_ID = $usuario_cadastrado->ID;

        $adicionar_capacidade = new WP_User($usuario_ID);
        $adicionar_capacidade->add_cap($capacidade);
        registrar('OK. Usuário com nova capacidade!');
        $headers = array('Content-Type: text/html; charset=UTF-8');
        $message =
            <<<HTML
<h1>Curso Tesouro Direto</h1>
<p>
Olá, {$primeiro_nome}. 
</p>
<p>
Sua compra do curso (..) foi concluída e agora você já pode assistir as aulas.</p>

<p style="text-align:center">
<a href="https://__site__/wp-login.php" style="padding:10px 25px;background:#27ae60!important;color:#FFF!important;display:inline-block;text-decoration:none!important;border-radius:5px;letter-spacing: 0.05em;font-size:1.1em;margin:8px auto;border-bottom:2px solid #1d924f;">Acessar curso</a>
</p>
HTML;

        wp_mail($email, "Compra concluída", $message, $headers);

        registrar('E-mail enviado.');
    }

}
// ----------------------------------------------------
// SE O USUÁRIO JÁ EXISTE (COMO = NAO SEI, MAS...)
// ----------------------------------------------------
else {

    registrar('ERRO. Usuario já existe.','error');

    $headers = array('Content-Type: text/html; charset=UTF-8');
    $message =
        <<<HTML
<p>
Erro ao cadastrar usuário de COMPRA FINALIZADA APROVADA no WordPress
</p>
<p>Erro: usuário já existe</p>

<pre style="max-width:550px;">$registroEmail</pre>

<p>&nbsp;</p>
HTML;

    wp_mail($email_admin, "Erro em compra - usuário existente", $message, $headers);

    registrar('E-mail enviado para administrador.');

}

echo "finalizada-aprovada - Executado ate o fim.";
http_response_code (200);
registrar( http_response_code() );

?>
