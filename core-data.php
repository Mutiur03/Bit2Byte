<?php

function ensure_core_tables(PDO $pdo) {
    $pdo->exec(
        "CREATE TABLE IF NOT EXISTS admins (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(120) NOT NULL,
            email VARCHAR(190) NOT NULL UNIQUE,
            password_hash VARCHAR(255) NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4"
    );

    $pdo->exec(
        "CREATE TABLE IF NOT EXISTS members (
            id INT AUTO_INCREMENT PRIMARY KEY,
            full_name VARCHAR(160) NOT NULL,
            email VARCHAR(190) NOT NULL UNIQUE,
            phone VARCHAR(60) NULL,
            student_id VARCHAR(80) NULL UNIQUE,
            department VARCHAR(120) NULL,
            batch VARCHAR(80) NULL,
            photo_path VARCHAR(255) NULL,
            skills TEXT NULL,
            reason_for_joining TEXT NULL,
            status VARCHAR(40) NOT NULL DEFAULT 'pending',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4"
    );

    $columns = $pdo->query("SHOW COLUMNS FROM members LIKE 'photo_path'")->fetchAll();
    if (!$columns) {
        $pdo->exec('ALTER TABLE members ADD photo_path VARCHAR(255) NULL AFTER batch');
    }
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
