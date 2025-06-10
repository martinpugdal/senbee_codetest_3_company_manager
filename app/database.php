<?php
class Database {
    private PDO $pdo;

    public function __construct($dsn, $username, $password) {
        try {
            $this->pdo = new PDO($dsn, $username, $password);
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            error_log('Database connection failed: ' . $e->getMessage());
            die('A database error occurred. Please try again later.');
        }
    }

    public function getConnection(): PDO
    {
        return $this->pdo;
    }
}