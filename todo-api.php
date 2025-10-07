<?php

header('Content-Type: application/json');

require_once('helper.php');

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
        echo json_encode($todos);
        write_log('GET', $todos);
        break;
    case 'POST':
        // Add new entry to json file
        $data = file_get_contents('php://input');
        $input = json_decode($data, true);

        validate_input($input, 'todo');

        $new_todo = ["id" => uniqid(), "title" => $input['todo'], "completed" => false];
        $todos[] = $new_todo;
        file_put_contents($file, json_encode($todos));
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

        foreach ($todos as $index => $todo) {
            if ($todo['id'] == $data['id']) {
                // $todo holds only a copy, but we need to change the array itself
                $todos[$index]['completed'] = $data['completed'];
                break;
            }
        }

        file_put_contents($file, json_encode($todos));
        echo json_encode(['status' => 'success']);
        break;
    case 'DELETE':
        // Get data from the input stream.
        $data = json_decode(file_get_contents('php://input'), true);
        // Filter Todo to delete from the list.
        $todos = array_values(
            array_filter($todos,
                function($todo) use ($data) {
                    return $todo['id'] !== $data['id'];
        }));
        // Write the Todos back to JSON file.
        file_put_contents('todo.json', json_encode($todos));
        // Tell the client the success of the operation.
        echo json_encode(['status' => 'success']);
        write_log("DELETE", $data);
        break;
}
