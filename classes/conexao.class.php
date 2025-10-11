<?php

class Conexao{
    private $conexao;
    private $consulta;
    private $msg;
    
    public function __construct($servidor = "Producao") {
        
        switch ($servidor){            
            case "Producao":

                if($_SERVER['HTTP_HOST'] != 'localhost'){
                    $dbHost = 'localhost'; 
                    $dbPort = '3306'; 
                    $dbName = 'zephyr98_controle_contas';
                    $dbUsername = 'zephyr98_root_contas'; 
                    $dbPassword = '529440Lucas.'; 
                }else{
                    $dbHost = 'localhost'; 
                    $dbPort = '3306'; 
                    $dbName = 'controle_contas';
                    $dbUsername = 'root'; 
                    $dbPassword = '';    
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
    
    // Método para obter conexão
    public function getConexao() {
        return $this->conexao;
    }

    // Método para configurar mensagem
    public function setMsg($msg) {
        $this->msg = $msg;
    }

    // Método para obter mensagem
    public function getMsg() {
        return $this->msg;
    }

    // Método para configurar consulta
    public function setConsulta($consulta) {
        $this->consulta = $consulta;
    }

    // Método para obter consulta
    public function getConsulta() {
        return $this->consulta;
    }

    // Método para obter número de linhas
    public function numRows($consulta = null) {
        if (!$consulta) {
            $consulta = $this->getConsulta();
        }
        return ($consulta) ? $consulta->rowCount() : false;
    }

    // Método para buscar registro associativo
    public function fetchReg($consulta = null) {
        if (!$consulta) {
            $consulta = $this->getConsulta();
        }
        return ($consulta) ? $consulta->fetch(PDO::FETCH_ASSOC) : false;
    }

    // Método para buscar linha
    public function fetchRow($consulta = null) {
        if (!$consulta) {
            $consulta = $this->getConsulta();
        }
        return ($consulta) ? $consulta->fetch(PDO::FETCH_NUM) : false;
    }

    // Método para obter último ID inserido
    public function lastID() {
        return $this->conexao->lastInsertId();
    }

    // Método para fechar conexão (não necessário com PDO, mas mantido para compatibilidade)
    public function close() {
        $this->conexao = null;
    }
}
?>
