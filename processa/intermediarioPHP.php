<?php

// ini_set('display_errors', 1);
// error_reporting(E_ALL);

require '../includes/verificacaoLogado.php';
require '../classes/sql.class.php';
$sql = new SQL();

$acao = isset($_POST['acao']) ? $_POST['acao'] : '';

switch ($acao) {
    case 'cadastrarConta':
        echo cadastrarConta();
        break;
    case 'marcarPagoConta':
        echo marcarPagoConta();
        break;
    case 'buscarConta':
        echo buscarConta();
        break;
    default:
        echo json_encode(['informacao' => 'ERROR', 'mensagem' => 'Ação inválida']);
}


function cadastrarConta() {
    global $sql;

    $nomeConta    = filter_input(INPUT_POST, 'nomeConta',      FILTER_SANITIZE_SPECIAL_CHARS);
    $vencimento   = filter_input(INPUT_POST, 'dataVencimento', FILTER_SANITIZE_NUMBER_INT);
    $mes          = filter_input(INPUT_POST, 'mes',            FILTER_SANITIZE_NUMBER_INT);
    $ano          = filter_input(INPUT_POST, 'ano',            FILTER_SANITIZE_NUMBER_INT);
    $recorrenteRaw = filter_input(INPUT_POST, 'recorrente',    FILTER_SANITIZE_NUMBER_INT);
    $recorrente   = $recorrenteRaw ? '1' : '0';

    if (empty($nomeConta) || empty($vencimento) || empty($mes) || empty($ano)) {
        return json_encode(['informacao' => 'ERROR', 'mensagem' => 'Preencha todos os campos!']);
    }

    $inserir = $sql->inserirContas($nomeConta, $vencimento, $mes, $ano, $recorrente);

    if ($inserir['informacao'] === 'SUCESSO') {
        return json_encode(['informacao' => 'SUCESSO', 'mensagem' => 'Conta cadastrada com sucesso!']);
    }
    return json_encode(['informacao' => 'ERROR', 'mensagem' => 'Erro ao cadastrar conta!']);
}


function marcarPagoConta() {
    global $sql;

    $idConta = filter_input(INPUT_POST, 'idConta', FILTER_SANITIZE_NUMBER_INT);

    if (empty($idConta)) {
        return json_encode(['informacao' => 'ERROR', 'mensagem' => 'Parâmetros inválidos!']);
    }

    $atualizar = $sql->atualizarPagoConta($idConta);

    if ($atualizar['informacao'] === 'SUCESSO') {
        return json_encode(['informacao' => 'SUCESSO', 'mensagem' => 'Status atualizado com sucesso!']);
    }
    return json_encode(['informacao' => 'ERROR', 'mensagem' => 'Erro ao atualizar status!']);
}


function buscarConta() {
    global $sql;

    $idConta = filter_input(INPUT_POST, 'id', FILTER_SANITIZE_NUMBER_INT);

    if (empty($idConta)) {
        return json_encode(['informacao' => 'ERROR', 'mensagem' => 'Parâmetros inválidos!']);
    }

    $buscar = $sql->buscarContaPorId($idConta);

    if (!empty($buscar)) {
        return json_encode($buscar);
    }
    return json_encode(['informacao' => 'ERROR', 'mensagem' => 'Conta não encontrada!']);
}
