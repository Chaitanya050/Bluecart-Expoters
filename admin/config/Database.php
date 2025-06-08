<?php
class Database {
    private static $instance = null;
    private $connection;
    
    private function __construct() {
        require_once 'config.php';
        
        try {
            $this->connection = new PDO(
                "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME,
                DB_USER,
                DB_PASS,
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4"
                ]
            );
        } catch (PDOException $e) {
            die("Connection failed: " . $e->getMessage());
        }
    }
    
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    public function getConnection() {
        return $this->connection;
    }
    
    public function query($sql, $params = []) {
        try {
            $stmt = $this->connection->prepare($sql);
            $stmt->execute($params);
            return $stmt;
        } catch (PDOException $e) {
            die("Query failed: " . $e->getMessage());
        }
    }
    
    public function insert($table, $data) {
        $columns = implode(', ', array_keys($data));
        $values = implode(', ', array_fill(0, count($data), '?'));
        
        $sql = "INSERT INTO {$table} ({$columns}) VALUES ({$values})";
        
        try {
            $stmt = $this->connection->prepare($sql);
            $stmt->execute(array_values($data));
            return $this->connection->lastInsertId();
        } catch (PDOException $e) {
            die("Insert failed: " . $e->getMessage());
        }
    }
    
    public function update($table, $data, $where) {
        $set = implode(' = ?, ', array_keys($data)) . ' = ?';
        $whereClause = implode(' = ? AND ', array_keys($where)) . ' = ?';
        
        $sql = "UPDATE {$table} SET {$set} WHERE {$whereClause}";
        
        try {
            $stmt = $this->connection->prepare($sql);
            $stmt->execute(array_merge(array_values($data), array_values($where)));
            return $stmt->rowCount();
        } catch (PDOException $e) {
            die("Update failed: " . $e->getMessage());
        }
    }
    
    public function delete($table, $where) {
        $whereClause = implode(' = ? AND ', array_keys($where)) . ' = ?';
        
        $sql = "DELETE FROM {$table} WHERE {$whereClause}";
        
        try {
            $stmt = $this->connection->prepare($sql);
            $stmt->execute(array_values($where));
            return $stmt->rowCount();
        } catch (PDOException $e) {
            die("Delete failed: " . $e->getMessage());
        }
    }
} 