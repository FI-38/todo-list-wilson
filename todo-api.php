<?php

header('Content-Type: application/json');

require_once('helper.php');
require_once('credentials.php');

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
    write_log("PDO", "Successful created pdo object.");
} catch (\PDOException $e) {
    write_log("PDOException", $e->getMessage() . " in "
              . $e->getFile() . " on line " . $e->getLine());
}

// Read current todos in json file
$file = 'todo.json';
if (file_exists($file)) {
    $json_data = file_get_contents($file);
    $todos = json_decode($json_data, true);
} else {
    $todos = [];
}

switch ($_SERVER['REQUEST_METHOD']) {
    case 'GET':
        // Return all todos as JSON string
        $statement = $pdo->query("SELECT * FROM todo");
        $todo_items = $statement->fetchAll();
        echo json_encode($todo_items);
        write_log('GET', $todos);
        break;
    case 'POST':
        // Add new entry to json file
        $data = file_get_contents('php://input');
        $input = json_decode($data, true);

        // Validate the given todo.
        validate_input($input, 'todo');

        // Insert the todo into our database.
        $statement = $pdo->prepare("INSERT INTO todo (title, completed) VALUES (:title, :completed)");
        $statement->execute(['title' => $input['todo'], 'completed' => 0]);

        echo json_encode(['status' => 'success']);
        write_log('POST', $input['todo']);
        break;
    case 'PUT':
        // Add new entry to json file
        $data = file_get_contents('php://input');
        $input = json_decode($data, true);

        validate_input($input, 'title');
        validate_input($input, 'id');

        foreach ($todos as $index => $todo) {
            if ($todo['id'] == $input['id']) {
                // $todo holds only a copy, but we need to change the array itself
                $todos[$index]['title'] = $input['title'];
                break;
            }
        }

        file_put_contents($file, json_encode($todos));
        echo json_encode(['status' => 'success']);
        write_log('PUT', $input['title']);
        break;
    case 'PATCH':
        $data = json_decode(file_get_contents('php://input'), true);

        // Update the completion status of our todo in the database.
        $statement = $pdo->prepare("UPDATE todo SET completed = :completed WHERE id = :id");
        $statement->execute(['id' => $data['id'], 'completed' => (int)$data['completed']]);

        echo json_encode(['status' => 'success']);
        break;
    case 'DELETE':
        // Get data from the input stream.
        $data = json_decode(file_get_contents('php://input'), true);

        // Delete the todo from our database.
        $statement = $pdo->prepare("DELETE FROM todo WHERE id=:id");
        $statement->execute(['id' => $data['id']]);

        // Tell the client the success of the operation.
        echo json_encode(['status' => 'success']);
        write_log("DELETE", $data);
        break;
}
