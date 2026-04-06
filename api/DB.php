<?php

class DB
{
    private $host = 'localhost';
    private $port = '3306';
    private $db = 'sae'; // <- ici
    private $db_user = 'root'; // <- ici
    private $db_pass = ''; // <- ici

    public function connect()
    {

        $conn = new mysqli($this->host, $this->db_user, $this->db_pass, $this->db, $this->port);
        if ($conn->connect_error) {
            throw new RuntimeException("Database connection failed");
        }
        $conn->set_charset("utf8mb4");
        return $conn;
    }

    public function query($sql, $types = "", $args = [])
    {
        // types est un string qui contient les types des arguments
        // Par ex : "ssds" signifie que les 4 arguments sont de type string, string, decimal, string
        $conn = $this->connect();

        $stmt = $conn->prepare($sql);
        if ($stmt === false) {
            $conn->close();
            throw new RuntimeException("Database query preparation failed");
        }
        if (!empty($types))
        {
            $stmt->bind_param($types, ...$args);
        }

        if (!$stmt->execute()) {
            $stmt->close();
            $conn->close();
            throw new RuntimeException("Database query execution failed");
        }

        $id = $conn->insert_id;
        $stmt->close();
        $conn->close();
        return $id;
    }

    public function select($sql, $types = "", $args = [])
    {
        $conn = $this->connect();

        $stmt = $conn->prepare($sql);
        if ($stmt === false) {
            $conn->close();
            throw new RuntimeException("Database query preparation failed");
        }
        if (!empty($types))
        {
            $stmt->bind_param($types, ...$args);
        }
        if (!$stmt->execute()) {
            $stmt->close();
            $conn->close();
            throw new RuntimeException("Database query execution failed");
        }

        $result = $stmt->get_result();
        $data = $result->fetch_all(MYSQLI_ASSOC);

        $stmt->close();
        $conn->close();
        return $data;
    }

    public static function clean($input): string
    {
        return htmlspecialchars($input);
    }
}
