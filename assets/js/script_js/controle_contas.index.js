
function cadastrarConta(){

    let dados = {
        acao: "cadastrarConta",
        nomeConta: $("#nomeConta").val(),
        dataVencimento: $("#dataVencimento").val()
    };

    if (dados.nomeConta === "" || dados.dataVencimento === "") {
        Swal.fire({
            icon: 'warning',
            title: 'Atenção',
            text: 'Preencha o campo nome e data de vencimento!',
            showConfirmButton: true
        });
        return;
    }

    $.ajax({
        url: "processa/intermediarioPHP.php",
        data: dados,                             
        cache: false,
        dataType: "json",
        type: "POST",
        success: function(resp){
            
            if (resp.informacao === "SUCESSO") {
                Swal.fire({
                    icon: 'success',
                    title: 'Sucesso',
                    text: 'Conta cadastrada!',
                    showConfirmButton: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        document.location.reload(true);
                    }
                });
            }
 
                
        },
        error: function(){
            Swal.close();            
        }              
    });

}

function marcarPagoConta(idConta){

    let dados = {
        acao: "marcarPagoConta",
        idConta: idConta
    };

    $.ajax({
        url: "processa/intermediarioPHP.php",
        data: dados,                             
        cache: false,
        dataType: "json",
        type: "POST",
        success: function(resp){
            
            if (resp.informacao === "SUCESSO") {
                Swal.fire({
                    icon: 'success',
                    title: 'Sucesso',
                    text: 'Conta marcada como paga!',
                    showConfirmButton: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        document.location.reload(true);
                    }
                });
            }
 
                
        },
        error: function(){
            Swal.close();            
        }              
    });
}

function abrirModalAcoes(idConta) {

    let dados = {
        acao: 'buscarConta',
        id: idConta
    };

  $.ajax({
    url: 'processa/intermediarioPHP.php', 
    method: 'POST',
    data: dados,
    dataType: 'json',

    success: function(resp) {

        if (resp && resp.length > 0) {
            const c = resp[0];
            let pagoChecked = (c.pago == 1) ? 'checked' : '';

            let html = `
                <form id="formAtualizarConta" enctype="multipart/form-data">
                    <input type="hidden" name="idSerialModal" value="${c.serial}">

                    <div class="row align-items-center g-3">
                    <div class="col-md-6">
                        <label for="nome" class="form-label">Nome</label>
                        <input type="text" class="form-control" id="nome" name="nome" value="${c.nome}">
                    </div>

                    <div class="col-md-3">
                        <label for="vencimento" class="form-label">Vencimento</label>
                        <input type="number" class="form-control" id="vencimento" name="vencimento" value="${c.vencimento}">
                    </div>

                    <div class="col-md-3 d-flex align-items-center">
                        <div class="form-check form-switch mt-4">
                        <input class="form-check-input" type="checkbox" id="pago" name="pago" value="1" ${pagoChecked}>
                        <label class="form-check-label ms-2" for="pago">Pago</label>
                        </div>
                    </div>
                    </div>

                    <hr>

                    <div class="row mt-3">
                    <div class="col-md-12">
                        <label for="arquivo" class="form-label">Anexar comprovante (PDF até 2 MB)</label>
                        <input type="file" class="form-control form-control-sm" id="arquivo" name="arquivo" accept=".pdf">
                    </div>
                    </div>

                    ${
                    c.caminho
                        ? `
                        <div class="row mt-3">
                            <div class="col-md-12">
                            <label class="form-label">Comprovante atual:</label>
                            <div class="ratio ratio-16x9 border rounded">
                                <iframe src="uploads/${c.caminho}" frameborder="0"></iframe>
                            </div>
                            </div>
                        </div>
                        `
                        : ''
                    }
                </form>
            `;


            $('#modalAcaoBody').html(html);
            $('#modalAcao').modal('show');


    } else {
        Swal.fire('Erro', 'Não foi possível carregar a conta.', 'error');
    }


    },
    error: function() {
      Swal.fire('Erro', 'Falha na comunicação com o servidor.', 'error');
    }
  });

}


function atualizarConta() {

    let form = $('#formAtualizarConta')[0];
    let formData = new FormData(form);

    const fileInput = $('#arquivo')[0];
    if (fileInput.files.length > 0) {
        const file = fileInput.files[0];
        
        if (file.type !== 'application/pdf') {
            Swal.fire('Atenção', 'O arquivo deve ser um PDF.', 'warning');
            return;
        }
        
        if (file.size > 2 * 1024 * 1024) { // 2 MB
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
        success: function(resp) {
            if (resp.sucesso) {
                Swal.fire({
                    icon: 'success',
                    title: 'Sucesso',
                    text: 'Conta atualizada com sucesso!'
                }).then(() => {
                    $('#modalAcao').modal('hide');
                    document.location.reload();
                });
            } else {
                Swal.fire('Erro', resp.mensagem || 'Falha ao atualizar a conta.', 'error');
            }
        },
        error: function() {
            Swal.fire('Erro', 'Erro de comunicação com o servidor.', 'error');
        }
    });

}
