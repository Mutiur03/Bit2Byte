<?php
require_once __DIR__ . '/db.php';

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

$stmt = $pdo->query('SELECT * FROM members ORDER BY created_at DESC');
$members = $stmt->fetchAll();

function e($value) {
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}
?>
<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Admin Dashboard | Bit2Byte</title>
    <link
      href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@300;400;500;600;700&family=Inter:wght@300;400;500;600&display=swap"
      rel="stylesheet"
    />
    <link
      href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
      rel="stylesheet"
    />
    <link rel="stylesheet" href="auth.css" />
  </head>
  <body class="overflow-auto">
    <div class="bg-overlay grid-pattern"></div>
    <div class="bg-overlay dot-pattern"></div>
    <div class="glow-spot"></div>

    <main class="container-fluid min-vh-100 py-4 py-md-5">
      <div class="auth-card auth-card-wide mx-auto w-100">
        <div class="d-flex flex-column flex-md-row justify-content-between gap-3 mb-4">
          <div>
            <h1 class="brand-text mb-2">Member Applications</h1>
            <p class="policy mb-0">Logged in as <?= e($_SESSION['admin_name']) ?></p>
          </div>
          <a href="logout.php" class="btn-submit text-center text-decoration-none px-4 py-3">
            <span class="btn-text">Logout</span>
          </a>
        </div>

        <?php if (!$members): ?>
          <p class="policy mb-0">No member applications yet.</p>
        <?php else: ?>
          <div class="admin-table-wrap">
            <table class="admin-table">
              <thead>
                <tr>
                  <th>Name</th>
                  <th>Contact</th>
                  <th>Academic</th>
                  <th>Skills</th>
                  <th>Reason</th>
                  <th>Status</th>
                  <th>Action</th>
                  <th>Submitted</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($members as $member): ?>
                  <tr>
                    <td>
                      <?= e($member['full_name']) ?><br />
                    </td>
                    <td>
                      <?= e($member['email']) ?><br />
                      <small><?= e($member['phone']) ?></small>
                    </td>
                    <td>
                      <?= e($member['department']) ?><br />
                      <small>ID: <?= e($member['student_id']) ?> | Batch: <?= e($member['batch']) ?></small>
                    </td>
                    <td><?= nl2br(e($member['skills'])) ?></td>
                    <td><?= nl2br(e($member['reason_for_joining'])) ?></td>
                    <td><span class="status-pill"><?= e($member['status']) ?></span></td>
                    <td>
                      <form class="admin-actions" action="member-status.php" method="post">
                        <input type="hidden" name="member_id" value="<?= e($member['id']) ?>" />
                        <?php if ($member['status'] === 'pending'): ?>
                          <button class="btn btn-sm btn-success" name="status" value="approved" type="submit">
                            Approve
                          </button>
                          <button class="btn btn-sm btn-danger" name="status" value="rejected" type="submit">
                            Reject
                          </button>
                        <?php else: ?>
                          <span class="text-secondary">No action</span>
                        <?php endif; ?>
                      </form>
                    </td>
                    <td>
                      <small><?= e(date('F j, Y, g:i a', strtotime($member['created_at']))) ?></small>
                    </td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
        <?php endif; ?>
      </div>
    </main>
  </body>
</html>
