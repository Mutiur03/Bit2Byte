<?php
require_once __DIR__ . '/db.php';

function redirect_with_message($url, $message) {
    header('Location: ' . $url . '?message=' . urlencode($message));
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = trim($_POST['full_name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $student_id = trim($_POST['student_id'] ?? '');
    $department = trim($_POST['department'] ?? '');
    $batch = trim($_POST['batch'] ?? '');
    $skills = trim($_POST['skills'] ?? '');
    $reason_for_joining = trim($_POST['reason_for_joining'] ?? '');

    if (!$full_name || !$email) {
        redirect_with_message('signup.html', 'All fields are required.');
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        redirect_with_message('signup.html', 'Please enter a valid email address.');
    }

    try {
        $stmt = $pdo->prepare(
            'INSERT INTO members
                (full_name, email, phone, student_id, department, batch, skills, reason_for_joining)
             VALUES
                (:full_name, :email, :phone, :student_id, :department, :batch, :skills, :reason_for_joining)'
        );
        $stmt->execute([
            ':full_name' => $full_name,
            ':email' => $email,
            ':phone' => $phone ?: null,
            ':student_id' => $student_id ?: null,
            ':department' => $department ?: null,
            ':batch' => $batch ?: null,
            ':skills' => $skills ?: null,
            ':reason_for_joining' => $reason_for_joining ?: null,
        ]);
    } catch (PDOException $e) {
        if ($e->getCode() === '23000') {
            redirect_with_message('signup.html', 'Email or student ID already registered.');
        }

        redirect_with_message('signup.html', 'Registration failed. Please try again.');
    }

    redirect_with_message('signup.html', 'Registration successful. Admin will review your application.');
}

redirect_with_message('signup.html', 'Invalid request.');
