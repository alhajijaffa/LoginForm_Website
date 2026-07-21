<?php
session_start();
include '../config.php';
include '../includes/header.php';

$search = $_GET['search'] ?? '';
if (!empty($search)) {
    $stmt = $conn->prepare("SELECT * FROM students WHERE first_name LIKE ? OR last_name LIKE ? OR email LIKE ? ORDER BY id DESC");
    $like = "%$search%";
    $stmt->bind_param("sss", $like, $like, $like);
    $stmt->execute();
    $students = $stmt->get_result();
} else {
    $students = $conn->query("SELECT * FROM students ORDER BY id DESC");
}
?>

<div class="card">
    <div class="card-header">
        <h3>All Students</h3>
        <div class="card-actions">
            <form method="get" class="search-form">
                <input type="text" name="search" placeholder="Search students..." value="<?= htmlspecialchars($search) ?>">
                <button type="submit" class="btn btn-sm btn-primary"><i class="fas fa-search"></i></button>
            </form>
            <button class="btn btn-primary" onclick="document.getElementById('addStudentModal').style.display='flex'"><i class="fas fa-plus"></i> Add Student</button>
        </div>
    </div>
    <table class="data-table">
        <thead>
            <tr><th>ID</th><th>First Name</th><th>Last Name</th><th>Email</th><th>Gender</th><th>Phone</th><th>Status</th><th>Actions</th></tr>
        </thead>
        <tbody>
            <?php while ($s = $students->fetch_assoc()): ?>
                <tr>
                    <td><?= $s['id'] ?></td>
                    <td><?= htmlspecialchars($s['first_name']) ?></td>
                    <td><?= htmlspecialchars($s['last_name']) ?></td>
                    <td><?= htmlspecialchars($s['email']) ?></td>
                    <td><?= $s['gender'] ?: '-' ?></td>
                    <td><?= $s['phone'] ?: '-' ?></td>
                    <td><span class="badge badge-<?= $s['status'] ?>"><?= ucfirst($s['status']) ?></span></td>
                    <td class="actions-cell">
                        <button class="btn btn-sm btn-primary" onclick="editStudent(<?= htmlspecialchars(json_encode($s)) ?>)"><i class="fas fa-edit"></i></button>
                        <form action="../api/actions.php" method="post" style="display:inline;" onsubmit="return confirm('Delete this student?')">
                            <input type="hidden" name="action" value="delete_student">
                            <input type="hidden" name="id" value="<?= $s['id'] ?>">
                            <button type="submit" class="btn btn-danger btn-sm"><i class="fas fa-trash"></i></button>
                        </form>
                    </td>
                </tr>
            <?php endwhile; ?>
            <?php if ($students->num_rows == 0): ?>
                <tr><td colspan="8" class="text-center">No students found</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<div class="modal" id="addStudentModal">
    <div class="modal-content modal-lg">
        <div class="modal-header">
            <h3>Add Student</h3>
            <button class="modal-close" onclick="document.getElementById('addStudentModal').style.display='none'">&times;</button>
        </div>
        <form action="../api/actions.php" method="post">
            <input type="hidden" name="action" value="add_student">
            <div class="form-grid">
                <input type="text" name="first_name" placeholder="First Name" required>
                <input type="text" name="last_name" placeholder="Last Name" required>
                <input type="email" name="email" placeholder="Email" required>
                <select name="gender"><option value="">Gender</option><option>Male</option><option>Female</option><option>Other</option></select>
                <input type="text" name="phone" placeholder="Phone">
                <input type="date" name="date_of_birth">
                <input type="text" name="address" placeholder="Address">
                <select name="user_id">
                    <option value="">Link to User Account (optional)</option>
                    <?php
                    $accounts = $conn->query("SELECT id, name, email FROM users WHERE role = 'student' AND id NOT IN (SELECT user_id FROM students WHERE user_id IS NOT NULL)");
                    while ($a = $accounts->fetch_assoc()):
                    ?>
                        <option value="<?= $a['id'] ?>"><?= htmlspecialchars($a['name'] . ' (' . $a['email'] . ')') ?></option>
                    <?php endwhile; ?>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Add Student</button>
        </form>
    </div>
</div>

<div class="modal" id="editStudentModal">
    <div class="modal-content modal-lg">
        <div class="modal-header">
            <h3>Edit Student</h3>
            <button class="modal-close" onclick="document.getElementById('editStudentModal').style.display='none'">&times;</button>
        </div>
        <form action="../api/actions.php" method="post">
            <input type="hidden" name="action" value="edit_student">
            <input type="hidden" name="id" id="edit_student_id">
            <div class="form-grid">
                <input type="text" name="first_name" id="edit_first_name" required>
                <input type="text" name="last_name" id="edit_last_name" required>
                <input type="email" name="email" id="edit_email" required>
                <select name="gender" id="edit_gender"><option value="">Gender</option><option>Male</option><option>Female</option><option>Other</option></select>
                <input type="text" name="phone" id="edit_phone">
                <input type="date" name="date_of_birth" id="edit_dob">
                <input type="text" name="address" id="edit_address">
                <select name="status" id="edit_status">
                    <option value="active">Active</option>
                    <option value="inactive">Inactive</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Save Changes</button>
        </form>
    </div>
</div>

<script>
function editStudent(s) {
    document.getElementById('edit_student_id').value = s.id;
    document.getElementById('edit_first_name').value = s.first_name;
    document.getElementById('edit_last_name').value = s.last_name;
    document.getElementById('edit_email').value = s.email;
    document.getElementById('edit_gender').value = s.gender || '';
    document.getElementById('edit_phone').value = s.phone || '';
    document.getElementById('edit_dob').value = s.date_of_birth || '';
    document.getElementById('edit_address').value = s.address || '';
    document.getElementById('edit_status').value = s.status;
    document.getElementById('editStudentModal').style.display = 'flex';
}
</script>

<?php include '../includes/footer.php'; ?>
