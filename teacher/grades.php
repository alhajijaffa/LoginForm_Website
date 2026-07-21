<?php
session_start();
include '../config.php';
include '../includes/header.php';

$selectedCourse = $_GET['course_id'] ?? null;
$selectedModule = $_GET['module_id'] ?? null;
$courses = $conn->query("SELECT id, course_name, course_code FROM courses ORDER BY course_name");

$modules = null;
if ($selectedCourse) {
    $stmt = $conn->prepare("SELECT * FROM modules WHERE course_id = ? ORDER BY order_num, id");
    $stmt->bind_param("i", $selectedCourse);
    $stmt->execute();
    $modules = $stmt->get_result();
}

$enrolledStudents = null;
if ($selectedModule) {
    $stmt = $conn->prepare("
        SELECT s.id as student_id, s.first_name, s.last_name,
               g.id as grade_id, g.marks, g.grade, g.comments
        FROM students s
        JOIN enrollments e ON s.id = e.student_id
        LEFT JOIN grades g ON g.enrollment_id = e.id AND g.module_id = ?
        WHERE e.course_id = ? AND s.status = 'active'
        ORDER BY s.first_name
    ");
    $stmt->bind_param("ii", $selectedModule, $selectedCourse);
    $stmt->execute();
    $enrolledStudents = $stmt->get_result();
}
?>

<div class="card">
    <div class="card-header">
        <h3>Enter Grades</h3>
    </div>
    <div class="filter-row" style="padding: 0 20px 20px;">
        <form method="get" class="filter-form-inline">
            <select name="course_id" onchange="document.getElementById('module_select').value=''; this.form.submit()">
                <option value="">--Select Course--</option>
                <?php while ($c = $courses->fetch_assoc()): ?>
                    <option value="<?= $c['id'] ?>" <?= ($selectedCourse == $c['id']) ? 'selected' : '' ?>><?= htmlspecialchars($c['course_code'] . ' - ' . $c['course_name']) ?></option>
                <?php endwhile; ?>
            </select>
            <?php if ($selectedCourse): ?>
                <select name="module_id" id="module_select" onchange="this.form.submit()">
                    <option value="">--Select Module--</option>
                    <?php if ($modules): while ($m = $modules->fetch_assoc()): ?>
                        <option value="<?= $m['id'] ?>" <?= ($selectedModule == $m['id']) ? 'selected' : '' ?>><?= htmlspecialchars($m['title']) ?></option>
                    <?php endwhile; endif; ?>
                </select>
            <?php endif; ?>
        </form>
    </div>

    <?php if ($selectedModule && $enrolledStudents): ?>
        <form action="../api/actions.php" method="post">
            <input type="hidden" name="action" value="save_grades">
            <input type="hidden" name="module_id" value="<?= $selectedModule ?>">
            <input type="hidden" name="course_id" value="<?= $selectedCourse ?>">
            <table class="data-table">
                <thead>
                    <tr><th>Student</th><th>Marks</th><th>Grade</th><th>Comments</th></tr>
                </thead>
                <tbody>
                    <?php while ($s = $enrolledStudents->fetch_assoc()):
                        $enrollment = $conn->query("SELECT id FROM enrollments WHERE student_id = {$s['student_id']} AND course_id = $selectedCourse")->fetch_assoc();
                    ?>
                        <tr>
                            <td><?= htmlspecialchars($s['first_name'] . ' ' . $s['last_name']) ?></td>
                            <input type="hidden" name="enrollment_ids[]" value="<?= $enrollment['id'] ?>">
                            <td><input type="number" name="marks[]" class="table-input" value="<?= $s['marks'] ?? '' ?>" min="0" max="100" step="0.01" placeholder="0-100"></td>
                            <td>
                                <select name="grades[]" class="table-select">
                                    <option value="">-</option>
                                    <option value="A" <?= ($s['grade'] ?? '') === 'A' ? 'selected' : '' ?>>A</option>
                                    <option value="B" <?= ($s['grade'] ?? '') === 'B' ? 'selected' : '' ?>>B</option>
                                    <option value="C" <?= ($s['grade'] ?? '') === 'C' ? 'selected' : '' ?>>C</option>
                                    <option value="D" <?= ($s['grade'] ?? '') === 'D' ? 'selected' : '' ?>>D</option>
                                    <option value="F" <?= ($s['grade'] ?? '') === 'F' ? 'selected' : '' ?>>F</option>
                                </select>
                            </td>
                            <td><input type="text" name="comments[]" class="table-input" value="<?= htmlspecialchars($s['comments'] ?? '') ?>" placeholder="Optional"></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
            <div style="padding: 20px;">
                <button type="submit" class="btn btn-primary">Save Grades</button>
            </div>
        </form>
    <?php elseif ($selectedCourse): ?>
        <p class="text-center" style="padding:40px;">Select a module to enter grades</p>
    <?php else: ?>
        <p class="text-center" style="padding:40px;">Select a course to begin grading</p>
    <?php endif; ?>
</div>

<?php include '../includes/footer.php'; ?>
