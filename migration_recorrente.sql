-- Migração: adiciona coluna recorrente à tabela contas
-- Execute este script uma única vez no banco de dados

ALTER TABLE `contas`
  ADD COLUMN `recorrente` char(1) NOT NULL DEFAULT '0' AFTER `ativo`;

-- Marca todas as contas existentes como recorrentes
-- (todas as contas no banco foram duplicadas manualmente todo mês, portanto são recorrentes)
UPDATE `contas` SET `recorrente` = '1';
