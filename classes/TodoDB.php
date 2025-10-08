<?php

require_once('credentials.php');

/**
 * Database handling for the todos in the FI38 demo project.
 *
 * All database functionality is defined here.
 *
 * @author  US-FI38 <post@fi38-coding.com>
 * @property object $connection PDO connection to the MariaDB
 * @property object $stmt Database statement handler object.
 * @throws
 * @since 1.0
 */
class TodoDB {
    private $connection;
    private $stmt;


    /**
     * Contructructor of the TodoDB class.
     */
    public function __construct() {
        global $host, $db, $user, $pass;
        try {
            $this->connection = new PDO(
                "mysql:host=$host;dbname=$db;",
                $user,
                $pass
            );
            $this->connection->setAttribute(
                PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log($e->getMessage());
        }
    }

    /**
     * Prepare and execute the given sql statement.
     *
     * @param string $sql The sql statement.
     * @param array $params An array of the needed parameters.
     * @return object $stmt The excecuted statement.
     */
    private function prepareExecuteStatement($sql, $params = []) {
        try {
            $stmt = $this->connection->prepare($sql);
            $stmt->execute($params);
            return $stmt;
        } catch(Exception $e) {
            error_log($e->getMessage());
        }
    }

    public function getTodos() {
        $sql = "SELECT * FROM todo ORDER BY id DESC";
        return $this->prepareExecuteStatement($sql)->fetchAll();
    }

    public function createTodo($title) {
        write_log("info", $title);
        $sql = "INSERT INTO todo (title) VALUES (:title)";
        return $this->prepareExecuteStatement($sql, ["title" => $title]);
    }

    public function deleteTodo($id) {
        $sql = "DELETE FROM todo WHERE id = ?";
        return $this->prepareExecuteStatement($sql, [$id]);
    }

    public function completeTodo($id, $completed) {
        $sql = "UPDATE todo SET completed = ? WHERE id = ?";
        return $this->prepareExecuteStatement($sql, [(int)$completed, $id]);
    }

    public function updateTodo($id, $title) {
        $sql = "UPDATE todo SET title = ?, completed = ? WHERE id = ?";
        return $this->prepareExecuteStatement($sql, [$title, 0, $id]);
    }
}