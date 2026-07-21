<?php
session_start();
include '../config.php';
include '../includes/header.php';

$student = $conn->query("SELECT * FROM students WHERE user_id = " . $_SESSION['user_id'])->fetch_assoc();

if (!$student) {
    echo '<div class="card"><p class="text-center" style="padding:40px;">No student profile linked to your account.</p></div>';
    include '../includes/footer.php';
    exit();
}

$courses = $conn->query("
    SELECT c.*, e.enrolled_at,
           (SELECT COUNT(*) FROM modules WHERE course_id = c.id) as total_modules,
           (SELECT COUNT(*) FROM progress p JOIN modules m ON p.module_id = m.id WHERE m.course_id = c.id AND p.student_id = {$student['id']} AND p.completed = 1) as completed_modules
    FROM courses c
    JOIN enrollments e ON c.id = e.course_id
    WHERE e.student_id = {$student['id']}
    ORDER BY c.course_name
");
?>

<div class="course-grid">
    <?php while ($c = $courses->fetch_assoc()):
        $progress = $c['total_modules'] > 0 ? round(($c['completed_modules'] / $c['total_modules']) * 100) : 0;
    ?>
        <a href="course_detail.php?course_id=<?= $c['id'] ?>" class="course-card">
            <div class="course-card-header">
                <span class="course-code"><?= htmlspecialchars($c['course_code']) ?></span>
                <span class="course-credits"><?= $c['credits'] ?> Credits</span>
            </div>
            <h3><?= htmlspecialchars($c['course_name']) ?></h3>
            <p class="course-desc"><?= htmlspecialchars($c['description'] ?: 'No description') ?></p>
            <div class="course-progress">
                <div class="progress-bar small">
                    <div class="progress-fill" style="width: <?= $progress ?>%"></div>
                </div>
                <span><?= $c['completed_modules'] ?>/<?= $c['total_modules'] ?> modules (<?= $progress ?>%)</span>
            </div>
            <div class="course-meta">
                <span><i class="fas fa-calendar"></i> Enrolled <?= date('M d, Y', strtotime($c['enrolled_at'])) ?></span>
            </div>
        </a>
    <?php endwhile; ?>
    <?php if ($courses->num_rows == 0): ?>
        <div class="card"><p class="text-center" style="padding:40px;">You are not enrolled in any courses yet.</p></div>
    <?php endif; ?>
</div>

<?php include '../includes/footer.php'; ?>
