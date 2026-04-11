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

    function setSchema($host) {
        $this->schema = 'zephyr98_controle_contas.';
    }

    function getSchema() {
        return $this->schema;
    }

    // ---- Helpers internos com prepared statements ----

    private function executar($sql, $params = []) {
        try {
            $stmt = $this->conexao->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("SQL::executar — " . $e->getMessage() . " | SQL: $sql");
            return false;
        }
    }

    private function executarEscrita($sql, $params = []) {
        try {
            $stmt = $this->conexao->prepare($sql);
            $stmt->execute($params);
            return ["informacao" => "SUCESSO"];
        } catch (PDOException $e) {
            error_log("SQL::executarEscrita — " . $e->getMessage() . " | SQL: $sql");
            return ["informacao" => "ERROR", "SQLErro" => $e->getMessage()];
        }
    }

    // ---- CONTAS ----

    function buscarContas() {
        $schema = $this->getSchema();
        $sql = "
            SELECT * FROM {$schema}contas c
            WHERE c.ativo = '1'
            ORDER BY CAST(c.ano AS UNSIGNED) DESC,
                     CAST(c.mes AS UNSIGNED) DESC,
                     c.vencimento ASC
        ";
        return $this->executar($sql);
    }

    function buscarContaPorId($idConta) {
        $schema = $this->getSchema();
        $sql = "SELECT * FROM {$schema}contas WHERE serial = ? AND ativo = '1'";
        return $this->executar($sql, [(int)$idConta]);
    }

    function inserirContas($nomeConta, $dataVencimento, $mes, $ano, $recorrente = '1') {
        $schema = $this->getSchema();
        $sql = "
            INSERT INTO {$schema}contas (nome, pago, vencimento, caminho, ativo, mes, ano, recorrente)
            VALUES (?, '', ?, NULL, '1', ?, ?, ?)
        ";
        return $this->executarEscrita($sql, [
            $nomeConta,
            (int)$dataVencimento,
            str_pad((int)$mes, 2, '0', STR_PAD_LEFT),
            (string)$ano,
            $recorrente ? '1' : '0'
        ]);
    }

    function atualizarPagoConta($idConta) {
        $schema = $this->getSchema();
        $sql = "UPDATE {$schema}contas SET pago = '1' WHERE serial = ?";
        return $this->executarEscrita($sql, [(int)$idConta]);
    }

    /**
     * Atualiza dados de uma conta.
     * Se $novoCaminho for null, o campo caminho NÃO é alterado (preserva comprovante existente).
     */
    function atualizarConta($id, $nome, $vencimento, $pago, $recorrente, $novoCaminho = null) {
        $schema = $this->getSchema();

        if ($novoCaminho !== null) {
            $sql = "
                UPDATE {$schema}contas
                SET nome = ?, vencimento = ?, pago = ?, recorrente = ?, caminho = ?
                WHERE serial = ?
            ";
            return $this->executarEscrita($sql, [
                $nome, (int)$vencimento, $pago ? '1' : '0',
                $recorrente ? '1' : '0', $novoCaminho, (int)$id
            ]);
        } else {
            $sql = "
                UPDATE {$schema}contas
                SET nome = ?, vencimento = ?, pago = ?, recorrente = ?
                WHERE serial = ?
            ";
            return $this->executarEscrita($sql, [
                $nome, (int)$vencimento, $pago ? '1' : '0',
                $recorrente ? '1' : '0', (int)$id
            ]);
        }
    }

    // ---- GERAÇÃO AUTOMÁTICA DE MÊS ----

    /**
     * Verifica se já existem contas cadastradas para o mês/ano informados.
     */
    function verificarMesExiste($mes, $ano) {
        $schema = $this->getSchema();
        $sql = "
            SELECT COUNT(*) as total FROM {$schema}contas
            WHERE ativo = '1'
              AND CAST(mes AS UNSIGNED) = ?
              AND CAST(ano AS UNSIGNED) = ?
        ";
        $result = $this->executar($sql, [(int)$mes, (int)$ano]);
        return $result && (int)$result[0]['total'] > 0;
    }

    /**
     * Gera automaticamente as contas de $mesDest/$anoDest copiando as contas
     * recorrentes do mês anterior mais recente que possua registros.
     *
     * Fluxo:
     * 1. Encontra o mês mais recente com contas (antes do destino)
     * 2. Copia as contas marcadas como recorrente = '1'
     * 3. Se não houver nenhuma recorrente (banco sem migração), copia todas
     */
    function gerarMesAutomatico($mesDest, $anoDest) {
        $schema = $this->getSchema();
        $mesDestInt = (int)$mesDest;
        $anoDestInt = (int)$anoDest;
        $mesDestPad = str_pad($mesDestInt, 2, '0', STR_PAD_LEFT);

        // Encontra o mês de referência mais recente anterior ao destino
        $sqlRef = "
            SELECT mes, ano FROM {$schema}contas
            WHERE ativo = '1'
              AND (
                CAST(ano AS UNSIGNED) < ?
                OR (CAST(ano AS UNSIGNED) = ? AND CAST(mes AS UNSIGNED) < ?)
              )
            ORDER BY CAST(ano AS UNSIGNED) DESC, CAST(mes AS UNSIGNED) DESC
            LIMIT 1
        ";
        $ref = $this->executar($sqlRef, [$anoDestInt, $anoDestInt, $mesDestInt]);

        if (empty($ref)) {
            return ["informacao" => "ERROR", "mensagem" => "Nenhum mês de referência encontrado"];
        }

        $mesRef = $ref[0]['mes'];
        $anoRef = $ref[0]['ano'];

        // Tenta copiar apenas recorrentes
        try {
            $sqlRecorrentes = "
                INSERT INTO {$schema}contas (nome, pago, vencimento, caminho, ativo, mes, ano, recorrente)
                SELECT nome, '', vencimento, NULL, ativo, ?, ?, '1'
                FROM {$schema}contas
                WHERE ativo = '1' AND recorrente = '1'
                  AND CAST(mes AS UNSIGNED) = ?
                  AND CAST(ano AS UNSIGNED) = ?
            ";
            $stmt = $this->conexao->prepare($sqlRecorrentes);
            $stmt->execute([$mesDestPad, (string)$anoDestInt, (int)$mesRef, (int)$anoRef]);
            $inseridos = $stmt->rowCount();

            // Fallback: nenhuma recorrente encontrada (banco sem migração)
            if ($inseridos === 0) {
                $sqlTodas = "
                    INSERT INTO {$schema}contas (nome, pago, vencimento, caminho, ativo, mes, ano, recorrente)
                    SELECT nome, '', vencimento, NULL, ativo, ?, ?, '1'
                    FROM {$schema}contas
                    WHERE ativo = '1'
                      AND CAST(mes AS UNSIGNED) = ?
                      AND CAST(ano AS UNSIGNED) = ?
                ";
                $stmtFb = $this->conexao->prepare($sqlTodas);
                $stmtFb->execute([$mesDestPad, (string)$anoDestInt, (int)$mesRef, (int)$anoRef]);
            }

            return ["informacao" => "SUCESSO", "mesRef" => $mesRef, "anoRef" => $anoRef];

        } catch (PDOException $e) {
            error_log("SQL::gerarMesAutomatico — " . $e->getMessage());
            return ["informacao" => "ERROR", "SQLErro" => $e->getMessage()];
        }
    }

    // ---- Métodos legados mantidos para compatibilidade ----

    public function executarQuery($sql) {
        try {
            $stmt = $this->conexao->query($sql);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("SQL::executarQuery — " . $e->getMessage());
            return false;
        }
    }

    public function executarQueryBoleano($sql) {
        try {
            $this->conexao->query($sql);
            return ["informacao" => "SUCESSO"];
        } catch (PDOException $e) {
            return ["informacao" => "ERROR", "SQLErro" => $e->getMessage(), "SQL" => $sql];
        }
    }

    public function executarQueryBoleanoTransaction($sql) {
        try {
            $this->conexao->beginTransaction();
            $stmt = $this->conexao->exec($sql);
            if ($stmt !== false) {
                $this->conexao->commit();
                return ["informacao" => "SUCESSO"];
            }
            $this->conexao->rollBack();
            return ["informacao" => "ERROR", "SQLErro" => "Erro ao executar transação", "SQL" => $sql];
        } catch (PDOException $e) {
            $this->conexao->rollBack();
            return ["informacao" => "ERROR", "SQLErro" => $e->getMessage(), "SQL" => $sql];
        }
    }

    /** @deprecated Mantido para não quebrar inserirContasMes.php antigo */
    function buscarContasBase() {
        $schema = $this->getSchema();
        $data = new DateTime('now');
        $data->modify('-1 month');
        $mes = $data->format('m');
        $ano = $data->format('Y');
        $sql = "
            SELECT * FROM {$schema}contas c
            WHERE c.ativo = '1' AND c.mes = ? AND c.ano = ?
            ORDER BY c.vencimento ASC
        ";
        return $this->executar($sql, [$mes, $ano]);
    }
}
?>
