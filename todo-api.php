<?php

header('Content-Type: application/json');

// LOG function in PHP
function write_log($action, $data) {
    $log = fopen('log.txt', 'a');
    $timestamp = date('Y-m-d H:i:s');
    fwrite($log, "$timestamp - $action: " . json_encode($data) . "\n");
    fclose($log);
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
        echo json_encode($todos);
        write_log('GET', $todos);
        break;
    case 'POST':
        // Add new entry to json file
        $data = file_get_contents('php://input');
        $input = json_decode($data, true);
        $todos[] = $input['todo'];
        file_put_contents($file, json_encode($todos));
        echo json_encode(['status' => 'success']);
        write_log('POST', $input['todo']);
        break;
    case 'PUT':
        // Placeholder for updating a TODO
        break;
    case 'DELETE':
        // Placeholder for deleting a TODO
        break;
}
