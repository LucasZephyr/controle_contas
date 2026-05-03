<?php
ini_set('display_errors', 0);

require_once 'includes/verificacaoLogado.php';
require_once 'classes/sql.class.php';
$sql = new SQL();

$dataAtual = new DateTime('now');
$mesAtual  = (int)$dataAtual->format('m');
$anoAtual  = (int)$dataAtual->format('Y');
$mesAtualPad = str_pad($mesAtual, 2, '0', STR_PAD_LEFT);

// Auto-gera as contas do mês atual se ainda não existirem
if (!$sql->verificarMesExiste($mesAtualPad, $anoAtual)) {
    $sql->gerarMesAutomatico($mesAtualPad, $anoAtual);
}

$buscarContas = $sql->buscarContas();

$contasAgrupadas = [];
foreach ($buscarContas as $conta) {
    $mes = (int)$conta['mes'];
    $ano = (int)$conta['ano'];

    // Ignora meses futuros
    if ($ano > $anoAtual || ($ano == $anoAtual && $mes > $mesAtual)) {
        continue;
    }

    // Chave cronológica YYYYMM
    $chave = ($ano * 100) + $mes;
    $contasAgrupadas[$chave][] = $conta;
}
krsort($contasAgrupadas);

$nomesMeses = [
    '01' => 'Janeiro',  '02' => 'Fevereiro', '03' => 'Março',
    '04' => 'Abril',    '05' => 'Maio',       '06' => 'Junho',
    '07' => 'Julho',    '08' => 'Agosto',     '09' => 'Setembro',
    '10' => 'Outubro',  '11' => 'Novembro',   '12' => 'Dezembro'
];
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <title>Controle de Contas</title>
    <?php include_once 'includes/cabecalho.php' ?>
</head>

<body>

    <?php include_once 'includes/navBar.php' ?>

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

        <hr>

        <section class="section dashboard">
            <div class="row">
                <div class="col-lg-12">

                    <?php foreach ($contasAgrupadas as $chave => $contasMes):
                        $ano = substr((string)$chave, 0, 4);
                        $mes = substr((string)$chave, 4, 2);
                        $mes = str_pad($mes, 2, '0', STR_PAD_LEFT);
                        $titulo = isset($nomesMeses[$mes])
                            ? $nomesMeses[$mes] . ' de ' . $ano
                            : $mes . ' ' . $ano;
                    ?>
                        <div class="row mb-4">
                            <div class="card shadow-sm">
                                <h5 class="card-header bg-light fw-bold d-flex justify-content-between align-items-center">
                                    <span>Tabelas de Contas - <?= $titulo ?></span>
                                    <button
                                        type="button"
                                        class="btn btn-sm btn-outline-success"
                                        title="Adicionar conta em <?= $titulo ?>"
                                        onclick="abrirModalNovaConta('<?= $mes ?>', '<?= $ano ?>')">
                                        <i class="bi bi-plus-lg"></i>
                                    </button>
                                </h5>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-striped align-middle">
                                            <thead>
                                                <tr>
                                                    <th>Nome</th>
                                                    <th>Comprov</th>
                                                    <th>Pago</th>
                                                    <th>Vence</th>
                                                    <th>Ação</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($contasMes as $conta):
                                                    if (strpos(strtoupper($conta['nome']), 'ACABOU') !== false) {
                                                        continue;
                                                    }
                                                ?>
                                                    <tr>
                                                        <td><?= strtoupper(htmlspecialchars($conta['nome'])) ?></td>
                                                        <td>
                                                            <?= ($conta['caminho'] != '')
                                                                ? '<span class="badge bg-success mx-1">Sim</span>'
                                                                : '<span class="badge bg-danger mx-1">Nao</span>' ?>
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
                                                                        onchange="marcarPagoConta(<?= (int)$conta['serial'] ?>);">
                                                                </div>
                                                            <?php endif; ?>
                                                        </td>
                                                        <td><?= (int)$conta['vencimento'] ?></td>
                                                        <td>
                                                            <button
                                                                type="button"
                                                                class="btn btn-sm btn-primary"
                                                                onclick="abrirModalAcoes(<?= (int)$conta['serial'] ?>);">
                                                                <i class="bi bi-list"></i>
                                                            </button>
                                                        </td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>

                </div>
            </div>
        </section>

    </main><!-- FIM DO MENU PRINCIPAL -->


    <!-- Modal: Editar/Visualizar Conta -->
    <div class="modal fade" id="modalAcao" tabindex="-1" aria-labelledby="modalAcaoLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="modalAcaoLabel">Ações</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="modalAcaoBody"></div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-sm btn-success" data-bs-dismiss="modal" onclick="atualizarConta();">
                        <i class="bi bi-save"></i> Salvar
                    </button>
                </div>
            </div>
        </div>
    </div>


    <!-- Modal: Nova Conta -->
    <div class="modal fade" id="modalNovaConta" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5">Nova Conta</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="formNovaConta">
                        <input type="hidden" id="novaContaMes" name="mes">
                        <input type="hidden" id="novaContaAno" name="ano">

                        <div class="mb-2">
                            <span class="fw-bold" id="novaContaMesAnoLabel"></span>
                        </div>

                        <div class="mb-3">
                            <label for="novaContaNome" class="form-label">Nome da Conta</label>
                            <input type="text" class="form-control" id="novaContaNome" name="nomeConta" placeholder="Ex: Internet">
                        </div>

                        <div class="mb-3">
                            <label for="novaContaVencimento" class="form-label">Dia de Vencimento</label>
                            <input type="number" class="form-control" id="novaContaVencimento" name="dataVencimento" min="1" max="31">
                        </div>

                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="novaContaRecorrente" name="recorrente" value="1" checked>
                            <label class="form-check-label" for="novaContaRecorrente">
                                Recorrente <small class="text-muted">(aparece automaticamente todo mês)</small>
                            </label>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-sm btn-success" onclick="salvarNovaConta();">
                        <i class="bi bi-plus-lg"></i> Adicionar
                    </button>
                </div>
            </div>
        </div>
    </div>


    <!-- ======= RODAPÉ ======= -->
    <?php include_once "includes/rodape.php"; ?>

    <script src="assets/js/script_js/controle_contas.index.js?v=2"></script>

</body>
</html>
