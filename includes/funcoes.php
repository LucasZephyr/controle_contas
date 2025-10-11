<?php 

function iniciarSessao($tempo = 7200) {
    session_set_cookie_params($tempo);
    session_start();
}

function redirecionarSeLogado($pagina = 'index.php') {
    if (!empty($_SESSION['usuario']['logado']) && $_SESSION['usuario']['logado'] === 'sim') {
        header("Location: $pagina");
        exit;
    }
}

function inicializarCSRF() {
    $token = gerarTokenCSRF();
    $_SESSION['csrf_token'] = $token;
    return $token;
}

function obterErro() {
    return isset($_SESSION['erro']) ? $_SESSION['erro'] : 0;
}

function gerarTokenCSRF() {
    if (function_exists('openssl_random_pseudo_bytes')) {
        return bin2hex(openssl_random_pseudo_bytes(32));
    } else {
        return bin2hex(md5(uniqid(mt_rand(), true)));
    }
}

function infoSistema() {
    return [
        'nomeSistema' => 'SISTEMA DE CONTROLE',
        'titulo'      => 'Sistema de Controle de Contas',
        'subtitulo'   => 'Roseli Matos',
        'descricao'   => 'Sistema para controle de pagamentos e envio de comprovantes de pagamento'
    ];
}
