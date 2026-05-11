<?php

function ensure_content_tables(PDO $pdo) {
    $pdo->exec(
        "CREATE TABLE IF NOT EXISTS events (
            id INT AUTO_INCREMENT PRIMARY KEY,
            title VARCHAR(160) NOT NULL,
            event_date DATE NULL,
            status VARCHAR(40) NOT NULL DEFAULT 'Upcoming',
            description TEXT NULL,
            location VARCHAR(160) NULL,
            location_icon VARCHAR(80) NOT NULL DEFAULT 'location_on',
            sort_order INT NOT NULL DEFAULT 0,
            is_visible TINYINT(1) NOT NULL DEFAULT 1,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4"
    );

    $pdo->exec(
        "CREATE TABLE IF NOT EXISTS projects (
            id INT AUTO_INCREMENT PRIMARY KEY,
            title VARCHAR(160) NOT NULL,
            description TEXT NULL,
            tags VARCHAR(255) NULL,
            sort_order INT NOT NULL DEFAULT 0,
            is_visible TINYINT(1) NOT NULL DEFAULT 1,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4"
    );

    $pdo->exec(
        "CREATE TABLE IF NOT EXISTS team_members (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(120) NOT NULL,
            role VARCHAR(120) NOT NULL,
            photo_path VARCHAR(255) NULL,
            bio TEXT NULL,
            sort_order INT NOT NULL DEFAULT 0,
            is_visible TINYINT(1) NOT NULL DEFAULT 1,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4"
    );
}

function seed_content_tables(PDO $pdo) {
    $count = (int) $pdo->query('SELECT COUNT(*) FROM events')->fetchColumn();
    if ($count === 0) {
        $stmt = $pdo->prepare(
            'INSERT INTO events (title, event_date, status, description, location, location_icon, sort_order)
             VALUES (:title, :event_date, :status, :description, :location, :location_icon, :sort_order)'
        );

        $events = [
            ['Algorithmic Mastery 2.0', '2024-10-24', 'Upcoming', 'A hands-on competitive programming session focused on dynamic programming patterns, interview practice, and timed problem sets.', 'Lab 701', 'location_on', 1],
            ['Bit2Byte Intra-Hackathon', '2024-11-02', 'Upcoming', 'A team-based build day where students prototype small but useful tools for campus workflows and present their work to mentors.', 'Main Auditorium', 'location_on', 2],
            ['Rust for Beginners', '2024-09-15', 'Completed', 'An introductory workshop covering ownership, memory safety, and practical examples for students exploring systems programming.', 'Session archive available', 'history', 3],
        ];

        foreach ($events as $event) {
            $stmt->execute([
                ':title' => $event[0],
                ':event_date' => $event[1],
                ':status' => $event[2],
                ':description' => $event[3],
                ':location' => $event[4],
                ':location_icon' => $event[5],
                ':sort_order' => $event[6],
            ]);
        }
    }

    $count = (int) $pdo->query('SELECT COUNT(*) FROM projects')->fetchColumn();
    if ($count === 0) {
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
    }

    $count = (int) $pdo->query('SELECT COUNT(*) FROM team_members')->fetchColumn();
    if ($count === 0) {
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
    }
}

function init_content_data(PDO $pdo) {
    ensure_content_tables($pdo);
    seed_content_tables($pdo);
}

function visible_events(PDO $pdo) {
    $stmt = $pdo->query('SELECT * FROM events WHERE is_visible = 1 ORDER BY sort_order ASC, event_date ASC, id ASC');
    return $stmt->fetchAll();
}

function visible_projects(PDO $pdo) {
    $stmt = $pdo->query('SELECT * FROM projects WHERE is_visible = 1 ORDER BY sort_order ASC, id ASC');
    return $stmt->fetchAll();
}

function visible_team_members(PDO $pdo) {
    $stmt = $pdo->query('SELECT * FROM team_members WHERE is_visible = 1 ORDER BY sort_order ASC, id ASC');
    return $stmt->fetchAll();
}

function all_events(PDO $pdo) {
    $stmt = $pdo->query('SELECT * FROM events ORDER BY sort_order ASC, event_date ASC, id ASC');
    return $stmt->fetchAll();
}

function all_projects(PDO $pdo) {
    $stmt = $pdo->query('SELECT * FROM projects ORDER BY sort_order ASC, id ASC');
    return $stmt->fetchAll();
}

function all_team_members(PDO $pdo) {
    $stmt = $pdo->query('SELECT * FROM team_members ORDER BY sort_order ASC, id ASC');
    return $stmt->fetchAll();
}

function e($value) {
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}
