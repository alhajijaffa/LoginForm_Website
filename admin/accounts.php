<?php
session_start();
include '../config.php';
include '../includes/header.php';

$users = $conn->query("SELECT * FROM users ORDER BY id DESC");
?>

<div class="card">
    <div class="card-header">
        <h3>All Accounts</h3>
        <button class="btn btn-primary" onclick="document.getElementById('addAccountModal').style.display='flex'"><i class="fas fa-plus"></i> Add Account</button>
    </div>
    <table class="data-table">
        <thead>
            <tr><th>ID</th><th>Name</th><th>Email</th><th>Role</th><th>Actions</th></tr>
        </thead>
        <tbody>
            <?php while ($u = $users->fetch_assoc()): ?>
                <tr>
                    <td><?= $u['id'] ?></td>
                    <td><?= htmlspecialchars($u['name']) ?></td>
                    <td><?= htmlspecialchars($u['email']) ?></td>
                    <td><span class="badge badge-<?= $u['role'] ?>"><?= ucfirst($u['role']) ?></span></td>
                    <td>
                        <?php if ($u['id'] != $_SESSION['user_id']): ?>
                            <form action="../api/actions.php" method="post" style="display:inline;" onsubmit="return confirm('Delete this account?')">
                                <input type="hidden" name="action" value="delete_account">
                                <input type="hidden" name="id" value="<?= $u['id'] ?>">
                                <button type="submit" class="btn btn-danger btn-sm"><i class="fas fa-trash"></i></button>
                            </form>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

<div class="modal" id="addAccountModal">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Create Account</h3>
            <button class="modal-close" onclick="document.getElementById('addAccountModal').style.display='none'">&times;</button>
        </div>
        <form action="../api/actions.php" method="post">
            <input type="hidden" name="action" value="create_account">
            <input type="text" name="name" placeholder="Full Name" required>
            <input type="email" name="email" placeholder="Email" required>
            <input type="password" name="password" placeholder="Password" required minlength="6">
            <select name="role" required>
                <option value="">--Select Role--</option>
                <option value="teacher">Teacher</option>
                <option value="student">Student</option>
            </select>
            <button type="submit" class="btn btn-primary">Create Account</button>
        </form>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
