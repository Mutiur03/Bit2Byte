<?php
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    if (!$email || !$password) {
        echo json_encode(['success' => false, 'message' => 'Email and password required.']);
        exit;
    }
    $users = file_exists('users.json') ? json_decode(file_get_contents('users.json'), true) : [];
    foreach ($users as $user) {
        if ($user['email'] === $email && password_verify($password, $user['password'])) {
            echo json_encode(['success' => true, 'message' => 'Login successful.']);
            exit;
        }
    }
    echo json_encode(['success' => false, 'message' => 'Invalid credentials.']);
    exit;
}
echo json_encode(['success' => false, 'message' => 'Invalid request.']);
