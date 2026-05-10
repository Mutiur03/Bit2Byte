<?php
function redirect_with_message($url, $message) {
    header('Location: ' . $url . '?message=' . urlencode($message));
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (!$email || !$password) {
        redirect_with_message('login.html', 'Email and password required.');
    }

    $users = file_exists('users.json') ? json_decode(file_get_contents('users.json'), true) : [];
    foreach ($users as $user) {
        if ($user['email'] === $email && password_verify($password, $user['password'])) {
            header('Location: welcome.html');
            exit;
        }
    }

    redirect_with_message('login.html', 'Invalid credentials.');
}

redirect_with_message('login.html', 'Invalid request.');
