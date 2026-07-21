<?php
session_start();
include '../config.php';
include '../includes/header.php';

$student = $conn->query("SELECT * FROM students WHERE user_id = " . $_SESSION['user_id'])->fetch_assoc();
$courseId = $_GET['course_id'] ?? null;

if (!$student || !$courseId) {
    echo '<div class="card"><p class="text-center" style="padding:40px;">Invalid course.</p></div>';
    include '../includes/footer.php';
    exit();
}

$course = $conn->query("SELECT * FROM courses WHERE id = $courseId")->fetch_assoc();
$enrollment = $conn->query("SELECT * FROM enrollments WHERE student_id = {$student['id']} AND course_id = $courseId")->fetch_assoc();

if (!$course || !$enrollment) {
    echo '<div class="card"><p class="text-center" style="padding:40px;">You are not enrolled in this course.</p></div>';
    include '../includes/footer.php';
    exit();
}

$modules = $conn->query("SELECT * FROM modules WHERE course_id = $courseId ORDER BY order_num, id");
$completedCount = 0;
$totalModules = $modules->num_rows;

$progressData = $conn->query("SELECT module_id FROM progress WHERE student_id = {$student['id']} AND completed = 1");
$completedIds = [];
while ($p = $progressData->fetch_assoc()) {
    $completedIds[] = $p['module_id'];
    $completedCount++;
}

$grades = $conn->query("
    SELECT g.*, m.title as module_title
    FROM grades g
    LEFT JOIN modules m ON g.module_id = m.id
    WHERE g.enrollment_id = {$enrollment['id']}
    ORDER BY g.graded_at DESC
");

$progressPercent = $totalModules > 0 ? round(($completedCount / $totalModules) * 100) : 0;
?>

<div class="card">
    <div class="course-detail-header">
        <div>
            <h2><?= htmlspecialchars($course['course_code'] . ' - ' . $course['course_name']) ?></h2>
            <p><?= htmlspecialchars($course['description'] ?: 'No description available') ?></p>
            <span class="badge badge-blue"><?= $course['credits'] ?> Credits</span>
        </div>
        <div class="course-stats-mini">
            <div class="stat-mini"><strong><?= $completedCount ?>/<?= $totalModules ?></strong><span>Modules</span></div>
            <div class="stat-mini"><strong><?= $progressPercent ?>%</strong><span>Progress</span></div>
        </div>
    </div>
    <div class="progress-bar-container" style="padding: 0 20px 20px;">
        <div class="progress-bar">
            <div class="progress-fill" style="width: <?= $progressPercent ?>%"><?= $progressPercent ?>%</div>
        </div>
    </div>
</div>

<div class="grid-2">
    <div class="card">
        <h3>Modules</h3>
        <div class="module-list">
            <?php if ($totalModules > 0): $num = 1;
                $modules->data_seek(0);
                while ($m = $modules->fetch_assoc()):
                    $isComplete = in_array($m['id'], $completedIds);
            ?>
                <div class="module-item <?= $isComplete ? 'completed' : '' ?>">
                    <div class="module-icon">
                        <?php if ($isComplete): ?>
                            <i class="fas fa-check-circle"></i>
                        <?php else: ?>
                            <i class="fas fa-circle"></i>
                        <?php endif; ?>
                    </div>
                    <div class="module-info">
                        <strong><?= $num++ ?>. <?= htmlspecialchars($m['title']) ?></strong>
                        <p><?= htmlspecialchars($m['description'] ?: '') ?></p>
                    </div>
                </div>
            <?php endwhile; else: ?>
                <p class="text-center" style="padding:20px;">No modules yet</p>
            <?php endif; ?>
        </div>
    </div>

    <div class="card">
        <h3>Grades</h3>
        <table class="data-table">
            <thead>
                <tr><th>Module</th><th>Marks</th><th>Grade</th><th>Comments</th></tr>
            </thead>
            <tbody>
                <?php while ($g = $grades->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($g['module_title'] ?: 'Overall') ?></td>
                        <td><?= $g['marks'] !== null ? $g['marks'] . '%' : '-' ?></td>
                        <td><span class="badge badge-grade"><?= $g['grade'] ?: '-' ?></span></td>
                        <td><?= htmlspecialchars($g['comments'] ?: '-') ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<a href="courses.php" class="btn btn-primary" style="margin-top:20px;display:inline-block;text-decoration:none;">Back to My Courses</a>

<?php include '../includes/footer.php'; ?>
