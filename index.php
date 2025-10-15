<?php

ini_set('display_errors', 0);

require 'includes/verificacaoLogado.php';

require 'classes/sql.class.php';
$sql = new SQL();
#echo '<pre>';print_r($getDadoFeriasRelatorio);exit;

$buscarContas = $sql->buscarContas();
#echo '<pre>';print_r($buscarContas);echo '</pre>';exit;

$dataAtual = date('Y-m-d');
$mesAtual = date('m');
$anoAtual = date('Y');

$contasAgrupadas = array();

foreach ($buscarContas as $conta) {
    $mes = (int)$conta['mes'];
    $ano = (int)$conta['ano'];

    #se for um mes e ano futuro, pula...
    if ($ano > $anoAtual || ($ano == $anoAtual && $mes > $mesAtual)) {
        continue;
    }
    $chave = $conta['mes'] . '-' . $conta['ano'];
    $contasAgrupadas[$chave][] = $conta;
}
krsort($contasAgrupadas);

$nomesMeses = array(
    '01' => 'Janeiro',
    '02' => 'Fevereiro',
    '03' => 'Março',
    '04' => 'Abril',
    '05' => 'Maio',
    '06' => 'Junho',
    '07' => 'Julho',
    '08' => 'Agosto',
    '09' => 'Setembro',
    '10' => 'Outubro',
    '11' => 'Novembro',
    '12' => 'Dezembro'
);

?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <title>Controle de Contas</title>
    
    <?php include 'includes/cabecalho.php'?>
</head>

<body>

    <?php include 'includes/navBar.php'?>



    <!-- Inicio menu Principal-->
    <main id="main" class="main">

        <div class="pagetitle">
            <h1>Historico de Contas</h1>
            <nav>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="index.php">inicio</a></li>
                    <li class="breadcrumb-item">Tabelas de Pagamentos</li>
                </ol>
            </nav>
        </div>

        <!--
        <section class="section dashboard">
            <div class="row">
                <div class="col-lg-12">
                    <div class="row">
                        <div class="card">
                            <h5 class="card-header">NOVA CONTA</h5>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-sm">
                                        <thead>
                                            <tr>
                                                <th scope="col">Nome</th>
                                                <th scope="col">Vencimento</th>
                                                <th scope="col">Cadastrar</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td>
                                                    <input type="text" id="nomeConta" class="form-control form-control-sm" placeholder="Nome da Conta">
                                                </td>
                                                <td>
                                                    <input type="number" id="dataVencimento" class="form-control form-control-sm" placeholder="Dia do Vencimento">
                                                </td>
                                                <td align="right">
                                                    <button type="button" onclick="cadastrarConta();" class="btn btn-sm btn-success">
                                                        <i class="bi bi-plus-lg"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        -->

        <hr>

        <section class="section dashboard">
            <div class="row">
                <div class="col-lg-12">

                    <?php foreach ($contasAgrupadas as $chave => $contasMes){
                        list($mes, $ano) = explode('-', $chave);
                        $titulo = (isset($nomesMeses[str_pad($mes, 2, '0', STR_PAD_LEFT)]) 
                        ? $nomesMeses[str_pad($mes, 2, '0', STR_PAD_LEFT)] 
                        : $mes) . " $ano";

                    ?>
                        <div class="row mb-4">
                            <div class="card shadow-sm">
                                <h5 class="card-header bg-light fw-bold">Tabelas de Contas - <?= $titulo ?></h5>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-striped align-middle">
                                            <thead>
                                                <tr>
                                                    <th>Nome</th>
                                                    <th>Pago</th>
                                                    <th>Comprov</th>
                                                    <th>Vence</th>
                                                    <th>Ação</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($contasMes as $conta){ ?>
                                                    <tr>
                                                        <td>
                                                            <?= strtoupper($conta['nome']) ?>
                                                        </td>
                                                        <td>
                                                            <?= ($conta['caminho'] != "") 
                                                            ? 'Sim' 
                                                            : 'Nao' ?>
                                                        </td>
                                                        <td>
                                                            <?php if ($conta['pago'] == 1): ?>
                                                                <i class="bi bi-check-circle-fill text-success"></i>
                                                            <?php else: ?>
                                                                <div class="form-check form-switch">
                                                                    <input 
                                                                        class="form-check-input" 
                                                                        type="checkbox" 
                                                                        id="pagoConta<?= $conta['serial'] ?>" 
                                                                        value="1"
                                                                        onchange="marcarPagoConta(<?= $conta['serial'] ?>);"
                                                                    >
                                                                </div>
                                                            <?php endif; ?>
                                                        </td>
                                                        <td><?= $conta['vencimento'] ?></td>
                                                        <td>
                                                            <button 
                                                                type="button" 
                                                                class="btn btn-sm btn-primary" 
                                                                onclick="abrirModalAcoes(<?= $conta['serial'] ?>);"
                                                            >
                                                                <i class="bi bi-list"></i>
                                                            </button>
                                                        </td>
                                                    </tr>
                                                <?php } ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php } ?>

                </div>
            </div>
        </section>

        



    </main><!-- FIM DO MENU PRINCIPAL -->

    <!-- Modal -->
    <div class="modal fade" id="modalAcao" tabindex="-1" aria-labelledby="modalAcaoLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="modalAcaoLabel">Ações</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="modalAcaoBody">
                    
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-sm btn-success" data-bs-dismiss="modal" onclick="atualizarConta();">
                        <i class="bi bi-save"></i> Salvar
                    </button>
                </div>
            </div>
        </div>
    </div>


    <!-- ======= RODAPÉ ======= -->
    <?php include "includes/rodape.php";?>

    <script src="assets/js/script_js/controle_contas.index.js"> </script>

</body>

</html>