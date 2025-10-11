<?php

require '../includes/verificacaoLogado.php';

require '../classes/sql.class.php';
$sql = new SQL();

$buscarContasBase = $sql->buscarContasBase();

$insert = "INSERT INTO contas (nome, pago, vencimento, caminho, ativo, mes, ano) VALUES\n";

$valores = array();
foreach ($buscarContasBase as $conta) {
    $caminho = 'null';
    
    $linha = "(" .
             "'" . $conta['nome'] . "', " .
             "'" . '' . "', " .
             $conta['vencimento'] . ", " .
             $caminho . ", " .
             "'" . $conta['ativo'] . "', " .
             "'" . $_REQUEST['mes'] . "', " .
             "'" . $_REQUEST['ano'] . "')";
    
    $valores[] = $linha;
}

$insert .= implode(",\n", $valores) . ";";

$executar = $sql->executarQueryBoleano($insert);

if($executar['informacao'] == "SUCESSO"){
    echo json_encode(['sucesso' => true]);
}else{
    echo json_encode(['sucesso' => false]);
}