<?php


function seed_default_admin(PDO $pdo) {
    $count = (int) $pdo->query('SELECT COUNT(*) FROM admins')->fetchColumn();
    if ($count > 0) {
        return;
    }

    $stmt = $pdo->prepare(
        'INSERT INTO admins (name, email, password_hash)
         VALUES (:name, :email, :password_hash)'
    );
    $stmt->execute([
        ':name' => env_value('ADMIN_DEFAULT_NAME', 'Mutiur Rahman'),
        ':email' => env_value('ADMIN_DEFAULT_EMAIL', 'mutiur5bb@gmail.com'),
        ':password_hash' => password_hash(env_value('ADMIN_DEFAULT_PASSWORD', '12345678'), PASSWORD_DEFAULT),
    ]);
}

function init_core_data(PDO $pdo) {
    try {
    seed_default_admin($pdo);
    } catch (Exception $e) {
        error_log('Error initializing core data: ' . $e->getMessage());
    }
}
