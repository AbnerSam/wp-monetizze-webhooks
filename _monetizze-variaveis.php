<?php
/*! 
 * 2018
 * https://github.com/hmaesta/wp-monetizze-webhooks
 */


$email_admin = 'seuemail@gmail.com';


$dados = $_POST;

if (empty($dados)) {
    die ('Erro');
}

// ----------------------------------------------------
// VARIAVEIS VENDA
// ----------------------------------------------------

$codigo_do_produto = $dados['produto']['codigo'];
$nome_do_produto = $dados['produto']['nome'];
$codigo_da_transacao = $dados['venda']['codigo'];
$codigo_plano = $dados['venda']['plano'];
$cupom_utilizado = $dados['venda']['cupom'];

$data_inicio = $dados['venda']['dataInicio']; // Data que iniciou a compra (yyyy-mm-dd H:i:s)
$data_finalizada = $dados['venda']['dataFinalizada']; // Data que foi confirmado o pagamento (yyyy-mm-dd H:i:s)
$meio_pagamento = $dados['venda']['meioPagamento']; // (PagSeguro, MoIP, Monetizze)
$forma_pagamento = $dados['venda']['formaPagamento']; // (Cartão de crédito,  Débito online, Boleto, Gratis, Outra)
$garantia_restante = $dados['venda']['garantiaRestante'];
$status_venda = $dados['venda']['status']; // Status da venda (Aguardando pagamento, Finalizada, Cancelada, Devolvida, Bloqueada, Completa)
$valor_venda = $dados['venda']['valor']; // Valor total pago (1457.00)
$quantidade_venda = $dados['venda']['quantidade']; // Quantidade de produtos comprados nessa venda
$valor_recebido = $dados['venda']['valorRecebido']; // Valor total que você recebeu por essa venda (1367.00)
$tipo_frete = $dados['venda']['tipo_frete']; // Tipo do frete ( 4014 = SEDEX, 4510 = PAC, 999999 = Valor Fixo, ou código da Intelipost)
//$desc_tipo_frete = $dados['venda']['descr_tipo_frete']; // Descricao do frete (Ex: Correios SEDEX, Corretios PAC, Total Express)
$frete = $dados['venda']['frete']; // Valor pago pelo frete

$src = $dados['venda']['src']; // Valor do SRC que foi enviado via parâmetro da URL de divulgação
$utm_source = $dados['venda']['utm_source'];
$utm_medium = $dados['venda']['utm_medium'];
$utm_content = $dados['venda']['utm_content'];
$utm_campaign = $dados['venda']['utm_campaign'];

//$plano_codigo = $dados['plano']['codigo']; // codigo do plano
//$plano_referencia = $dados['plano']['referencia']; // referencia do plano
//$plano_nome = $dados['plano']['nome']; // nome do plano
//$plano_quantidade = $dados['plano']['quantidade']; // quantidade de produtos que sao entregues com esse plano, normalmente usado em produtos físicos

$link_boleto = $dados['venda']['linkBoleto']; // Link para impressão do boleto
$linha_digitavel = $dados['venda']['linha_digitavel']; // Linha digitável do boleto

$comissoes = $dados['comissoes'];

foreach ($comissoes as $comissao) {
$refAfiliado[] = $comissao['refAfiliado']; // Referencia do afiliado ao produto
$nomeComissionado[] = $comissao['nome']; // Afiliado
$tipoComissao[] = $comissao['tipo_comissao']; // tipo da comissão (Sistema, Produtor, Co-Produtor, Primeiro Clique, Clique intermediário, Último Clique, Lead, Premium, Gerente)
$valorComissao[] = $comissao['valor']; // Valor que esse comissionado recebeu
$porcComissao[] = $comissao['comissao']; // Porcentagem do valor todal da venda que ele recebeu
$EmailComissao[] = $comissao['email']; // E-mail do afiliado
}

$email = $dados['comprador']['email'];
if ($email === "teste@teste.com.br") { $email = $email_para_teste; }

$data_nascimento = $dados['comprador']['data_nascimento']; // (yyyy-mm-dd)
$cnpj_cpf = $dados['comprador']['cnpj_cpf'];
$telefone = $dados['comprador']['telefone'];
$cep = $dados['comprador']['cep'];
$endereco = $dados['comprador']['endereco'];
$numero = $dados['comprador']['numero'];
$complemento = $dados['comprador']['complemento'];
$bairro = $dados['comprador']['bairro'];
$cidade = $dados['comprador']['cidade'];
$estado = $dados['comprador']['estado'];
$pais = $dados['comprador']['pais'];

$nome_completo = $dados['comprador']['nome'];

function separacao_nome($string)
{
    $arr = explode(' ', $string);
    $num = count($arr);

    if ($num == 2) {
        list($first_name, $last_name) = $arr;
    } else {
        list($first_name, $middle_name, $last_name) = $arr;
    }

    return (empty($first_name) || $num > 3) ? false : array(
        'first_name' => $first_name,
        //'middle_name' => $middle_name,
        'last_name' => $last_name
    );
}

$primeiro_nome = separacao_nome($nome_completo)['first_name'];
$ultimo_nome = separacao_nome($nome_completo)['last_name'];

$data_atual = date('Y-m-d H:i:s');
