<?php
session_start();
include '../config.php';
include '../includes/header.php';

$totalStudents = $conn->query("SELECT COUNT(*) as c FROM students WHERE status = 'active'")->fetch_assoc()['c'];
$totalCourses = $conn->query("SELECT COUNT(*) as c FROM courses")->fetch_assoc()['c'];
$totalEnrollments = $conn->query("SELECT COUNT(*) as c FROM enrollments")->fetch_assoc()['c'];
$gradedCount = $conn->query("SELECT COUNT(*) as c FROM grades WHERE graded_by = " . $_SESSION['user_id'])->fetch_assoc()['c'];
?>

<div class="stats-grid">
    <div class="stat-card blue">
        <div class="stat-icon"><i class="fas fa-user-graduate"></i></div>
        <div class="stat-info">
            <h3><?= $totalStudents ?></h3>
            <p>Total Students</p>
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
        <div class="stat-icon"><i class="fas fa-star"></i></div>
        <div class="stat-info">
            <h3><?= $gradedCount ?></h3>
            <p>Grades Given</p>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
