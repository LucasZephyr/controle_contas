<?php
require_once 'includes/verificacaoLogado.php';
require_once 'classes/sql.class.php';
$sql = new SQL();

$dataAtual = new DateTime('now');
$mesPadrao = (int)$dataAtual->format('m');
$anoPadrao = (int)$dataAtual->format('Y');

$nomesMeses = [
    1 => 'Janeiro',  2 => 'Fevereiro', 3 => 'Março',
    4 => 'Abril',    5 => 'Maio',      6 => 'Junho',
    7 => 'Julho',    8 => 'Agosto',    9 => 'Setembro',
    10 => 'Outubro', 11 => 'Novembro', 12 => 'Dezembro'
];
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <title>Gerar Mês - Controle de Contas</title>
    <?php include_once 'includes/cabecalho.php' ?>
</head>
<body>

    <?php include_once 'includes/navBar.php' ?>

    <main id="main" class="main">

        <div class="pagetitle">
            <h1>Gerar Contas do Mês</h1>
            <nav>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="index.php">Início</a></li>
                    <li class="breadcrumb-item active">Gerar Mês</li>
                </ol>
            </nav>
        </div>

        <hr>

        <section class="section">
            <div class="row justify-content-center">
                <div class="col-lg-6">
                    <div class="card shadow-sm">
                        <h5 class="card-header bg-light fw-bold">
                            <i class="bi bi-calendar-plus me-2"></i>Gerar Contas para um Mês
                        </h5>
                        <div class="card-body">

                            <p class="text-muted mb-4">
                                Selecione o mês e ano desejado. O sistema irá copiar automaticamente
                                todas as contas recorrentes do mês anterior mais recente.
                            </p>

                            <div class="row align-items-end g-3">
                                <div class="col-md-5">
                                    <label for="mes" class="form-label">Mês</label>
                                    <select class="form-select" id="mes" name="mes">
                                        <?php foreach ($nomesMeses as $num => $nome): ?>
                                            <option value="<?= $num ?>" <?= $num === $mesPadrao ? 'selected' : '' ?>>
                                                <?= $nome ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <div class="col-md-4">
                                    <label for="ano" class="form-label">Ano</label>
                                    <input type="number" class="form-control" id="ano" name="ano"
                                           value="<?= $anoPadrao ?>" min="2020" max="2099" required>
                                </div>

                                <div class="col-md-3">
                                    <button type="button" class="btn btn-primary w-100" onclick="gerarMes();">
                                        <i class="bi bi-calendar-check me-1"></i> Gerar
                                    </button>
                                </div>
                            </div>

                            <div class="alert alert-info mt-4 mb-0">
                                <i class="bi bi-info-circle me-2"></i>
                                <strong>Como funciona:</strong> Ao abrir a página inicial, o mês atual já é
                                gerado automaticamente. Use esta tela apenas para gerar meses anteriores
                                ou futuros que ainda não possuam contas.
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </section>

    </main>

    <?php include_once 'includes/rodape.php' ?>

    <script>
        function gerarMes() {
            var mes = document.getElementById('mes').value;
            var ano = document.getElementById('ano').value;
            var nomesMeses = [
                '', 'Janeiro', 'Fevereiro', 'Março', 'Abril', 'Maio', 'Junho',
                'Julho', 'Agosto', 'Setembro', 'Outubro', 'Novembro', 'Dezembro'
            ];

            Swal.fire({
                icon: 'question',
                title: 'Confirmar geração?',
                text: 'As contas recorrentes serão criadas para ' + nomesMeses[parseInt(mes)] + ' de ' + ano + '.',
                showCancelButton: true,
                confirmButtonText: 'Sim, gerar',
                cancelButtonText: 'Cancelar'
            }).then(function(result) {
                if (result.isConfirmed) {
                    $.ajax({
                        url: 'processa/duplicar_contas.php',
                        type: 'POST',
                        dataType: 'json',
                        data: { mes: mes, ano: ano },
                        success: function(resp) {
                            if (resp.sucesso) {
                                Swal.fire('Sucesso!', 'Contas geradas com sucesso.', 'success')
                                    .then(function() { window.location.href = 'index.php'; });
                            } else {
                                Swal.fire('Aviso', resp.mensagem || 'Não foi possível gerar as contas.', 'warning');
                            }
                        },
                        error: function() {
                            Swal.fire('Erro', 'Falha na comunicação com o servidor.', 'error');
                        }
                    });
                }
            });
        }
    </script>

</body>
</html>
