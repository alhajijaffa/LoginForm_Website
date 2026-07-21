<?php
session_start();
include '../config.php';
include '../includes/header.php';

$selectedCourse = $_GET['course_id'] ?? null;
$courses = $conn->query("SELECT id, course_name, course_code FROM courses ORDER BY course_name");

$students = null;
if ($selectedCourse) {
    $stmt = $conn->prepare("SELECT s.id, s.first_name, s.last_name FROM students s JOIN enrollments e ON s.id = e.student_id WHERE e.course_id = ? AND s.status = 'active' ORDER BY s.first_name");
    $stmt->bind_param("i", $selectedCourse);
    $stmt->execute();
    $students = $stmt->get_result();

    $stmt2 = $conn->prepare("SELECT * FROM modules WHERE course_id = ? ORDER BY order_num, id");
    $stmt2->bind_param("i", $selectedCourse);
    $stmt2->execute();
    $modulesList = $stmt2->get_result();
}
?>

<div class="card">
    <div class="card-header">
        <h3>Mark Module Completion</h3>
    </div>
    <div class="filter-row" style="padding: 0 20px 20px;">
        <form method="get" class="filter-form-inline">
            <select name="course_id" onchange="this.form.submit()">
                <option value="">--Select Course--</option>
                <?php while ($c = $courses->fetch_assoc()): ?>
                    <option value="<?= $c['id'] ?>" <?= ($selectedCourse == $c['id']) ? 'selected' : '' ?>><?= htmlspecialchars($c['course_code'] . ' - ' . $c['course_name']) ?></option>
                <?php endwhile; ?>
            </select>
        </form>
    </div>

    <?php if ($selectedCourse && $students && $students->num_rows > 0): ?>
        <form action="../api/actions.php" method="post">
            <input type="hidden" name="action" value="save_progress">
            <input type="hidden" name="course_id" value="<?= $selectedCourse ?>">

            <div style="overflow-x:auto; padding: 0 20px 20px;">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Student</th>
                            <?php if ($modulesList): while ($m = $modulesList->fetch_assoc()): ?>
                                <th class="text-center"><?= htmlspecialchars($m['title']) ?></th>
                            <?php endwhile; endif; ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $modulesList->data_seek(0);
                        while ($s = $students->fetch_assoc()):
                        ?>
                            <tr>
                                <td><?= htmlspecialchars($s['first_name'] . ' ' . $s['last_name']) ?></td>
                                <?php $modulesList->data_seek(0); while ($m = $modulesList->fetch_assoc()):
                                    $prog = $conn->query("SELECT completed FROM progress WHERE student_id = {$s['id']} AND module_id = {$m['id']}")->fetch_assoc();
                                    $checked = ($prog && $prog['completed']) ? 'checked' : '';
                                ?>
                                    <td class="text-center">
                                        <input type="checkbox" name="completed[<?= $s['id'] ?>][<?= $m['id'] ?>]" value="1" <?= $checked ?>>
                                    </td>
                                <?php endwhile; ?>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
            <div style="padding: 0 20px 20px;">
                <button type="submit" class="btn btn-primary">Save Progress</button>
            </div>
        </form>
    <?php else: ?>
        <p class="text-center" style="padding:40px;">Select a course to mark module completion</p>
    <?php endif; ?>
</div>

<?php include '../includes/footer.php'; ?>
