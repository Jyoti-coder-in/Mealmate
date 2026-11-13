<?php
session_start();
if (!isset($_SESSION['admin_id'])) { header('Location: login.php'); exit(); }
require_once __DIR__ . '/../database/database.php';
$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['review_id'])) {
    $id = (int)$_POST['review_id'];
    $decision = $_POST['decision'] ?? '';
    $note = trim($_POST['note'] ?? '');
    if (in_array($decision, ['Approved','Rejected'], true)) {
        $stmt = mysqli_prepare($conn, "UPDATE users SET verification_status=?, verification_note=? WHERE id=? AND college_id_file IS NOT NULL AND college_id_file <> ''");
        mysqli_stmt_bind_param($stmt, 'ssi', $decision, $note, $id);
        $message = mysqli_stmt_execute($stmt) && mysqli_stmt_affected_rows($stmt) >= 0 ? 'Student verification updated.' : 'Could not update verification. Import the migration if needed.';
    }
}
$rows = mysqli_query($conn, "SELECT id,name,email,college,college_id_file,verification_status,verification_note FROM users WHERE college_id_file IS NOT NULL AND college_id_file <> '' ORDER BY CASE verification_status WHEN 'Pending' THEN 0 ELSE 1 END, id DESC");
?>
<!doctype html><html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Student ID Reviews - MealMate</title><link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"><style>body{background:#fff0f6;color:#4a1834}.heading{color:#d63384}.card{border:0;border-radius:16px;box-shadow:0 4px 16px #0001}</style></head><body><div class="container py-4"><a href="dashboard.php">← Admin Dashboard</a><h2 class="heading mt-3">College ID Verification</h2><p>Review student proof and approve or reject each request.</p><?php if($message): ?><div class="alert alert-info"><?php echo htmlspecialchars($message); ?></div><?php endif; ?><div class="card p-3"><div class="table-responsive"><table class="table align-middle"><thead><tr><th>Student</th><th>College</th><th>ID Proof</th><th>Status</th><th>Review</th></tr></thead><tbody><?php if($rows && mysqli_num_rows($rows)): while($u=mysqli_fetch_assoc($rows)): ?><tr><td><?php echo htmlspecialchars($u['name'].' · '.$u['email']); ?></td><td><?php echo htmlspecialchars($u['college'] ?? ''); ?></td><td><a target="_blank" rel="noopener" href="view_student_id.php?id=<?php echo (int)$u['id']; ?>">View uploaded ID</a></td><td><strong><?php echo htmlspecialchars($u['verification_status'] ?? 'Pending'); ?></strong><?php if(!empty($u['verification_note'])): ?><div class="small"><?php echo htmlspecialchars($u['verification_note']); ?></div><?php endif; ?></td><td><form method="post" class="d-grid gap-2"><input type="hidden" name="review_id" value="<?php echo (int)$u['id']; ?>"><input class="form-control form-control-sm" name="note" maxlength="500" placeholder="Optional note" value="<?php echo htmlspecialchars($u['verification_note'] ?? ''); ?>"><button class="btn btn-success btn-sm" name="decision" value="Approved">Approve</button><button class="btn btn-danger btn-sm" name="decision" value="Rejected">Reject</button></form></td></tr><?php endwhile; else: ?><tr><td colspan="5">No student IDs uploaded yet.</td></tr><?php endif; ?></tbody></table></div></div></div></body></html>
