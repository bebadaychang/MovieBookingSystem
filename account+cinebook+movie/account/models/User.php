<?php

class User {

    private $conn;
    private $table = "users";

    public function __construct($db) {
        $this->conn = $db;
    }

    // Lấy user theo username hoặc email
    public function findUser($login) {

        $sql = "SELECT * FROM users WHERE username = :login OR email = :login LIMIT 1";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':login', $login);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Lấy tất cả user (test admin)
    public function getAll() {

        $sql = "SELECT * FROM users";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}