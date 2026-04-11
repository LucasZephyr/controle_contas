
// -------------------------------------------------------
// Nova Conta (modal por mês)
// -------------------------------------------------------

function abrirModalNovaConta(mes, ano) {
    var nomesMeses = [
        'Janeiro', 'Fevereiro', 'Março', 'Abril', 'Maio', 'Junho',
        'Julho', 'Agosto', 'Setembro', 'Outubro', 'Novembro', 'Dezembro'
    ];

    $('#novaContaMes').val(mes);
    $('#novaContaAno').val(ano);
    $('#novaContaNome').val('');
    $('#novaContaVencimento').val('');
    $('#novaContaRecorrente').prop('checked', true);

    var nomeMes = nomesMeses[parseInt(mes, 10) - 1];
    $('#novaContaMesAnoLabel').text(nomeMes + ' de ' + ano);

    $('#modalNovaConta').modal('show');
}

function salvarNovaConta() {
    var nome       = $('#novaContaNome').val().trim();
    var vencimento = $('#novaContaVencimento').val();
    var mes        = $('#novaContaMes').val();
    var ano        = $('#novaContaAno').val();
    var recorrente = $('#novaContaRecorrente').is(':checked') ? 1 : 0;

    if (!nome || !vencimento) {
        Swal.fire({ icon: 'warning', title: 'Atenção', text: 'Preencha o nome e o dia de vencimento!' });
        return;
    }

    $.ajax({
        url: 'processa/intermediarioPHP.php',
        method: 'POST',
        data: {
            acao: 'cadastrarConta',
            nomeConta: nome,
            dataVencimento: vencimento,
            mes: mes,
            ano: ano,
            recorrente: recorrente
        },
        dataType: 'json',
        success: function (resp) {
            if (resp.informacao === 'SUCESSO') {
                Swal.fire({ icon: 'success', title: 'Sucesso', text: 'Conta adicionada!' })
                    .then(function () {
                        $('#modalNovaConta').modal('hide');
                        document.location.reload(true);
                    });
            } else {
                Swal.fire('Erro', resp.mensagem || 'Erro ao adicionar conta.', 'error');
            }
        },
        error: function () {
            Swal.fire('Erro', 'Falha na comunicação com o servidor.', 'error');
        }
    });
}


// -------------------------------------------------------
// Marcar como pago
// -------------------------------------------------------

function marcarPagoConta(idConta) {
    $.ajax({
        url: 'processa/intermediarioPHP.php',
        data: { acao: 'marcarPagoConta', idConta: idConta },
        cache: false,
        dataType: 'json',
        type: 'POST',
        success: function (resp) {
            if (resp.informacao === 'SUCESSO') {
                Swal.fire({ icon: 'success', title: 'Sucesso', text: 'Conta marcada como paga!', showConfirmButton: true })
                    .then(function (result) {
                        if (result.isConfirmed) { document.location.reload(true); }
                    });
            }
        },
        error: function () { Swal.close(); }
    });
}


// -------------------------------------------------------
// Abrir modal de edição
// -------------------------------------------------------

function abrirModalAcoes(idConta) {
    $.ajax({
        url: 'processa/intermediarioPHP.php',
        method: 'POST',
        data: { acao: 'buscarConta', id: idConta },
        dataType: 'json',
        success: function (resp) {
            if (!resp || !resp.length) {
                Swal.fire('Erro', 'Não foi possível carregar a conta.', 'error');
                return;
            }

            var c = resp[0];
            var pagoChecked       = (c.pago == 1)       ? 'checked' : '';
            var recorrenteChecked = (c.recorrente == '1') ? 'checked' : '';

            var comprovante = '';
            if (c.caminho) {
                comprovante = '<div class="row mt-3">' +
                    '<div class="col-md-12">' +
                    '<label class="form-label">Comprovante atual:</label>' +
                    '<div class="ratio ratio-16x9 border rounded">' +
                    '<iframe src="uploads/' + c.caminho + '" frameborder="0"></iframe>' +
                    '</div></div></div>';
            }

            var html =
                '<form id="formAtualizarConta" enctype="multipart/form-data">' +
                    '<input type="hidden" name="idSerialModal" value="' + c.serial + '">' +

                    '<div class="row align-items-center g-3">' +
                        '<div class="col-md-5">' +
                            '<label for="nome" class="form-label">Nome</label>' +
                            '<input type="text" class="form-control" id="nome" name="nome" value="' + c.nome + '">' +
                        '</div>' +
                        '<div class="col-md-3">' +
                            '<label for="vencimento" class="form-label">Vencimento</label>' +
                            '<input type="number" class="form-control" id="vencimento" name="vencimento" value="' + c.vencimento + '">' +
                        '</div>' +
                        '<div class="col-md-2 d-flex align-items-center">' +
                            '<div class="form-check form-switch mt-4">' +
                                '<input class="form-check-input" type="checkbox" id="pago" name="pago" value="1" ' + pagoChecked + '>' +
                                '<label class="form-check-label ms-2" for="pago">Pago</label>' +
                            '</div>' +
                        '</div>' +
                        '<div class="col-md-2 d-flex align-items-center">' +
                            '<div class="form-check form-switch mt-4">' +
                                '<input class="form-check-input" type="checkbox" id="recorrente" name="recorrente" value="1" ' + recorrenteChecked + '>' +
                                '<label class="form-check-label ms-2" for="recorrente">Recorrente</label>' +
                            '</div>' +
                        '</div>' +
                    '</div>' +

                    '<hr>' +

                    '<div class="row mt-3">' +
                        '<div class="col-md-12">' +
                            '<label for="arquivo" class="form-label">Anexar comprovante (PDF até 2 MB)</label>' +
                            '<input type="file" class="form-control form-control-sm" id="arquivo" name="arquivo" accept=".pdf">' +
                        '</div>' +
                    '</div>' +

                    comprovante +
                '</form>';

            $('#modalAcaoBody').html(html);
            $('#modalAcao').modal('show');
        },
        error: function () {
            Swal.fire('Erro', 'Falha na comunicação com o servidor.', 'error');
        }
    });
}


// -------------------------------------------------------
// Salvar edição
// -------------------------------------------------------

function atualizarConta() {
    var form     = $('#formAtualizarConta')[0];
    var formData = new FormData(form);

    var fileInput = $('#arquivo')[0];
    if (fileInput.files.length > 0) {
        var file = fileInput.files[0];
        if (file.type !== 'application/pdf') {
            Swal.fire('Atenção', 'O arquivo deve ser um PDF.', 'warning');
            return;
        }
        if (file.size > 2 * 1024 * 1024) {
            Swal.fire('Atenção', 'O arquivo deve ter no máximo 2 MB.', 'warning');
            return;
        }
    }

    $.ajax({
        url: 'processa/atualizar_conta.php',
        method: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        dataType: 'json',
        success: function (resp) {
            if (resp.sucesso) {
                Swal.fire({ icon: 'success', title: 'Sucesso', text: 'Conta atualizada com sucesso!' })
                    .then(function () {
                        $('#modalAcao').modal('hide');
                        document.location.reload();
                    });
            } else {
                Swal.fire('Erro', resp.mensagem || 'Falha ao atualizar a conta.', 'error');
            }
        },
        error: function () {
            Swal.fire('Erro', 'Erro de comunicação com o servidor.', 'error');
        }
    });
}


// -------------------------------------------------------
// Cadastrar conta (legado – usado em inserirContasMes.php)
// -------------------------------------------------------

function cadastrarConta() {
    var dados = {
        acao: 'cadastrarConta',
        nomeConta: $('#nomeConta').val(),
        dataVencimento: $('#dataVencimento').val()
    };

    if (!dados.nomeConta || !dados.dataVencimento) {
        Swal.fire({ icon: 'warning', title: 'Atenção', text: 'Preencha o campo nome e data de vencimento!' });
        return;
    }

    $.ajax({
        url: 'processa/intermediarioPHP.php',
        data: dados,
        cache: false,
        dataType: 'json',
        type: 'POST',
        success: function (resp) {
            if (resp.informacao === 'SUCESSO') {
                Swal.fire({ icon: 'success', title: 'Sucesso', text: 'Conta cadastrada!', showConfirmButton: true })
                    .then(function (result) {
                        if (result.isConfirmed) { document.location.reload(true); }
                    });
            }
        },
        error: function () { Swal.close(); }
    });
}
