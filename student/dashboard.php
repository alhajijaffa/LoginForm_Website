<?php
session_start();
include '../config.php';
include '../includes/header.php';

$student = $conn->query("SELECT * FROM students WHERE user_id = " . $_SESSION['user_id'])->fetch_assoc();

if (!$student) {
    echo '<div class="card"><p class="text-center" style="padding:40px;">No student profile linked to your account. Contact admin.</p></div>';
    include '../includes/footer.php';
    exit();
}

$totalCourses = $conn->query("SELECT COUNT(*) as c FROM enrollments WHERE student_id = {$student['id']}")->fetch_assoc()['c'];

$totalModules = 0;
$completedModules = 0;
if ($totalCourses > 0) {
    $totalModules = $conn->query("
        SELECT COUNT(DISTINCT m.id) as c
        FROM modules m
        JOIN courses c ON m.course_id = c.id
        JOIN enrollments e ON e.course_id = c.id
        WHERE e.student_id = {$student['id']}
    ")->fetch_assoc()['c'];

    $completedModules = $conn->query("SELECT COUNT(*) as c FROM progress WHERE student_id = {$student['id']} AND completed = 1")->fetch_assoc()['c'];
}

$progressPercent = $totalModules > 0 ? round(($completedModules / $totalModules) * 100) : 0;

$avgGrade = $conn->query("
    SELECT AVG(g.marks) as avg_marks
    FROM grades g
    JOIN enrollments e ON g.enrollment_id = e.id
    WHERE e.student_id = {$student['id']} AND g.marks IS NOT NULL
")->fetch_assoc()['avg_marks'];

$recentGrades = $conn->query("
    SELECT g.marks, g.grade, g.comments, m.title as module_title, c.course_name, c.course_code
    FROM grades g
    JOIN enrollments e ON g.enrollment_id = e.id
    JOIN courses c ON e.course_id = c.id
    LEFT JOIN modules m ON g.module_id = m.id
    WHERE e.student_id = {$student['id']}
    ORDER BY g.graded_at DESC
    LIMIT 5
");
?>

<div class="stats-grid">
    <div class="stat-card blue">
        <div class="stat-icon"><i class="fas fa-book"></i></div>
        <div class="stat-info">
            <h3><?= $totalCourses ?></h3>
            <p>Enrolled Courses</p>
        </div>
    </div>
    <div class="stat-card green">
        <div class="stat-icon"><i class="fas fa-check-circle"></i></div>
        <div class="stat-info">
            <h3><?= $completedModules ?>/<?= $totalModules ?></h3>
            <p>Modules Completed</p>
        </div>
    </div>
    <div class="stat-card orange">
        <div class="stat-icon"><i class="fas fa-chart-bar"></i></div>
        <div class="stat-info">
            <h3><?= $progressPercent ?>%</h3>
            <p>Overall Progress</p>
        </div>
    </div>
    <div class="stat-card purple">
        <div class="stat-icon"><i class="fas fa-star"></i></div>
        <div class="stat-info">
            <h3><?= $avgGrade ? round($avgGrade, 1) . '%' : 'N/A' ?></h3>
            <p>Average Marks</p>
        </div>
    </div>
</div>

<div class="progress-bar-container">
    <h3>Overall Progress</h3>
    <div class="progress-bar">
        <div class="progress-fill" style="width: <?= $progressPercent ?>%"><?= $progressPercent ?>%</div>
    </div>
</div>

<div class="card">
    <h3>Recent Grades</h3>
    <table class="data-table">
        <thead>
            <tr><th>Course</th><th>Module</th><th>Marks</th><th>Grade</th><th>Comments</th></tr>
        </thead>
        <tbody>
            <?php while ($g = $recentGrades->fetch_assoc()): ?>
                <tr>
                    <td><?= htmlspecialchars($g['course_code'] . ' - ' . $g['course_name']) ?></td>
                    <td><?= htmlspecialchars($g['module_title'] ?: '-') ?></td>
                    <td><?= $g['marks'] !== null ? $g['marks'] . '%' : '-' ?></td>
                    <td><span class="badge badge-grade"><?= $g['grade'] ?: '-' ?></span></td>
                    <td><?= htmlspecialchars($g['comments'] ?: '-') ?></td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

<?php include '../includes/footer.php'; ?>
