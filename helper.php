<?php

// LOG function
function write_log($action, $data) {
    $log = fopen('log.txt', 'a');
    $timestamp = date('Y-m-d H:i:s');
    fwrite($log, "$timestamp - $action: " . json_encode($data) . "\n");
    fclose($log);
}

// Backend validation: check if given key in array is empty or only whitespace
function validate_input($input, $key, $exit_on_error = true) {
    if (!isset($input[$key]) || empty(trim($input[$key]))) {
        // Return error response for invalid input
        http_response_code(400);
        echo json_encode([
            'status' => 'error',
            'message' => 'TODO-Text darf nicht leer sein! (Server Validierung)'
        ]);
        write_log('POST_ERROR', 'Empty or whitespace-only todo');
        exit;
    }
}