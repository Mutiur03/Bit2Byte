<?php
function redirect_with_message($url, $message) {
    header('Location: ' . $url . '?message=' . urlencode($message));
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (!$name || !$email || !$password) {
        redirect_with_message('signup.html', 'All fields are required.');
    }

    $users = file_exists('users.json') ? json_decode(file_get_contents('users.json'), true) : [];
    foreach ($users as $user) {
        if ($user['email'] === $email) {
            redirect_with_message('signup.html', 'Email already registered.');
        }
    }

    $users[] = [
        'name' => $name,
        'email' => $email,
        'password' => password_hash($password, PASSWORD_DEFAULT)
    ];
    file_put_contents('users.json', json_encode($users, JSON_PRETTY_PRINT));

    redirect_with_message('login.html', 'Registration successful. Please sign in.');
}

redirect_with_message('signup.html', 'Invalid request.');
