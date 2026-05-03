<?php

require_once '../includes/verificacaoLogado.php';
require_once '../classes/sql.class.php';
$sql = new SQL();

$id          = filter_input(INPUT_POST, 'idSerialModal', FILTER_SANITIZE_NUMBER_INT);
$nome        = filter_input(INPUT_POST, 'nome',          FILTER_SANITIZE_SPECIAL_CHARS);
$vencimento  = filter_input(INPUT_POST, 'vencimento',    FILTER_SANITIZE_NUMBER_INT);
$pago        = isset($_POST['pago']) ? 1 : 0;
$recorrente  = isset($_POST['recorrente']) && $_POST['recorrente'] == '1' ? '1' : '0';

// Por padrão, não altera o caminho (preserva comprovante existente)
$novoCaminho = null;

if (!empty($_FILES['arquivo']['name'])) {
    $arquivo = $_FILES['arquivo'];
    $ext     = strtolower(pathinfo($arquivo['name'], PATHINFO_EXTENSION));

    if ($ext === 'pdf' && $arquivo['size'] <= 2 * 1024 * 1024) {
        $diretorio = '../uploads/';
        if (!is_dir($diretorio)) {
            mkdir($diretorio, 0775, true);
        }
        $nomeArquivo = uniqid('conta_') . '_' . time() . '.pdf';
        $destino     = $diretorio . $nomeArquivo;

        if (move_uploaded_file($arquivo['tmp_name'], $destino)) {
            $novoCaminho = $nomeArquivo;
        } else {
            error_log('Falha ao mover o arquivo PDF para o destino.');
        }
    } else {
        error_log('Arquivo inválido: formato diferente de PDF ou tamanho maior que 2 MB.');
    }
}

$result = $sql->atualizarConta($id, $nome, $vencimento, $pago, $recorrente, $novoCaminho);
echo json_encode(['sucesso' => $result['informacao'] === 'SUCESSO']);
