<?php
session_start();
include '../config.php';
include '../includes/header.php';

$selectedCourse = $_GET['course_id'] ?? null;
$courses = $conn->query("SELECT id, course_name, course_code FROM courses ORDER BY course_name");

$modules = null;
if ($selectedCourse) {
    $stmt = $conn->prepare("SELECT * FROM modules WHERE course_id = ? ORDER BY order_num, id");
    $stmt->bind_param("i", $selectedCourse);
    $stmt->execute();
    $modules = $stmt->get_result();
}
?>

<div class="card">
    <div class="card-header">
        <h3>Modules</h3>
        <form method="get" class="filter-form">
            <select name="course_id" onchange="this.form.submit()">
                <option value="">--Select Course--</option>
                <?php while ($c = $courses->fetch_assoc()): ?>
                    <option value="<?= $c['id'] ?>" <?= ($selectedCourse == $c['id']) ? 'selected' : '' ?>><?= htmlspecialchars($c['course_code'] . ' - ' . $c['course_name']) ?></option>
                <?php endwhile; ?>
            </select>
        </form>
    </div>

    <?php if ($selectedCourse): ?>
        <button class="btn btn-primary" style="margin:0 20px 20px" onclick="document.getElementById('addModuleModal').style.display='flex'"><i class="fas fa-plus"></i> Add Module</button>
        <table class="data-table">
            <thead>
                <tr><th>#</th><th>Title</th><th>Description</th><th>Actions</th></tr>
            </thead>
            <tbody>
                <?php if ($modules): $order = 1; while ($m = $modules->fetch_assoc()): ?>
                    <tr>
                        <td><?= $order++ ?></td>
                        <td><?= htmlspecialchars($m['title']) ?></td>
                        <td><?= htmlspecialchars($m['description'] ?: '-') ?></td>
                        <td class="actions-cell">
                            <button class="btn btn-sm btn-primary" onclick='editModule(<?= json_encode($m) ?>)'><i class="fas fa-edit"></i></button>
                            <form action="../api/actions.php" method="post" style="display:inline;" onsubmit="return confirm('Delete this module?')">
                                <input type="hidden" name="action" value="delete_module">
                                <input type="hidden" name="id" value="<?= $m['id'] ?>">
                                <input type="hidden" name="course_id" value="<?= $selectedCourse ?>">
                                <button type="submit" class="btn btn-danger btn-sm"><i class="fas fa-trash"></i></button>
                            </form>
                        </td>
                    </tr>
                <?php endwhile; endif; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p class="text-center" style="padding:40px;">Select a course to manage its modules</p>
    <?php endif; ?>
</div>

<div class="modal" id="addModuleModal">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Add Module</h3>
            <button class="modal-close" onclick="document.getElementById('addModuleModal').style.display='none'">&times;</button>
        </div>
        <form action="../api/actions.php" method="post">
            <input type="hidden" name="action" value="add_module">
            <input type="hidden" name="course_id" value="<?= $selectedCourse ?>">
            <input type="text" name="title" placeholder="Module Title (e.g. Week 1 - Introduction)" required>
            <textarea name="description" placeholder="Description" rows="3"></textarea>
            <input type="number" name="order_num" placeholder="Order number" value="0" min="0">
            <button type="submit" class="btn btn-primary">Add Module</button>
        </form>
    </div>
</div>

<div class="modal" id="editModuleModal">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Edit Module</h3>
            <button class="modal-close" onclick="document.getElementById('editModuleModal').style.display='none'">&times;</button>
        </div>
        <form action="../api/actions.php" method="post">
            <input type="hidden" name="action" value="edit_module">
            <input type="hidden" name="id" id="edit_module_id">
            <input type="hidden" name="course_id" value="<?= $selectedCourse ?>">
            <input type="text" name="title" id="edit_module_title" required>
            <textarea name="description" id="edit_module_desc" rows="3"></textarea>
            <input type="number" name="order_num" id="edit_module_order" min="0">
            <button type="submit" class="btn btn-primary">Save Changes</button>
        </form>
    </div>
</div>

<script>
function editModule(m) {
    document.getElementById('edit_module_id').value = m.id;
    document.getElementById('edit_module_title').value = m.title;
    document.getElementById('edit_module_desc').value = m.description || '';
    document.getElementById('edit_module_order').value = m.order_num;
    document.getElementById('editModuleModal').style.display = 'flex';
}
</script>

<?php include '../includes/footer.php'; ?>
