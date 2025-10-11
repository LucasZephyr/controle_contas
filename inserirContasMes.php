<?php
require 'includes/verificacaoLogado.php';

?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Duplicar Contas</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <style>
        body {
            background-color: #f8f9fa;
            padding: 20px;
        }
        .card {
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            border: none;
            border-radius: 10px;
        }
        .card-title {
            color: #2c3e50;
            font-weight: 600;
        }
        .form-label {
            font-weight: 500;
            color: #495057;
        }
        .btn-primary {
            background-color: #3498db;
            border-color: #3498db;
            font-weight: 500;
        }
        .btn-primary:hover {
            background-color: #2980b9;
            border-color: #2980b9;
        }
        .info-box {
            background-color: #e8f4fc;
            border-left: 4px solid #3498db;
            padding: 15px;
            border-radius: 5px;
            margin-top: 20px;
        }
        .month-selector {
            display: flex;
            flex-direction: column;
            align-items: center;
            margin-bottom: 15px;
        }
        .month-selector .month-name {
            font-size: 14px;
            margin-top: 5px;
            color: #6c757d;
        }
    </style>
</head>
<body>

    <?php include 'includes/cabecalho.php'; ?>
    <?php include 'includes/navBar.php'; ?>

    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card mt-4">
                    <div class="card-header bg-white">
                        <h5 class="card-title mb-0">
                            <i class="bi bi-files me-2"></i>Duplicar Contas
                        </h5>
                    </div>
                    <div class="card-body">
                        <p class="text-muted mb-4">
                            Selecione o mês e ano de referência para duplicar as contas do período anterior.
                        </p>
                        
                        <form id="formDuplicarContas">
                            <div class="row align-items-end g-3">
                                <div class="col-md-4">
                                    <label for="mes" class="form-label">Mês</label>
                                    <div class="month-selector">
                                        <input type="range" class="form-range" id="mesRange" min="1" max="12" value="1">
                                        <div class="month-name" id="monthName">Janeiro</div>
                                    </div>
                                    <input type="number" class="form-control mt-2" id="mes" name="mes" min="1" max="12" value="1" required>
                                </div>
                                <div class="col-md-4">
                                    <label for="ano" class="form-label">Ano</label>
                                    <input type="number" class="form-control" id="ano" name="ano" value="2023" required>
                                </div>
                                <div class="col-md-4">
                                    <button type="button" class="btn btn-primary btn-sm w-100 py-2" onclick="duplicarContas();">
                                        <i class="bi bi-files me-2"></i> Duplicar Contas
                                    </button>
                                </div>
                            </div>
                        </form>
                        
                        <div class="info-box">
                            <h6><i class="bi bi-info-circle me-2"></i>Como funciona?</h6>
                            <p class="mb-0">
                                Esta função irá copiar todas as contas do mês anterior para o mês e ano selecionados. 
                                As contas duplicadas manterão as mesmas categorias e valores, mas poderão ser editadas posteriormente.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        const monthNames = [
            "Janeiro", "Fevereiro", "Março", "Abril", "Maio", "Junho",
            "Julho", "Agosto", "Setembro", "Outubro", "Novembro", "Dezembro"
        ];
        
        document.getElementById('mesRange').addEventListener('input', function() {
            const monthValue = this.value;
            document.getElementById('mes').value = monthValue;
            document.getElementById('monthName').textContent = monthNames[monthValue - 1];
        });
        
        document.getElementById('mes').addEventListener('input', function() {
            const monthValue = this.value;
            document.getElementById('mesRange').value = monthValue;
            document.getElementById('monthName').textContent = monthNames[monthValue - 1];
        });

        document.getElementById('ano').value = new Date().getFullYear();
        
        
        function duplicarContas() {
            const mes = $('#mes').val();
            const ano = $('#ano').val();

            if (!mes || !ano) {
                Swal.fire('Atenção', 'Informe o mês e o ano para duplicar as contas.', 'warning');
                return;
            }

            Swal.fire({
                icon: 'question',
                title: 'Confirmar duplicação?',
                text: `As contas serão duplicadas para ${mes}/${ano}.`,
                showCancelButton: true,
                confirmButtonText: 'Sim, duplicar',
                cancelButtonText: 'Cancelar'

            }).then((result) => {

                if (result.isConfirmed) {

                    $.ajax({
                        url: 'processa/duplicar_contas.php',
                        type: 'POST',
                        dataType: 'json',
                        data: { mes: mes, ano: ano },
                        success: function(resp) {
                        if (resp.sucesso) {
                            Swal.fire('Sucesso!', 'As contas foram duplicadas com sucesso.', 'success').then(() => {
                            document.location.reload(true);
                            });
                        } else {
                            Swal.fire('Erro', resp.mensagem || 'Não foi possível duplicar as contas.', 'error');
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