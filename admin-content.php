<?php
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/content-data.php';
require_once __DIR__ . '/upload-utils.php';

header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Cache-Control: post-check=0, pre-check=0', false);
header('Pragma: no-cache');
header('Expires: Thu, 01 Jan 1970 00:00:00 GMT');

if (empty($_COOKIE[session_name()])) {
    header('Location: login.php?message=' . urlencode('Please login as admin.'));
    exit;
}

session_start();

if (empty($_SESSION['admin_id'])) {
    header('Location: login.php?message=' . urlencode('Please login as admin.'));
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: admin-dashboard.php');
    exit;
}

init_content_data($pdo);

$type = $_POST['type'] ?? '';
$action = $_POST['action'] ?? '';
$id = (int) ($_POST['id'] ?? 0);
$allowed_types = ['event', 'project', 'team'];
$allowed_actions = ['save', 'delete'];

if (!in_array($type, $allowed_types, true) || !in_array($action, $allowed_actions, true)) {
    header('Location: admin-dashboard.php');
    exit;
}

if ($action === 'delete' && $id > 0) {
    $tables = [
        'event' => 'events',
        'project' => 'projects',
        'team' => 'team_members',
    ];

    $stmt = $pdo->prepare("DELETE FROM {$tables[$type]} WHERE id = :id");
    $stmt->execute([':id' => $id]);
    header('Location: admin-dashboard.php#' . $type . 's');
    exit;
}

if ($type === 'event') {
    $payload = [
        ':title' => trim($_POST['title'] ?? ''),
        ':event_date' => trim($_POST['event_date'] ?? '') ?: null,
        ':status' => trim($_POST['status'] ?? 'Upcoming') ?: 'Upcoming',
        ':description' => trim($_POST['description'] ?? ''),
        ':location' => trim($_POST['location'] ?? ''),
        ':location_icon' => trim($_POST['location_icon'] ?? 'location_on') ?: 'location_on',
        ':sort_order' => (int) ($_POST['sort_order'] ?? 0),
        ':is_visible' => isset($_POST['is_visible']) ? 1 : 0,
    ];

    if ($payload[':title'] !== '') {
        if ($id > 0) {
            $payload[':id'] = $id;
            $stmt = $pdo->prepare(
                'UPDATE events
                 SET title = :title, event_date = :event_date, status = :status, description = :description,
                     location = :location, location_icon = :location_icon, sort_order = :sort_order,
                     is_visible = :is_visible
                 WHERE id = :id'
            );
        } else {
            $stmt = $pdo->prepare(
                'INSERT INTO events (title, event_date, status, description, location, location_icon, sort_order, is_visible)
                 VALUES (:title, :event_date, :status, :description, :location, :location_icon, :sort_order, :is_visible)'
            );
        }
        $stmt->execute($payload);
    }

    header('Location: admin-dashboard.php#events');
    exit;
}

if ($type === 'project') {
    $payload = [
        ':title' => trim($_POST['title'] ?? ''),
        ':description' => trim($_POST['description'] ?? ''),
        ':tags' => trim($_POST['tags'] ?? ''),
        ':sort_order' => (int) ($_POST['sort_order'] ?? 0),
        ':is_visible' => isset($_POST['is_visible']) ? 1 : 0,
    ];

    if ($payload[':title'] !== '') {
        if ($id > 0) {
            $payload[':id'] = $id;
            $stmt = $pdo->prepare(
                'UPDATE projects
                 SET title = :title, description = :description, tags = :tags,
                     sort_order = :sort_order, is_visible = :is_visible
                 WHERE id = :id'
            );
        } else {
            $stmt = $pdo->prepare(
                'INSERT INTO projects (title, description, tags, sort_order, is_visible)
                 VALUES (:title, :description, :tags, :sort_order, :is_visible)'
            );
        }
        $stmt->execute($payload);
    }

    header('Location: admin-dashboard.php#projects');
    exit;
}

$photo_path = trim($_POST['photo_path'] ?? '');
try {
    $uploaded_photo = save_uploaded_image('team_image', 'team');
    if ($uploaded_photo !== null) {
        $photo_path = $uploaded_photo;
    }
} catch (RuntimeException $e) {
    header('Location: admin-dashboard.php#teams');
    exit;
}

$payload = [
    ':name' => trim($_POST['name'] ?? ''),
    ':role' => trim($_POST['role'] ?? ''),
    ':photo_path' => $photo_path,
    ':bio' => trim($_POST['bio'] ?? ''),
    ':sort_order' => (int) ($_POST['sort_order'] ?? 0),
    ':is_visible' => isset($_POST['is_visible']) ? 1 : 0,
];

if ($payload[':name'] !== '' && $payload[':role'] !== '') {
    if ($id > 0) {
        $payload[':id'] = $id;
        $stmt = $pdo->prepare(
            'UPDATE team_members
             SET name = :name, role = :role, photo_path = :photo_path, bio = :bio,
                 sort_order = :sort_order, is_visible = :is_visible
             WHERE id = :id'
        );
    } else {
        $stmt = $pdo->prepare(
            'INSERT INTO team_members (name, role, photo_path, bio, sort_order, is_visible)
             VALUES (:name, :role, :photo_path, :bio, :sort_order, :is_visible)'
        );
    }
    $stmt->execute($payload);
}

header('Location: admin-dashboard.php#teams');
exit;
