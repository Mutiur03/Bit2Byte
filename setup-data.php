<?php

function run_schema_file(PDO $pdo) {
    $schema_path = __DIR__ . '/schema.sql';
    if (!is_file($schema_path)) {
        return ['Schema file not found.'];
    }

    $sql = file_get_contents($schema_path);
    $statements = array_filter(array_map('trim', explode(';', $sql)));
    $messages = [];

    foreach ($statements as $statement) {
        $upper = strtoupper($statement);
        if (str_starts_with($upper, 'CREATE DATABASE') || str_starts_with($upper, 'USE ')) {
            continue;
        }

        $pdo->exec($statement);
    }

    $messages[] = 'Tables checked/created from schema.sql.';
    return $messages;
}

function seed_default_admin(PDO $pdo) {
    $count = (int) $pdo->query('SELECT COUNT(*) FROM admins')->fetchColumn();
    if ($count > 0) {
        return 'Admin seed skipped. Admin already exists.';
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

    return 'Admin seed inserted.';
}

function seed_events(PDO $pdo) {
    $count = (int) $pdo->query('SELECT COUNT(*) FROM events')->fetchColumn();
    if ($count > 0) {
        return 'Event seed skipped. Events already exist.';
    }

    $stmt = $pdo->prepare(
        'INSERT INTO events (title, event_date, description, location, location_icon, sort_order)
         VALUES (:title, :event_date, :description, :location, :location_icon, :sort_order)'
    );
    $dates = [
        date('Y-m-d', strtotime('+15 days')),
        date('Y-m-d', strtotime('+5 days')),
        date('Y-m-d', strtotime('-10 days')),
    ];
    $events = [
        ['Algorithmic Mastery 2.0', $dates[0], 'A hands-on competitive programming session focused on dynamic programming patterns, interview practice, and timed problem sets.', 'Lab 701', 'location_on', 1],
        ['Bit2Byte Intra-Hackathon', $dates[1], 'A team-based build day where students prototype small but useful tools for campus workflows and present their work to mentors.', 'Main Auditorium', 'location_on', 2],
        ['Rust for Beginners', $dates[2], 'An introductory workshop covering ownership, memory safety, and practical examples for students exploring systems programming.', 'Session archive available', 'history', 3],
    ];

    foreach ($events as $event) {
        $stmt->execute([
            ':title' => $event[0],
            ':event_date' => $event[1],
            ':description' => $event[2],
            ':location' => $event[3],
            ':location_icon' => $event[4],
            ':sort_order' => $event[5],
        ]);
    }

    return 'Event seed inserted.';
}

function seed_projects(PDO $pdo) {
    $count = (int) $pdo->query('SELECT COUNT(*) FROM projects')->fetchColumn();
    if ($count > 0) {
        return 'Project seed skipped. Projects already exist.';
    }

    $stmt = $pdo->prepare(
        'INSERT INTO projects (title, description, tags, sort_order)
         VALUES (:title, :description, :tags, :sort_order)'
    );

    $projects = [
        ['Campus Resource Portal', 'A central place for club notes, workshop material, event resources, and onboarding guides for new members.', 'HTML, CSS, JavaScript', 1],
        ['Event Registration System', 'A lightweight registration and attendee tracking tool for club workshops, competitions, and internal training programs.', 'PHP, JSON, Forms', 2],
    ];

    foreach ($projects as $project) {
        $stmt->execute([
            ':title' => $project[0],
            ':description' => $project[1],
            ':tags' => $project[2],
            ':sort_order' => $project[3],
        ]);
    }

    return 'Project seed inserted.';
}

function seed_team_members(PDO $pdo) {
    $count = (int) $pdo->query('SELECT COUNT(*) FROM team_members')->fetchColumn();
    if ($count > 0) {
        return 'Team seed skipped. Team members already exist.';
    }

    $stmt = $pdo->prepare(
        'INSERT INTO team_members (name, role, photo_path, bio, sort_order)
         VALUES (:name, :role, :photo_path, :bio, :sort_order)'
    );

    $members = [
        ['Arif Rahman', 'Club Lead', 'assets/team-arif.png', 'Coordinates club goals, partnerships, and the yearly activity plan.', 1],
        ['Nadia Sultana', 'Workshop Lead', 'assets/team-nadia.png', 'Plans technical sessions and supports members during guided learning tracks.', 2],
        ['Mahin Hasan', 'Project Mentor', 'assets/team-mahin.png', 'Helps teams scope features, review pull requests, and prepare demos.', 3],
        ['Tasnim Farah', 'Events Coordinator', 'assets/team-tasnim.png', 'Manages event logistics, member communication, and participant support.', 4],
    ];

    foreach ($members as $member) {
        $stmt->execute([
            ':name' => $member[0],
            ':role' => $member[1],
            ':photo_path' => $member[2],
            ':bio' => $member[3],
            ':sort_order' => $member[4],
        ]);
    }

    return 'Team seed inserted.';
}

function seed_all_data(PDO $pdo) {
    return [
        seed_default_admin($pdo),
        seed_events($pdo),
        seed_projects($pdo),
        seed_team_members($pdo),
    ];
}
