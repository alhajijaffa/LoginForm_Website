<?php
session_start();
include '../config.php';
include '../includes/header.php';

$totalStudents = $conn->query("SELECT COUNT(*) as c FROM students")->fetch_assoc()['c'];
$totalCourses = $conn->query("SELECT COUNT(*) as c FROM courses")->fetch_assoc()['c'];
$totalEnrollments = $conn->query("SELECT COUNT(*) as c FROM enrollments")->fetch_assoc()['c'];
$totalUsers = $conn->query("SELECT COUNT(*) as c FROM users")->fetch_assoc()['c'];

$courseStats = $conn->query("SELECT c.course_name, COUNT(e.id) as enrollment_count FROM courses c LEFT JOIN enrollments e ON c.id = e.course_id GROUP BY c.id, c.course_name ORDER BY c.course_name");
$courseLabels = [];
$courseData = [];
while ($row = $courseStats->fetch_assoc()) {
    $courseLabels[] = $row['course_name'];
    $courseData[] = $row['enrollment_count'];
}

$recentStudents = $conn->query("SELECT first_name, last_name, created_at FROM students ORDER BY created_at DESC LIMIT 5");
?>

<div class="stats-grid">
    <div class="stat-card blue">
        <div class="stat-icon"><i class="fas fa-user-graduate"></i></div>
        <div class="stat-info">
            <h3><?= $totalStudents ?></h3>
            <p>Students</p>
        </div>
    </div>
    <div class="stat-card green">
        <div class="stat-icon"><i class="fas fa-book"></i></div>
        <div class="stat-info">
            <h3><?= $totalCourses ?></h3>
            <p>Courses</p>
        </div>
    </div>
    <div class="stat-card orange">
        <div class="stat-icon"><i class="fas fa-link"></i></div>
        <div class="stat-info">
            <h3><?= $totalEnrollments ?></h3>
            <p>Enrollments</p>
        </div>
    </div>
    <div class="stat-card purple">
        <div class="stat-icon"><i class="fas fa-users"></i></div>
        <div class="stat-info">
            <h3><?= $totalUsers ?></h3>
            <p>Users</p>
        </div>
    </div>
</div>

<div class="grid-2">
    <div class="card">
        <h3>Students per Course</h3>
        <canvas id="courseChart"></canvas>
    </div>
    <div class="card">
        <h3>Recent Students</h3>
        <table class="data-table">
            <thead>
                <tr><th>Name</th><th>Joined</th></tr>
            </thead>
            <tbody>
                <?php while ($s = $recentStudents->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($s['first_name'] . ' ' . $s['last_name']) ?></td>
                        <td><?= date('M d, Y', strtotime($s['created_at'])) ?></td>
                    </tr>
                <?php endwhile; ?>
                <?php if ($totalStudents == 0): ?>
                    <tr><td colspan="2" class="text-center">No students yet</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
new Chart(document.getElementById('courseChart'), {
    type: 'bar',
    data: {
        labels: <?= json_encode($courseLabels) ?>,
        datasets: [{
            label: 'Enrollments',
            data: <?= json_encode($courseData) ?>,
            backgroundColor: '#7494ec'
        }]
    },
    options: {
        responsive: true,
        plugins: { legend: { display: false } },
        scales: { y: { beginAtZero: true, ticks: { stepSize: 1 } } }
    }
});
</script>

<?php include '../includes/footer.php'; ?>
