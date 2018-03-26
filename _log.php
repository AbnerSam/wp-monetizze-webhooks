<?php
/*! 
 * 2018
 * https://github.com/hmaesta/wp-monetizze-webhooks
 */

function registrar( $msg, $level = 'info' )
{
    // variável que vai armazenar o nível do log (INFO, WARNING ou ERROR)
    $levelStr = '';

    // verifica o nível do log
    switch ( $level )
    {
        case 'info':
            // nível de informação
            $levelStr = 'INFO';
            break;

        case 'warning':
            // nível de aviso
            $levelStr = 'WARNING';
            break;

        case 'error':
            // nível de erro
            $levelStr = 'ERROR';
            break;
    }

    // data de hoje
    $date = date( 'Y-m-d H:i:s' );

    // nome do arquivo
    // ex: 2018-01-01
    $__dia = date( 'Y-m-d' );
    
    // path do arquivo
    $__logfile = 'logs/' . $__dia . '.log';

    // formata a mensagem do log
    // 1o: data atual
    // 2o: nível da mensagem (INFO, WARNING ou ERROR)
    // 3o: a mensagem propriamente dita
    // 4o: uma quebra de linha
    $msg = sprintf( "[%s] [%s]: %s%s", $date, $levelStr, $msg, PHP_EOL );

    // escreve o log no arquivo
    // é necessário usar FILE_APPEND para que a mensagem seja escrita no final do arquivo, preservando o conteúdo antigo do arquivo
    file_put_contents( $__logfile, $msg, FILE_APPEND );
}