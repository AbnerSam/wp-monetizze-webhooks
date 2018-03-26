<?php
/*! 
 * 2018
 * https://github.com/hmaesta/wp-monetizze-webhooks
 */

$chave_unica = $dados['chave_unica'];
if ($chave_unica != '__preencher__') { // ex: cd2277e31...
    die ('Erro chave');
    registrar('Erro chave única');
}

/*
Você também pode usar a chave de produto
(Falta teste)

$chave_produto = $dados['produto']['chave'];
if($chave_produto  != '__preencher__') {
    die ('Erro chave produto');
    registrar('Erro chave produto');
}
*/