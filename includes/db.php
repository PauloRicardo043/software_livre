<?php
// Arquivo de conexão com o banco de dados
require_once 'config.php';

/**
 * Classe para gerenciar a conexão com o banco de dados
 */
class Database {
    private $connection;
    private static $instance;
    
    /**
     * Construtor privado - padrão Singleton
     */
    private function __construct() {
        try {
            $this->connection = new PDO(
                'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4',
                DB_USER,
                DB_PASS,
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false
                ]
            );
        } catch (PDOException $e) {
            die('Erro de conexão com o banco de dados: ' . $e->getMessage());
        }
    }
    
    /**
     * Obtém a instância única da conexão (Singleton)
     */
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    /**
     * Obtém a conexão PDO
     */
    public function getConnection() {
        return $this->connection;
    }
    
    /**
     * Executa uma consulta SQL com parâmetros
     * 
     * @param string $sql A consulta SQL
     * @param array $params Os parâmetros para a consulta
     * @return PDOStatement O resultado da consulta
     */
    public function query($sql, $params = []) {
        try {
            $stmt = $this->connection->prepare($sql);
            $stmt->execute($params);
            return $stmt;
        } catch (PDOException $e) {
            die('Erro na consulta: ' . $e->getMessage());
        }
    }
    
    /**
     * Obtém um único registro do banco de dados
     * 
     * @param string $sql A consulta SQL
     * @param array $params Os parâmetros para a consulta
     * @return array|false O registro encontrado ou false
     */
    public function fetchOne($sql, $params = []) {
        return $this->query($sql, $params)->fetch();
    }
    
    /**
     * Obtém todos os registros do banco de dados
     * 
     * @param string $sql A consulta SQL
     * @param array $params Os parâmetros para a consulta
     * @return array Os registros encontrados
     */
    public function fetchAll($sql, $params = []) {
        return $this->query($sql, $params)->fetchAll();
    }
    
    /**
     * Insere um registro no banco de dados
     * 
     * @param string $table A tabela para inserção
     * @param array $data Os dados para inserção (coluna => valor)
     * @return int O ID do registro inserido
     */
    public function insert($table, $data) {
        $columns = implode(', ', array_keys($data));
        $placeholders = implode(', ', array_fill(0, count($data), '?'));
        
        $sql = "INSERT INTO {$table} ({$columns}) VALUES ({$placeholders})";
        
        $this->query($sql, array_values($data));
        return $this->connection->lastInsertId();
    }
    
    /**
     * Atualiza um registro no banco de dados
     * 
     * @param string $table A tabela para atualização
     * @param array $data Os dados para atualização (coluna => valor)
     * @param string $where A condição WHERE
     * @param array $params Os parâmetros para a condição WHERE
     * @return int O número de linhas afetadas
     */
    public function update($table, $data, $where, $params = []) {
        $set = [];
        foreach ($data as $column => $value) {
            $set[] = "{$column} = ?";
        }
        $set = implode(', ', $set);
        
        $sql = "UPDATE {$table} SET {$set} WHERE {$where}";
        
        $stmt = $this->query($sql, array_merge(array_values($data), $params));
        return $stmt->rowCount();
    }
    
    /**
     * Exclui um registro do banco de dados
     * 
     * @param string $table A tabela para exclusão
     * @param string $where A condição WHERE
     * @param array $params Os parâmetros para a condição WHERE
     * @return int O número de linhas afetadas
     */
    public function delete($table, $where, $params = []) {
        $sql = "DELETE FROM {$table} WHERE {$where}";
        
        $stmt = $this->query($sql, $params);
        return $stmt->rowCount();
    }
}
