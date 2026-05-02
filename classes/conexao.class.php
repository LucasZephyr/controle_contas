<?php

class Conexao
{
    private $conexao;
    private $consulta;
    private $msg;

    public function __construct($servidor = "Producao")
    {
        $this->carregarEnv(dirname(__DIR__) . '/.env');

        switch ($servidor) {
            case "Producao":

                if ($_SERVER['HTTP_HOST'] != 'localhost') {
                    $dbHost     = getenv('PROD_DB_HOST');
                    $dbPort     = getenv('PROD_DB_PORT');
                    $dbName     = getenv('PROD_DB_NAME');
                    $dbUsername = getenv('PROD_DB_USERNAME');
                    $dbPassword = getenv('PROD_DB_PASSWORD');
                } else {
                    $dbHost     = getenv('LOCAL_DB_HOST');
                    $dbPort     = getenv('LOCAL_DB_PORT');
                    $dbName     = getenv('LOCAL_DB_NAME');
                    $dbUsername = getenv('LOCAL_DB_USERNAME');
                    $dbPassword = getenv('LOCAL_DB_PASSWORD');
                }

                try {
                    $this->conexao = new PDO("mysql:host=$dbHost;port=$dbPort;dbname=$dbName", $dbUsername, $dbPassword);
                    $this->conexao->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                } catch (PDOException $e) {
                    echo "Connection failed: " . $e->getMessage();
                    exit();
                }
                break;

            default:
                die("ERRO: Servidor $servidor inexistente!");
                break;
        }
    }

    private function carregarEnv($path)
    {
        if (!file_exists($path)) return;

        $linhas = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($linhas as $linha) {
            if (str_starts_with(trim($linha), '#')) continue;
            if (!str_contains($linha, '=')) continue;

            [$chave, $valor] = explode('=', $linha, 2);
            $chave = trim($chave);
            $valor = trim($valor);

            if (!array_key_exists($chave, $_ENV)) {
                putenv("$chave=$valor");
                $_ENV[$chave] = $valor;
            }
        }
    }

    public function getConexao()
    {
        return $this->conexao;
    }

    public function setMsg($msg)
    {
        $this->msg = $msg;
    }

    public function getMsg()
    {
        return $this->msg;
    }

    public function setConsulta($consulta)
    {
        $this->consulta = $consulta;
    }

    public function getConsulta()
    {
        return $this->consulta;
    }

    public function numRows($consulta = null)
    {
        if (!$consulta) {
            $consulta = $this->getConsulta();
        }
        return ($consulta) ? $consulta->rowCount() : false;
    }

    public function fetchReg($consulta = null)
    {
        if (!$consulta) {
            $consulta = $this->getConsulta();
        }
        return ($consulta) ? $consulta->fetch(PDO::FETCH_ASSOC) : false;
    }

    public function fetchRow($consulta = null)
    {
        if (!$consulta) {
            $consulta = $this->getConsulta();
        }
        return ($consulta) ? $consulta->fetch(PDO::FETCH_NUM) : false;
    }

    public function lastID()
    {
        return $this->conexao->lastInsertId();
    }

    public function close()
    {
        $this->conexao = null;
    }
}
