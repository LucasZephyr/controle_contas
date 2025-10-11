<?php

require_once("conexao.class.php");

class SQL {

    private $conexao;
    var $schema = '';

    public function __construct() {
        $oConexao = new Conexao();
        $this->conexao = $oConexao->getConexao();

        $this->setSchema($_SERVER['HTTP_HOST']);
    }

    function setSchema ($schema){
        if($_SERVER['HTTP_HOST'] != 'localhost'){
            $schema = 'zephyr98_controle_contas.';
        }else{
            $schema = '';
        }

        $this->schema = $schema;
    }

    function getSchema (){
        return $this->schema;
    }

    public function executarQuery($sql) {
        try {
            $stmt = $this->conexao->query($sql);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            echo "Query errada: " . $e->getMessage();
            return false;
        }
    }

    public function executarQueryBoleano($sql) {
        try {
            $this->conexao->query($sql);
            return ["informacao" => "SUCESSO"];
        } catch (PDOException $e) {
            return [
                "informacao" => "ERROR",
                "SQLErro" => $e->getMessage(),
                "SQL" => $sql
            ];
        }
    }

    public function executarQueryBoleanoTransaction($sql) {
        try {
            $this->conexao->beginTransaction();
            $stmt = $this->conexao->exec($sql);

            if ($stmt !== false) {
                $this->conexao->commit();
                return ["informacao" => "SUCESSO"];
            } else {
                $this->conexao->rollBack();
                return [
                    "informacao" => "ERROR",
                    "SQLErro" => "Erro ao executar transação",
                    "SQL" => $sql
                ];
            }
        } catch (PDOException $e) {
            $this->conexao->rollBack();
            return [
                "informacao" => "ERROR",
                "SQLErro" => $e->getMessage(),
                "SQL" => $sql
            ];
        }
    }


    # ---------------------------FUNCOES ----------------------------

    function inserirContas($nomeConta, $dataVencimento){    
        
        $schema = $this->getSchema();
        $sql = "
            INSERT INTO {$schema}contas (nome, vencimento, ativo, mes, ano) VALUES ('$nomeConta', '$dataVencimento', '1', '10', '2025')
        ";

        #echo '<pre>';print_r($sql);echo '</pre>';exit;
        return $this->executarQueryBoleano($sql); 
    }


    function atualizarPagoConta($idConta){    
        
        $schema = $this->getSchema();
        $sql = "
            update {$schema}contas set pago = 1 where serial = $idConta
        ";

        #echo '<pre>';print_r($sql);echo '</pre>';exit;
        return $this->executarQueryBoleano($sql); 
    }

    function buscarContas(){
        $schema = $this->getSchema();
        $sql = "
            SELECT * FROM {$schema}contas c where c.ativo = 1 ORDER BY c.vencimento ASC
        ";

        #echo '<pre>';print_r($sql);echo '</pre>';exit;
        return $this->executarQuery($sql);
    }

    function atualizarConta($id, $nome, $vencimento, $pago, $caminho = null){
        $schema = $this->getSchema();
        $sql = "
            UPDATE {$schema}contas 
            SET 
                nome = '$nome', 
                vencimento = '$vencimento', 
                pago = $pago ,
                caminho = ".($caminho ? "'$caminho'" : "NULL")."
                
                WHERE serial = $id
        ";
        #echo '<pre>';print_r($sql);echo '</pre>';exit;
        return $this->executarQueryBoleano($sql);
    }

    function buscarContaPorId($idConta){
        $schema = $this->getSchema();
        $sql = "
            SELECT * FROM {$schema}contas c where c.serial = $idConta and c.ativo = 1
        ";
        #echo '<pre>';print_r($sql);echo '</pre>';exit;
        return $this->executarQuery($sql);
    
    }

    function buscarContasBase(){
        $schema = $this->getSchema();
        $sql = "
            SELECT * FROM {$schema}contas c where c.ativo = 1 and c.mes = '10' and c.ano = '2025' ORDER BY c.vencimento ASC
        ";

        #echo '<pre>';print_r($sql);echo '</pre>';exit;
        return $this->executarQuery($sql);
        
    }












    

  
}

?>