<?php

require '../includes/verificacaoLogado.php';
require '../classes/sql.class.php';
$sql = new SQL();

$id = $_POST['idSerialModal'];
$nome = $_POST['nome'];
$vencimento = $_POST['vencimento'];
$pago = isset($_POST['pago']) ? 1 : 0;
$caminho = null;


if (!empty($_FILES['arquivo']['name'])) {
    $arquivo = $_FILES['arquivo'];
    $ext = strtolower(pathinfo($arquivo['name'], PATHINFO_EXTENSION));

    if ($ext === 'pdf' && $arquivo['size'] <= 2 * 1024 * 1024) {

        $diretorio = '../uploads/';
        if (!is_dir($diretorio)) {
            mkdir($diretorio, 0775, true);
        }

        $nomeArquivo = uniqid('conta_') . '_' . time() . '.pdf';
        $destino = $diretorio . $nomeArquivo;

        if (move_uploaded_file($arquivo['tmp_name'], $destino)) {
            $caminho = $nomeArquivo;
        } else {
            $caminho = null;
            error_log('Falha ao mover o arquivo PDF para o destino.');
        }

    } else {
        $caminho = null;
        error_log('Arquivo inválido: formato diferente de PDF ou tamanho maior que 2MB.');
    }
} else {
    $caminho = null;
}


#echo '<pre>';print_r($_FILES);echo '</pre>';exit;

$result = $sql->atualizarConta($id, $nome, $vencimento, $pago, $caminho);
echo json_encode(['sucesso' => $result ? true : false]);
