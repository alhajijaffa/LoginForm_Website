<?php
session_start();
include '../config.php';
include '../includes/header.php';

$enrollments = $conn->query("SELECT e.*, s.first_name, s.last_name, c.course_name, c.course_code FROM enrollments e JOIN students s ON e.student_id = s.id JOIN courses c ON e.course_id = c.id ORDER BY e.enrolled_at DESC");
$students = $conn->query("SELECT id, first_name, last_name FROM students WHERE status = 'active' ORDER BY first_name");
$courses = $conn->query("SELECT id, course_name, course_code FROM courses ORDER BY course_name");
?>

<div class="card">
    <div class="card-header">
        <h3>Enrollments</h3>
        <button class="btn btn-primary" onclick="document.getElementById('addEnrollmentModal').style.display='flex'"><i class="fas fa-plus"></i> Enroll Student</button>
    </div>
    <table class="data-table">
        <thead>
            <tr><th>ID</th><th>Student</th><th>Course</th><th>Enrolled On</th><th>Actions</th></tr>
        </thead>
        <tbody>
            <?php while ($e = $enrollments->fetch_assoc()): ?>
                <tr>
                    <td><?= $e['id'] ?></td>
                    <td><?= htmlspecialchars($e['first_name'] . ' ' . $e['last_name']) ?></td>
                    <td><?= htmlspecialchars($e['course_code'] . ' - ' . $e['course_name']) ?></td>
                    <td><?= date('M d, Y', strtotime($e['enrolled_at'])) ?></td>
                    <td>
                        <form action="../api/actions.php" method="post" style="display:inline;" onsubmit="return confirm('Remove this enrollment?')">
                            <input type="hidden" name="action" value="remove_enrollment">
                            <input type="hidden" name="id" value="<?= $e['id'] ?>">
                            <button type="submit" class="btn btn-danger btn-sm"><i class="fas fa-times"></i> Unenroll</button>
                        </form>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

<div class="modal" id="addEnrollmentModal">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Enroll Student</h3>
            <button class="modal-close" onclick="document.getElementById('addEnrollmentModal').style.display='none'">&times;</button>
        </div>
        <form action="../api/actions.php" method="post">
            <input type="hidden" name="action" value="add_enrollment">
            <select name="student_id" required>
                <option value="">--Select Student--</option>
                <?php while ($s = $students->fetch_assoc()): ?>
                    <option value="<?= $s['id'] ?>"><?= htmlspecialchars($s['first_name'] . ' ' . $s['last_name']) ?></option>
                <?php endwhile; ?>
            </select>
            <select name="course_id" required>
                <option value="">--Select Course--</option>
                <?php while ($c = $courses->fetch_assoc()): ?>
                    <option value="<?= $c['id'] ?>"><?= htmlspecialchars($c['course_code'] . ' - ' . $c['course_name']) ?></option>
                <?php endwhile; ?>
            </select>
            <button type="submit" class="btn btn-primary">Enroll</button>
        </form>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
