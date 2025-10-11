<?php
session_start();

$senhaCorreta   = '529440';
$tempoBloqueio  = 1800; // 30 minutos
$maxTentativas  = 5;

function gerarTokenCSRF() {
    if (function_exists('openssl_random_pseudo_bytes')) {
        return bin2hex(openssl_random_pseudo_bytes(32));
    } else {
        return bin2hex(md5(uniqid(mt_rand(), true)));
    }
}

if (!isset($_POST['csrf_token']) || !isset($_SESSION['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    $_SESSION['erro'] = 1;
    header("Location: ../login.php");
    exit;
}

if (!isset($_SESSION['tentativas_login'])) {
    $_SESSION['tentativas_login'] = 0;
}
if (!isset($_SESSION['ultimo_login_falho'])) {
    $_SESSION['ultimo_login_falho'] = 0;
}

if ($_SESSION['tentativas_login'] >= $maxTentativas) {
    $tempoRestante = time() - $_SESSION['ultimo_login_falho'];
    if ($tempoRestante < $tempoBloqueio) {
        $_SESSION['erro'] = 3;
        header("Location: ../login.php");
        exit;
    } else {
        $_SESSION['tentativas_login'] = 0;
    }
}

$senha = isset($_POST['senha']) ? trim($_POST['senha']) : '';

if ($senha === $senhaCorreta) {
    $_SESSION['usuario'] = array(
        'nome'        => 'Roseli Matos',
        'logado'      => 'sim',
        'data_login'  => date('Y-m-d H:i:s')
    );

    $_SESSION['tentativas_login'] = 0;
    $_SESSION['erro'] = 0;

    if($_REQUEST['acao'] == 'duplicar'){
        header("Location: ../inserirContasMes.php");
        exit;
    }else{
        header("Location: ../index.php");
        exit;
    }
} else {
    $_SESSION['tentativas_login']++;
    $_SESSION['ultimo_login_falho'] = time();
    $_SESSION['erro'] = 2;

    header("Location: ../login.php");
    exit;
}
?>
