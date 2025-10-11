<?php

// ini_set('display_errors', 1);
// error_reporting(E_ALL);

require '../includes/verificacaoLogado.php';

require '../classes/sql.class.php';
$sql = new SQL();


echo $_REQUEST['acao']();die();


function cadastrarConta(){

    global $sql;

    $nomeConta = filter_input(INPUT_POST, 'nomeConta', FILTER_SANITIZE_SPECIAL_CHARS);
    $dataVencimento = filter_input(INPUT_POST, 'dataVencimento', FILTER_SANITIZE_SPECIAL_CHARS);

    if(empty($nomeConta) || empty($dataVencimento)){
        $retorno = array("informacao" => "ERROR", "mensagem" => "Preencha todos os campos!");
        return json_encode($retorno);
    }

    $inserir = $sql->inserirContas($nomeConta, $dataVencimento);

    if($inserir['informacao'] == "SUCESSO"){
        $retorno = array("informacao" => "SUCESSO", "mensagem" => "Conta cadastrada com sucesso!");
        return json_encode($retorno);
    } else {
        $retorno = array("informacao" => "ERROR", "mensagem" => "Erro ao cadastrar conta!");
        return json_encode($retorno);
    }
}

function marcarPagoConta(){

    global $sql;

    $idConta = $_REQUEST['idConta'];
    $idConta = filter_input(INPUT_POST, 'idConta', FILTER_SANITIZE_SPECIAL_CHARS);

    if(empty($idConta)){
        $retorno = array("informacao" => "ERROR", "mensagem" => "Parâmetros inválidos!");
        return json_encode($retorno);
    }

    $atualizar = $sql->atualizarPagoConta($idConta);

    if($atualizar['informacao'] == "SUCESSO"){
        $retorno = array("informacao" => "SUCESSO", "mensagem" => "Status atualizado com sucesso!");
        return json_encode($retorno);
    } else {
        $retorno = array("informacao" => "ERROR", "mensagem" => "Erro ao atualizar status!");
        return json_encode($retorno);
    }
}

function buscarConta(){

    global $sql;

    $idConta = $_REQUEST['id'];
    $idConta = filter_input(INPUT_POST, 'id', FILTER_SANITIZE_SPECIAL_CHARS);

    if(empty($idConta)){
        $retorno = array("informacao" => "ERROR", "mensagem" => "Parâmetros inválidos!");
        return json_encode($retorno);
    }

    $buscar = $sql->buscarContaPorId($idConta);
    #echo '<pre>';print_r($buscar);echo '</pre>';exit;

    if(!empty($buscar)){
        return json_encode($buscar);
    } else {
        $retorno = array("informacao" => "ERROR", "mensagem" => "Erro ao atualizar status!");
        return json_encode($retorno);
    }

}
