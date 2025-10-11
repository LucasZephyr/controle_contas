<?php

// ini_set('display_errors', 1);
// error_reporting(E_ALL);

require 'includes/funcoes.php';

iniciarSessao();
redirecionarSeLogado();
$csrf_token = inicializarCSRF();
$erro = obterErro();
$infoSistema = infoSistema();

?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= htmlspecialchars($infoSistema['nomeSistema']) ?></title>

    <!-- Bootstrap e ícones -->
    <?php include 'includes/cabecalho.php'; ?>

    <style>
        body {
            background-color: #f6f9ff;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        .header {
            background-color: #fff;
            padding: 0.8rem 1.5rem;
            border-bottom: 1px solid #eee;
        }

        .login-wrapper {
            flex-grow: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
        }

        .card {
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        }

        input::placeholder {
            color: gray;
            font-family: Arial, sans-serif;
            text-align: center;
            font-weight: 100;
            font-size: 18px;
        }

        footer {
            text-align: center;
            padding: 10px;
            font-size: 0.9rem;
            color: #0d6efd;
            background-color: transparent;
        }
    </style>
</head>
<body>

    <!-- cabecalho -->
    <header class="header d-flex justify-content-between align-items-center">
        <h5 class="text-primary fw-bold m-0"><?= htmlspecialchars($infoSistema['nomeSistema']) ?></h5>
        <div id="relogio" class="text-secondary small"></div>
    </header>

    <!-- login -->
    <main class="login-wrapper">
        <div class="card p-4 col-11 col-sm-8 col-md-6 col-lg-4 text-center bg-white">
            <h5 class="mb-3 text-secondary"><?= htmlspecialchars($infoSistema['titulo']) ?></h5>
            <h4 class="fw-bold text-primary mb-4"><?= htmlspecialchars($infoSistema['subtitulo']) ?></h4>

            <form action="processa/processa_login.php" method="post" id="formLogin">
                <input type="password" id="senha" name="senha"
                       class="form-control text-center bg-warning-subtle mb-4"
                       placeholder="Senha" required>

                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">

                <button type="submit" class="btn btn-primary w-100">
                    <i class="bi bi-door-open"></i> Entrar
                </button>

                <select name="acao" id="acao" class="form-select form-select-sm mt-3">
                    <option value="inserir">inserir</option>
                    <option value="duplicar">Duplicar</option>
                </select>

                <?php if ($erro == 2){ ?>
                    <p class="text-danger mt-4 mb-0">Login inválido</p>
                <?php }elseif ($erro == 3){ ?>
                    <p class="text-danger mt-4 mb-0">
                        Login bloqueado por questões de segurança.<br>
                        Aguarde 30 minutos para tentar novamente!
                    </p>
                <?php } ?>

                <?php if (!empty($infoSistema['descricao'])){ ?>
                    <p class="text-muted mt-4"><?= htmlspecialchars($infoSistema['descricao']) ?></p>
                <?php } ?>
            </form>
        </div>
    </main>

    <!-- Rodapé -->
    <footer>
        Criador por Lucas Zephyr
    </footer>

    <script>
        function atualizarRelogio() {
            const agora = new Date();
            const pad = n => n.toString().padStart(2, '0');
            document.getElementById("relogio").textContent =
                `${pad(agora.getHours())}:${pad(agora.getMinutes())}:${pad(agora.getSeconds())}`;
        }
        setInterval(atualizarRelogio, 1000);
        atualizarRelogio();
    </script>

</body>
</html>
