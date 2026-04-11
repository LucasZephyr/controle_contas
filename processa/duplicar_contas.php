<?php

require '../includes/verificacaoLogado.php';
require '../classes/sql.class.php';
$sql = new SQL();

$mes = filter_input(INPUT_POST, 'mes', FILTER_SANITIZE_NUMBER_INT);
$ano = filter_input(INPUT_POST, 'ano', FILTER_SANITIZE_NUMBER_INT);

if (empty($mes) || empty($ano)) {
    echo json_encode(['sucesso' => false, 'mensagem' => 'Mês e ano são obrigatórios.']);
    exit;
}

if ($sql->verificarMesExiste($mes, $ano)) {
    echo json_encode(['sucesso' => false, 'mensagem' => 'O mês selecionado já possui contas cadastradas.']);
    exit;
}

$result = $sql->gerarMesAutomatico($mes, $ano);

if ($result['informacao'] === 'SUCESSO') {
    echo json_encode(['sucesso' => true]);
} else {
    echo json_encode([
        'sucesso'  => false,
        'mensagem' => isset($result['mensagem']) ? $result['mensagem'] : 'Erro ao gerar contas.'
    ]);
}
