<?php
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'] ?? '';
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    if (!$name || !$email || !$password) {
        echo json_encode(['success' => false, 'message' => 'All fields are required.']);
        exit;
    }
    $users = file_exists('users.json') ? json_decode(file_get_contents('users.json'), true) : [];
    foreach ($users as $user) {
        if ($user['email'] === $email) {
            echo json_encode(['success' => false, 'message' => 'Email already registered.']);
            exit;
        }
    }
    $users[] = [
        'name' => $name,
        'email' => $email,
        'password' => password_hash($password, PASSWORD_DEFAULT)
    ];
    file_put_contents('users.json', json_encode($users, JSON_PRETTY_PRINT));
    echo json_encode(['success' => true, 'message' => 'Registration successful.']);
    exit;
}
echo json_encode(['success' => false, 'message' => 'Invalid request.']);
