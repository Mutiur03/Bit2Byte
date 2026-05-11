<?php
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/core-data.php';
require_once __DIR__ . '/content-data.php';

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

init_core_data($pdo);
init_content_data($pdo);

$stmt = $pdo->query('SELECT * FROM members ORDER BY created_at DESC');
$members = $stmt->fetchAll();
$events = all_events($pdo);
$projects = all_projects($pdo);
$team_members = all_team_members($pdo);

$pending_members = count(array_filter($members, fn($member) => $member['status'] === 'pending'));
$visible_events = count(array_filter($events, fn($event) => (int) $event['is_visible'] === 1));
$visible_projects = count(array_filter($projects, fn($project) => (int) $project['is_visible'] === 1));
$visible_team_members = count(array_filter($team_members, fn($team_member) => (int) $team_member['is_visible'] === 1));
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
    <link rel="stylesheet" href="admin.css" />
  </head>
  <body class="admin-page overflow-auto">
    <div class="bg-overlay grid-pattern"></div>
    <div class="bg-overlay dot-pattern"></div>
    <div class="glow-spot"></div>

    <main class="container-fluid min-vh-100 py-3 py-md-5 admin-shell">
      <div class="auth-card auth-card-wide admin-dashboard mx-auto w-100">
        <div class="admin-topbar">
          <div class="admin-title-block">
            <h1 class="brand-text mb-2">Admin Dashboard</h1>
            <p class="policy mb-0">Logged in as <?= e($_SESSION['admin_name']) ?></p>
          </div>
          <div class="admin-top-actions">
            <a href="index.php" class="btn btn-primary">
              View Site
            </a>
            <a href="logout.php" class="btn btn-primary">
              Logout
            </a>
          </div>
        </div>

        <div class="admin-summary-grid">
          <a class="admin-summary-card" href="#members" data-admin-tab-target="members">
            <span>Pending</span>
            <strong><?= e($pending_members) ?></strong>
            <small>Member applications</small>
          </a>
          <a class="admin-summary-card" href="#events" data-admin-tab-target="events">
            <span>Events</span>
            <strong><?= e($visible_events) ?></strong>
            <small><?= e(count($events)) ?> total records</small>
          </a>
          <a class="admin-summary-card" href="#projects" data-admin-tab-target="projects">
            <span>Projects</span>
            <strong><?= e($visible_projects) ?></strong>
            <small><?= e(count($projects)) ?> total records</small>
          </a>
          <a class="admin-summary-card" href="#teams" data-admin-tab-target="teams">
            <span>Team</span>
            <strong><?= e($visible_team_members) ?></strong>
            <small><?= e(count($team_members)) ?> total records</small>
          </a>
        </div>

        <div class="tabs dashboard-tabs d-flex flex-wrap mb-4">
          <a href="#members" class="tab-btn active" data-admin-tab-target="members">Members</a>
          <a href="#events" class="tab-btn" data-admin-tab-target="events">Events</a>
          <a href="#projects" class="tab-btn" data-admin-tab-target="projects">Projects</a>
          <a href="#teams" class="tab-btn" data-admin-tab-target="teams">Team</a>
        </div>

        <section id="members" class="admin-section admin-tab-panel is-active" data-admin-panel="members">
          <div class="admin-section-heading">
            <div>
              <h2 class="admin-section-title">Member Applications</h2>
              <p class="admin-section-subtitle">Review student requests and update approval status.</p>
            </div>
            <span class="status-pill"><?= e(count($members)) ?> Total</span>
          </div>
          <?php if (!$members): ?>
            <p class="policy mb-0">No member applications yet.</p>
          <?php else: ?>
            <div class="admin-table-wrap">
              <table class="table table-hover align-middle mb-0 admin-table">
                <thead>
                  <tr>
                    <th>Name</th>
                    <th>Photo</th>
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
                      <td data-label="Name"><?= e($member['full_name']) ?></td>
                      <td data-label="Photo">
                        <?php if (!empty($member['photo_path'])): ?>
                          <img class="admin-thumb" src="<?= e($member['photo_path']) ?>" alt="<?= e($member['full_name']) ?>" />
                        <?php else: ?>
                          <span class="text-secondary">No photo</span>
                        <?php endif; ?>
                      </td>
                      <td data-label="Contact">
                        <?= e($member['email']) ?><br />
                        <small><?= e($member['phone']) ?></small>
                      </td>
                      <td data-label="Academic">
                        <?= e($member['department']) ?><br />
                        <small>ID: <?= e($member['student_id']) ?> | Batch: <?= e($member['batch']) ?></small>
                      </td>
                      <td data-label="Skills"><?= nl2br(e($member['skills'])) ?></td>
                      <td data-label="Reason"><?= nl2br(e($member['reason_for_joining'])) ?></td>
                      <td data-label="Status"><span class="status-pill"><?= e($member['status']) ?></span></td>
                      <td data-label="Action">
                        <form class="admin-actions" action="member-status.php" method="post">
                          <input type="hidden" name="member_id" value="<?= e($member['id']) ?>" />
                          <?php if ($member['status'] === 'pending'): ?>
                            <button class="btn btn-sm btn-success" name="status" value="approved" type="submit">Approve</button>
                            <button class="btn btn-sm btn-danger" name="status" value="rejected" type="submit">Reject</button>
                          <?php else: ?>
                            <span class="text-secondary">No action</span>
                          <?php endif; ?>
                        </form>
                      </td>
                      <td data-label="Submitted"><small><?= e(date('F j, Y, g:i a', strtotime($member['created_at']))) ?></small></td>
                    </tr>
                  <?php endforeach; ?>
                </tbody>
              </table>
            </div>
          <?php endif; ?>
        </section>

        <section id="events" class="admin-section admin-tab-panel" data-admin-panel="events">
          <div class="admin-section-heading">
            <div>
              <h2 class="admin-section-title">Events</h2>
              <p class="admin-section-subtitle">Manage homepage event cards, dates, locations, and visibility.</p>
            </div>
            <button class="btn btn-primary" type="button" data-open-event-modal data-mode="add">
              Add Event
            </button>
          </div>
          <div class="management-table-wrap">
            <table class="table table-hover align-middle mb-0 management-table">
              <thead>
                <tr>
                  <th>Event</th>
                  <th>Status</th>
                  <th>Date</th>
                  <th>Location</th>
                  <th>Visibility</th>
                  <th class="text-end">Actions</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($events as $event): ?>
                  <tr>
                    <td data-label="Event">
                      <strong><?= e($event['title']) ?></strong>
                      <small><?= e($event['description']) ?></small>
                    </td>
                    <td data-label="Status"><?= e($event['status']) ?></td>
                    <td data-label="Date"><?= e($event['event_date'] ? date('M j, Y', strtotime($event['event_date'])) : 'No date') ?></td>
                    <td data-label="Location"><?= e($event['location']) ?></td>
                    <td data-label="Visibility"><span class="status-pill"><?= e($event['is_visible'] ? 'Visible' : 'Hidden') ?></span></td>
                    <td data-label="Actions">
                      <div class="row-actions">
                        <button class="btn btn-sm btn-outline-info" type="button" data-open-preview data-title="<?= e($event['title']) ?>" data-kicker="<?= e($event['status']) ?>" data-description="<?= e($event['description']) ?>" data-meta="<?= e(($event['event_date'] ? date('M j, Y', strtotime($event['event_date'])) : 'No date') . ' | ' . $event['location']) ?>">Preview</button>
                        <button class="btn btn-sm btn-outline-light" type="button" data-open-event-modal data-mode="edit" data-id="<?= e($event['id']) ?>" data-title="<?= e($event['title']) ?>" data-event-date="<?= e($event['event_date']) ?>" data-status="<?= e($event['status']) ?>" data-description="<?= e($event['description']) ?>" data-location="<?= e($event['location']) ?>" data-location-icon="<?= e($event['location_icon']) ?>" data-sort-order="<?= e($event['sort_order']) ?>" data-is-visible="<?= e($event['is_visible']) ?>">Edit</button>
                        <button class="btn btn-sm btn-outline-danger" type="button" data-open-delete data-type="event" data-id="<?= e($event['id']) ?>" data-title="<?= e($event['title']) ?>">Delete</button>
                      </div>
                    </td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
        </section>

        <section id="projects" class="admin-section admin-tab-panel" data-admin-panel="projects">
          <div class="admin-section-heading">
            <div>
              <h2 class="admin-section-title">Projects</h2>
              <p class="admin-section-subtitle">Control project title, description, technology tags, and ordering.</p>
            </div>
            <button class="btn btn-primary" type="button" data-open-project-modal data-mode="add">
              Add Project
            </button>
          </div>
          <div class="management-table-wrap">
            <table class="table table-hover align-middle mb-0 management-table">
              <thead>
                <tr>
                  <th>Project</th>
                  <th>Tags</th>
                  <th>Sort</th>
                  <th>Visibility</th>
                  <th class="text-end">Actions</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($projects as $project): ?>
                  <tr>
                    <td data-label="Project">
                      <strong><?= e($project['title']) ?></strong>
                      <small><?= e($project['description']) ?></small>
                    </td>
                    <td data-label="Tags"><?= e($project['tags'] ?: 'No tags') ?></td>
                    <td data-label="Sort"><?= e($project['sort_order']) ?></td>
                    <td data-label="Visibility"><span class="status-pill"><?= e($project['is_visible'] ? 'Visible' : 'Hidden') ?></span></td>
                    <td data-label="Actions">
                      <div class="row-actions">
                        <button class="btn btn-sm btn-outline-info" type="button" data-open-preview data-title="<?= e($project['title']) ?>" data-kicker="<?= e($project['tags'] ?: 'Project') ?>" data-description="<?= e($project['description']) ?>" data-meta="<?= e('Sort ' . $project['sort_order']) ?>">Preview</button>
                        <button class="btn btn-sm btn-outline-light" type="button" data-open-project-modal data-mode="edit" data-id="<?= e($project['id']) ?>" data-title="<?= e($project['title']) ?>" data-description="<?= e($project['description']) ?>" data-tags="<?= e($project['tags']) ?>" data-sort-order="<?= e($project['sort_order']) ?>" data-is-visible="<?= e($project['is_visible']) ?>">Edit</button>
                        <button class="btn btn-sm btn-outline-danger" type="button" data-open-delete data-type="project" data-id="<?= e($project['id']) ?>" data-title="<?= e($project['title']) ?>">Delete</button>
                      </div>
                    </td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
        </section>

        <section id="teams" class="admin-section admin-tab-panel" data-admin-panel="teams">
          <div class="admin-section-heading">
            <div>
              <h2 class="admin-section-title">Team</h2>
              <p class="admin-section-subtitle">Update organizers, roles, photos, bios, and display order.</p>
            </div>
            <button class="btn btn-primary" type="button" data-open-team-modal data-mode="add">
              Add Member
            </button>
          </div>
          <div class="management-table-wrap">
            <table class="table table-hover align-middle mb-0 management-table">
              <thead>
                <tr>
                  <th>Member</th>
                  <th>Role</th>
                  <th>Photo</th>
                  <th>Sort</th>
                  <th>Visibility</th>
                  <th class="text-end">Actions</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($team_members as $team_member): ?>
                  <tr>
                    <td data-label="Member">
                      <strong><?= e($team_member['name']) ?></strong>
                      <small><?= e($team_member['bio']) ?></small>
                    </td>
                    <td data-label="Role"><?= e($team_member['role']) ?></td>
                    <td data-label="Photo">
                      <?php if ($team_member['photo_path']): ?>
                        <img class="admin-thumb" src="<?= e($team_member['photo_path']) ?>" alt="<?= e($team_member['name']) ?>" />
                      <?php else: ?>
                        <span class="text-secondary">No photo</span>
                      <?php endif; ?>
                    </td>
                    <td data-label="Sort"><?= e($team_member['sort_order']) ?></td>
                    <td data-label="Visibility"><span class="status-pill"><?= e($team_member['is_visible'] ? 'Visible' : 'Hidden') ?></span></td>
                    <td data-label="Actions">
                      <div class="row-actions">
                        <button class="btn btn-sm btn-outline-info" type="button" data-open-preview data-title="<?= e($team_member['name']) ?>" data-kicker="<?= e($team_member['role']) ?>" data-description="<?= e($team_member['bio']) ?>" data-meta="<?= e($team_member['photo_path'] ?: 'No photo') ?>" data-image="<?= e($team_member['photo_path']) ?>">Preview</button>
                        <button class="btn btn-sm btn-outline-light" type="button" data-open-team-modal data-mode="edit" data-id="<?= e($team_member['id']) ?>" data-name="<?= e($team_member['name']) ?>" data-role="<?= e($team_member['role']) ?>" data-photo-path="<?= e($team_member['photo_path']) ?>" data-bio="<?= e($team_member['bio']) ?>" data-sort-order="<?= e($team_member['sort_order']) ?>" data-is-visible="<?= e($team_member['is_visible']) ?>">Edit</button>
                        <button class="btn btn-sm btn-outline-danger" type="button" data-open-delete data-type="team" data-id="<?= e($team_member['id']) ?>" data-title="<?= e($team_member['name']) ?>">Delete</button>
                      </div>
                    </td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
        </section>
      </div>
    </main>

    <div class="admin-modal" id="event-modal" aria-hidden="true">
      <div class="admin-modal-panel" role="dialog" aria-modal="true" aria-labelledby="event-modal-title">
        <div class="admin-modal-header">
          <h3 id="event-modal-title">Event</h3>
          <button class="modal-close" type="button" data-close-modal>&times;</button>
        </div>
        <form class="admin-modal-form" action="admin-content.php" method="post">
          <input type="hidden" name="type" value="event" />
          <input type="hidden" name="action" value="save" />
          <input type="hidden" name="id" />
          <div class="admin-form-grid">
            <label>Title<input class="form-input" name="title" required /></label>
            <label>Date<input class="form-input" name="event_date" type="date" /></label>
            <label>Status<input class="form-input" name="status" /></label>
            <label>Location<input class="form-input" name="location" /></label>
            <label>Icon<input class="form-input" name="location_icon" /></label>
            <label>Sort<input class="form-input" name="sort_order" type="number" /></label>
          </div>
          <label>Description<textarea class="form-input form-textarea" name="description" rows="4"></textarea></label>
          <label class="admin-check mt-3"><input type="checkbox" name="is_visible" /> Visible</label>
          <div class="modal-actions">
            <button class="btn btn-outline-secondary" type="button" data-close-modal>Cancel</button>
            <button class="btn btn-primary" type="submit">Save</button>
          </div>
        </form>
      </div>
    </div>

    <div class="admin-modal" id="project-modal" aria-hidden="true">
      <div class="admin-modal-panel" role="dialog" aria-modal="true" aria-labelledby="project-modal-title">
        <div class="admin-modal-header">
          <h3 id="project-modal-title">Project</h3>
          <button class="modal-close" type="button" data-close-modal>&times;</button>
        </div>
        <form class="admin-modal-form" action="admin-content.php" method="post">
          <input type="hidden" name="type" value="project" />
          <input type="hidden" name="action" value="save" />
          <input type="hidden" name="id" />
          <div class="admin-form-grid">
            <label>Title<input class="form-input" name="title" required /></label>
            <label>Tags<input class="form-input" name="tags" /></label>
            <label>Sort<input class="form-input" name="sort_order" type="number" /></label>
          </div>
          <label>Description<textarea class="form-input form-textarea" name="description" rows="4"></textarea></label>
          <label class="admin-check mt-3"><input type="checkbox" name="is_visible" /> Visible</label>
          <div class="modal-actions">
            <button class="btn btn-outline-secondary" type="button" data-close-modal>Cancel</button>
            <button class="btn btn-primary" type="submit">Save</button>
          </div>
        </form>
      </div>
    </div>

    <div class="admin-modal" id="team-modal" aria-hidden="true">
      <div class="admin-modal-panel" role="dialog" aria-modal="true" aria-labelledby="team-modal-title">
        <div class="admin-modal-header">
          <h3 id="team-modal-title">Team Member</h3>
          <button class="modal-close" type="button" data-close-modal>&times;</button>
        </div>
        <form class="admin-modal-form" action="admin-content.php" method="post" enctype="multipart/form-data">
          <input type="hidden" name="type" value="team" />
          <input type="hidden" name="action" value="save" />
          <input type="hidden" name="id" />
          <div class="admin-form-grid">
            <label>Name<input class="form-input" name="name" required /></label>
            <label>Role<input class="form-input" name="role" required /></label>
            <label>Photo path<input class="form-input" name="photo_path" /></label>
            <label>Upload image<input class="form-input" name="team_image" type="file" accept="image/jpeg,image/png,image/webp,image/gif" /></label>
            <label>Sort<input class="form-input" name="sort_order" type="number" /></label>
          </div>
          <div class="current-image-preview" id="team-current-image-wrap" hidden>
            <span>Current image</span>
            <img id="team-current-image" alt="Current team member image" />
          </div>
          <label>Bio<textarea class="form-input form-textarea" name="bio" rows="4"></textarea></label>
          <label class="admin-check mt-3"><input type="checkbox" name="is_visible" /> Visible</label>
          <div class="modal-actions">
            <button class="btn btn-outline-secondary" type="button" data-close-modal>Cancel</button>
            <button class="btn btn-primary" type="submit">Save</button>
          </div>
        </form>
      </div>
    </div>

    <div class="admin-modal" id="preview-modal" aria-hidden="true">
      <div class="admin-modal-panel" role="dialog" aria-modal="true" aria-labelledby="preview-title">
        <div class="admin-modal-header">
          <h3 id="preview-title">Preview</h3>
          <button class="modal-close" type="button" data-close-modal>&times;</button>
        </div>
        <div class="preview-card">
          <img class="preview-image" id="preview-image" alt="" />
          <span id="preview-kicker"></span>
          <h4 id="preview-heading"></h4>
          <p id="preview-description"></p>
          <small id="preview-meta"></small>
        </div>
      </div>
    </div>

    <div class="admin-modal" id="delete-modal" aria-hidden="true">
      <div class="admin-modal-panel admin-modal-panel-small" role="dialog" aria-modal="true" aria-labelledby="delete-title">
        <div class="admin-modal-header">
          <h3 id="delete-title">Delete Record</h3>
          <button class="modal-close" type="button" data-close-modal>&times;</button>
        </div>
        <p class="admin-section-subtitle" id="delete-copy"></p>
        <form action="admin-content.php" method="post">
          <input type="hidden" name="type" />
          <input type="hidden" name="action" value="delete" />
          <input type="hidden" name="id" />
          <div class="modal-actions">
            <button class="btn btn-outline-secondary" type="button" data-close-modal>Cancel</button>
            <button class="btn btn-danger" type="submit">Delete</button>
          </div>
        </form>
      </div>
    </div>

    <script>
      const modals = document.querySelectorAll(".admin-modal");

      const openModal = (id) => {
        const modal = document.getElementById(id);
        modal.classList.add("is-open");
        modal.setAttribute("aria-hidden", "false");
      };

      const closeModals = () => {
        modals.forEach((modal) => {
          modal.classList.remove("is-open");
          modal.setAttribute("aria-hidden", "true");
        });
      };

      const setValue = (form, name, value = "") => {
        const field = form.elements[name];
        if (field) field.value = value;
      };

      const setChecked = (form, name, value) => {
        const field = form.elements[name];
        if (field) field.checked = value === "1" || value === 1 || value === true;
      };

      document.querySelectorAll("[data-close-modal]").forEach((button) => {
        button.addEventListener("click", closeModals);
      });

      modals.forEach((modal) => {
        modal.addEventListener("click", (event) => {
          if (event.target === modal) closeModals();
        });
      });

      document.addEventListener("keydown", (event) => {
        if (event.key === "Escape") closeModals();
      });

      document.querySelectorAll("[data-open-event-modal]").forEach((button) => {
        button.addEventListener("click", () => {
          const form = document.querySelector("#event-modal form");
          form.reset();
          setValue(form, "id", button.dataset.id || "");
          setValue(form, "title", button.dataset.title || "");
          setValue(form, "event_date", button.dataset.eventDate || "");
          setValue(form, "status", button.dataset.status || "Upcoming");
          setValue(form, "description", button.dataset.description || "");
          setValue(form, "location", button.dataset.location || "");
          setValue(form, "location_icon", button.dataset.locationIcon || "location_on");
          setValue(form, "sort_order", button.dataset.sortOrder || "0");
          setChecked(form, "is_visible", button.dataset.mode === "add" ? true : button.dataset.isVisible);
          document.getElementById("event-modal-title").textContent = button.dataset.mode === "add" ? "Add Event" : "Edit Event";
          openModal("event-modal");
        });
      });

      document.querySelectorAll("[data-open-project-modal]").forEach((button) => {
        button.addEventListener("click", () => {
          const form = document.querySelector("#project-modal form");
          form.reset();
          setValue(form, "id", button.dataset.id || "");
          setValue(form, "title", button.dataset.title || "");
          setValue(form, "description", button.dataset.description || "");
          setValue(form, "tags", button.dataset.tags || "");
          setValue(form, "sort_order", button.dataset.sortOrder || "0");
          setChecked(form, "is_visible", button.dataset.mode === "add" ? true : button.dataset.isVisible);
          document.getElementById("project-modal-title").textContent = button.dataset.mode === "add" ? "Add Project" : "Edit Project";
          openModal("project-modal");
        });
      });

      document.querySelectorAll("[data-open-team-modal]").forEach((button) => {
        button.addEventListener("click", () => {
          const form = document.querySelector("#team-modal form");
          const imageWrap = document.getElementById("team-current-image-wrap");
          const image = document.getElementById("team-current-image");
          form.reset();
          setValue(form, "id", button.dataset.id || "");
          setValue(form, "name", button.dataset.name || "");
          setValue(form, "role", button.dataset.role || "");
          setValue(form, "photo_path", button.dataset.photoPath || "");
          setValue(form, "bio", button.dataset.bio || "");
          setValue(form, "sort_order", button.dataset.sortOrder || "0");
          setChecked(form, "is_visible", button.dataset.mode === "add" ? true : button.dataset.isVisible);
          document.getElementById("team-modal-title").textContent = button.dataset.mode === "add" ? "Add Team Member" : "Edit Team Member";
          image.src = button.dataset.photoPath || "";
          imageWrap.hidden = !button.dataset.photoPath;
          openModal("team-modal");
        });
      });

      const teamImageInput = document.querySelector("#team-modal input[name='team_image']");
      teamImageInput.addEventListener("change", () => {
        const file = teamImageInput.files[0];
        if (!file) return;
        const imageWrap = document.getElementById("team-current-image-wrap");
        const image = document.getElementById("team-current-image");
        image.src = URL.createObjectURL(file);
        imageWrap.hidden = false;
      });

      document.querySelectorAll("[data-open-preview]").forEach((button) => {
        button.addEventListener("click", () => {
          document.getElementById("preview-kicker").textContent = button.dataset.kicker || "";
          document.getElementById("preview-heading").textContent = button.dataset.title || "";
          document.getElementById("preview-description").textContent = button.dataset.description || "";
          document.getElementById("preview-meta").textContent = button.dataset.meta || "";
          const previewImage = document.getElementById("preview-image");
          previewImage.src = button.dataset.image || "";
          previewImage.alt = button.dataset.title || "";
          previewImage.hidden = !button.dataset.image;
          openModal("preview-modal");
        });
      });

      document.querySelectorAll("[data-open-delete]").forEach((button) => {
        button.addEventListener("click", () => {
          const form = document.querySelector("#delete-modal form");
          setValue(form, "type", button.dataset.type);
          setValue(form, "id", button.dataset.id);
          document.getElementById("delete-copy").textContent = `Delete "${button.dataset.title}"? This cannot be undone.`;
          openModal("delete-modal");
        });
      });

      const tabTargets = document.querySelectorAll("[data-admin-tab-target]");
      const tabPanels = document.querySelectorAll("[data-admin-panel]");
      const validTabs = ["members", "events", "projects", "teams"];

      const showTab = (target, updateHash = true) => {
        const nextTarget = validTabs.includes(target) ? target : "members";

        tabPanels.forEach((panel) => {
          panel.classList.toggle("is-active", panel.dataset.adminPanel === nextTarget);
        });

        document.querySelectorAll(".tab-btn[data-admin-tab-target]").forEach((tab) => {
          tab.classList.toggle("active", tab.dataset.adminTabTarget === nextTarget);
        });

        if (updateHash) {
          history.replaceState(null, "", `#${nextTarget}`);
        }
      };

      tabTargets.forEach((tab) => {
        tab.addEventListener("click", (event) => {
          event.preventDefault();
          showTab(tab.dataset.adminTabTarget);
        });
      });

      showTab(window.location.hash.replace("#", ""), false);
    </script>
  </body>
</html>
