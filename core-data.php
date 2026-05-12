<?php

function ensure_core_tables(PDO $pdo) {
    // Database DDL lives in schema.sql and must be applied manually.
}

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
        ':name' => 'Mutiur Rahman',
        ':email' => 'mutiur5bb@gmail.com',
        ':password_hash' => password_hash('12345678', PASSWORD_DEFAULT),
    ]);
}

function init_core_data(PDO $pdo) {
    ensure_core_tables($pdo);
    seed_default_admin($pdo);
}
