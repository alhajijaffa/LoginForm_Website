<?php
session_start();
include '../config.php';
include '../includes/header.php';

$courses = $conn->query("SELECT c.*, (SELECT COUNT(*) FROM enrollments WHERE course_id = c.id) as enrollment_count FROM courses c ORDER BY c.id DESC");
?>

<div class="card">
    <div class="card-header">
        <h3>All Courses</h3>
        <button class="btn btn-primary" onclick="document.getElementById('addCourseModal').style.display='flex'"><i class="fas fa-plus"></i> Add Course</button>
    </div>
    <table class="data-table">
        <thead>
            <tr><th>ID</th><th>Code</th><th>Name</th><th>Credits</th><th>Enrolled</th><th>Actions</th></tr>
        </thead>
        <tbody>
            <?php while ($c = $courses->fetch_assoc()): ?>
                <tr>
                    <td><?= $c['id'] ?></td>
                    <td><strong><?= htmlspecialchars($c['course_code']) ?></strong></td>
                    <td><?= htmlspecialchars($c['course_name']) ?></td>
                    <td><?= $c['credits'] ?></td>
                    <td><?= $c['enrollment_count'] ?></td>
                    <td class="actions-cell">
                        <button class="btn btn-sm btn-primary" onclick='editCourse(<?= json_encode($c) ?>)'><i class="fas fa-edit"></i></button>
                        <form action="../api/actions.php" method="post" style="display:inline;" onsubmit="return confirm('Delete this course?')">
                            <input type="hidden" name="action" value="delete_course">
                            <input type="hidden" name="id" value="<?= $c['id'] ?>">
                            <button type="submit" class="btn btn-danger btn-sm"><i class="fas fa-trash"></i></button>
                        </form>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

<div class="modal" id="addCourseModal">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Add Course</h3>
            <button class="modal-close" onclick="document.getElementById('addCourseModal').style.display='none'">&times;</button>
        </div>
        <form action="../api/actions.php" method="post">
            <input type="hidden" name="action" value="add_course">
            <input type="text" name="course_code" placeholder="Course Code (e.g. CS101)" required>
            <input type="text" name="course_name" placeholder="Course Name" required>
            <textarea name="description" placeholder="Description" rows="3"></textarea>
            <input type="number" name="credits" placeholder="Credits" value="3" min="1" max="10">
            <button type="submit" class="btn btn-primary">Add Course</button>
        </form>
    </div>
</div>

<div class="modal" id="editCourseModal">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Edit Course</h3>
            <button class="modal-close" onclick="document.getElementById('editCourseModal').style.display='none'">&times;</button>
        </div>
        <form action="../api/actions.php" method="post">
            <input type="hidden" name="action" value="edit_course">
            <input type="hidden" name="id" id="edit_course_id">
            <input type="text" name="course_code" id="edit_course_code" required>
            <input type="text" name="course_name" id="edit_course_name" required>
            <textarea name="description" id="edit_course_desc" rows="3"></textarea>
            <input type="number" name="credits" id="edit_course_credits" min="1" max="10">
            <button type="submit" class="btn btn-primary">Save Changes</button>
        </form>
    </div>
</div>

<script>
function editCourse(c) {
    document.getElementById('edit_course_id').value = c.id;
    document.getElementById('edit_course_code').value = c.course_code;
    document.getElementById('edit_course_name').value = c.course_name;
    document.getElementById('edit_course_desc').value = c.description || '';
    document.getElementById('edit_course_credits').value = c.credits;
    document.getElementById('editCourseModal').style.display = 'flex';
}
</script>

<?php include '../includes/footer.php'; ?>
