<?php
require_once '../../includes/config.php';
requireLogin();
requireRole(['admin']);
$page_title = 'User Management';

$success = '';
$error = '';

// Handle actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'add') {
        $username = sanitize($_POST['username']);
        $full_name = sanitize($_POST['full_name']);
        $role = sanitize($_POST['role']);
        $email = sanitize($_POST['email']);
        $phone = sanitize($_POST['phone']);
        $password = $_POST['password'];

        if (!$username || !$full_name || !$role || !$password) {
            $error = 'Please fill in all required fields.';
        } else {
            try {
                $hash = password_hash($password, PASSWORD_DEFAULT);
                $pdo->prepare("INSERT INTO users (username, password, full_name, role, email, phone) VALUES (?,?,?,?,?,?)")
                    ->execute([$username, $hash, $full_name, $role, $email, $phone]);
                $success = "User <strong>$username</strong> created successfully.";
            } catch (Exception $e) {
                $error = 'Username already exists.';
            }
        }
    } elseif ($action === 'toggle') {
        $uid = (int)$_POST['user_id'];
        $pdo->prepare("UPDATE users SET is_active = NOT is_active WHERE id=?")->execute([$uid]);
        header('Location: index.php'); exit;
    } elseif ($action === 'reset_password') {
        $uid = (int)$_POST['user_id'];
        $newpw = password_hash('password', PASSWORD_DEFAULT);
        $pdo->prepare("UPDATE users SET password=? WHERE id=?")->execute([$newpw, $uid]);
        $success = 'Password reset to <strong>password</strong>.';
    }
}

$users = $pdo->query("SELECT * FROM users ORDER BY role, full_name")->fetchAll();

include '../../includes/header.php';
?>

<div class="page-header flex-between">
    <div>
        <h1><i class="fas fa-user-shield"></i> User Management</h1>
        <p>Manage system users and access control.</p>
    </div>
    <button onclick="openModal('addUserModal')" class="btn btn-primary"><i class="fas fa-user-plus"></i> Add User</button>
</div>

<?php if ($error): ?><div class="alert alert-danger"><i class="fas fa-exclamation-circle"></i> <?= $error ?></div><?php endif; ?>
<?php if ($success): ?><div class="alert alert-success"><i class="fas fa-check-circle"></i> <?= $success ?></div><?php endif; ?>

<!-- Role Summary -->
<div class="stats-grid" style="grid-template-columns:repeat(5,1fr);margin-bottom:24px;">
<?php
$roles = ['admin','captain','secretary','treasurer','resident'];
$roleColors = ['admin'=>['#dc3545','#fff5f5'],'captain'=>['#d4a520','#fffdf0'],'secretary'=>['#446CAC','#e8eef7'],'treasurer'=>['#28a745','#f0fff4'],'resident'=>['#6c757d','#f0f0f0']];
foreach ($roles as $r):
    $cnt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE role=?");
    $cnt->execute([$r]);
?>
<div class="stat-card" style="--card-color:<?= $roleColors[$r][0] ?>;--card-bg:<?= $roleColors[$r][1] ?>;padding:14px;">
    <div class="stat-info">
        <div class="stat-value" style="font-size:22px;color:<?= $roleColors[$r][0] ?>;"><?= $cnt->fetchColumn() ?></div>
        <div class="stat-label"><?= ucfirst($r) ?><?= $r==='admin'?'s':'s' ?></div>
    </div>
</div>
<?php endforeach; ?>
</div>

<div class="card">
    <div class="card-header">
        <h3><i class="fas fa-users"></i> System Users (<?= count($users) ?>)</h3>
    </div>
    <div class="card-body" style="padding:0;">
        <div class="table-wrap">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Username</th>
                        <th>Role</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Last Login</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $u): ?>
                    <tr>
                        <td>
                            <div style="display:flex;align-items:center;gap:10px;">
                                <div style="width:34px;height:34px;background:var(--blue);border-radius:50%;display:flex;align-items:center;justify-content:center;color:white;font-weight:700;font-size:13px;flex-shrink:0;">
                                    <?= strtoupper(substr($u['full_name'],0,1)) ?>
                                </div>
                                <strong><?= htmlspecialchars($u['full_name']) ?></strong>
                            </div>
                        </td>
                        <td style="font-family:monospace;"><?= htmlspecialchars($u['username']) ?></td>
                        <td><span class="badge badge-<?= $u['role'] ?>"><?= ucfirst($u['role']) ?></span></td>
                        <td style="font-size:12.5px;"><?= htmlspecialchars($u['email'] ?: '—') ?></td>
                        <td style="font-size:12.5px;"><?= htmlspecialchars($u['phone'] ?: '—') ?></td>
                        <td style="font-size:12px;"><?= $u['last_login'] ? date('M d, Y H:i', strtotime($u['last_login'])) : 'Never' ?></td>
                        <td>
                            <span class="badge <?= $u['is_active'] ? 'badge-approved' : 'badge-rejected' ?>">
                                <?= $u['is_active'] ? 'Active' : 'Inactive' ?>
                            </span>
                        </td>
                        <td>
                            <?php if ($u['id'] !== $_SESSION['user_id']): ?>
                            <div class="btn-group">
                                <form method="POST" style="display:inline;">
                                    <input type="hidden" name="action" value="toggle">
                                    <input type="hidden" name="user_id" value="<?= $u['id'] ?>">
                                    <button type="submit" class="btn btn-sm <?= $u['is_active'] ? 'btn-danger' : 'btn-success' ?>">
                                        <i class="fas fa-<?= $u['is_active'] ? 'ban' : 'check' ?>"></i>
                                    </button>
                                </form>
                                <form method="POST" style="display:inline;">
                                    <input type="hidden" name="action" value="reset_password">
                                    <input type="hidden" name="user_id" value="<?= $u['id'] ?>">
                                    <button type="submit" class="btn btn-sm btn-outline" onclick="return confirm('Reset password to default?')">
                                        <i class="fas fa-key"></i>
                                    </button>
                                </form>
                            </div>
                            <?php else: ?>
                            <span style="font-size:12px;color:var(--gray-500);">Current User</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Add User Modal -->
<div class="modal-backdrop" id="addUserModal">
    <div class="modal" style="max-width:500px;">
        <div class="modal-header">
            <h3><i class="fas fa-user-plus"></i> Add New User</h3>
            <button class="modal-close" onclick="closeModal('addUserModal')"><i class="fas fa-times"></i></button>
        </div>
        <form method="POST">
            <input type="hidden" name="action" value="add">
            <div class="modal-body">
                <div class="form-grid">
                    <div class="form-group full-width">
                        <label>Full Name <span style="color:red;">*</span></label>
                        <input type="text" name="full_name" required>
                    </div>
                    <div class="form-group">
                        <label>Username <span style="color:red;">*</span></label>
                        <input type="text" name="username" required>
                    </div>
                    <div class="form-group">
                        <label>Password <span style="color:red;">*</span></label>
                        <input type="password" name="password" required>
                    </div>
                    <div class="form-group">
                        <label>Role <span style="color:red;">*</span></label>
                        <select name="role" required>
                            <option value="">Select role</option>
                            <option value="admin">Admin</option>
                            <option value="captain">Captain</option>
                            <option value="secretary">Secretary</option>
                            <option value="treasurer">Treasurer</option>
                            <option value="resident">Resident</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Email</label>
                        <input type="email" name="email">
                    </div>
                    <div class="form-group">
                        <label>Phone</label>
                        <input type="tel" name="phone">
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline" onclick="closeModal('addUserModal')">Cancel</button>
                <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Create User</button>
            </div>
        </form>
    </div>
</div>

<?php include '../../includes/footer.php'; ?>
