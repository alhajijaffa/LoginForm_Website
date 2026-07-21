<?php
session_start();
include '../config.php';
include '../includes/header.php';

$student = $conn->query("SELECT * FROM students WHERE user_id = " . $_SESSION['user_id'])->fetch_assoc();
?>

<div class="card">
    <div class="profile-header">
        <div class="profile-avatar"><i class="fas fa-user-circle"></i></div>
        <h2><?= htmlspecialchars($_SESSION['name']) ?></h2>
        <span class="badge badge-blue">Student</span>
    </div>
</div>

<?php if ($student): ?>
<div class="grid-2">
    <div class="card">
        <h3>Personal Information</h3>
        <div class="profile-info">
            <div class="info-row"><label>First Name:</label><span><?= htmlspecialchars($student['first_name']) ?></span></div>
            <div class="info-row"><label>Last Name:</label><span><?= htmlspecialchars($student['last_name']) ?></span></div>
            <div class="info-row"><label>Email:</label><span><?= htmlspecialchars($student['email']) ?></span></div>
            <div class="info-row"><label>Gender:</label><span><?= $student['gender'] ?: '-' ?></span></div>
            <div class="info-row"><label>Phone:</label><span><?= $student['phone'] ?: '-' ?></span></div>
            <div class="info-row"><label>Date of Birth:</label><span><?= $student['date_of_birth'] ? date('M d, Y', strtotime($student['date_of_birth'])) : '-' ?></span></div>
            <div class="info-row"><label>Address:</label><span><?= $student['address'] ?: '-' ?></span></div>
            <div class="info-row"><label>Status:</label><span class="badge badge-<?= $student['status'] ?>"><?= ucfirst($student['status']) ?></span></div>
            <div class="info-row"><label>Enrolled Since:</label><span><?= date('M d, Y', strtotime($student['created_at'])) ?></span></div>
        </div>
    </div>

    <div class="card">
        <h3>Academic Summary</h3>
        <?php
        $totalCourses = $conn->query("SELECT COUNT(*) as c FROM enrollments WHERE student_id = {$student['id']}")->fetch_assoc()['c'];
        $avgGrade = $conn->query("SELECT AVG(g.marks) as avg_marks FROM grades g JOIN enrollments e ON g.enrollment_id = e.id WHERE e.student_id = {$student['id']} AND g.marks IS NOT NULL")->fetch_assoc()['avg_marks'];
        ?>
        <div class="profile-info">
            <div class="info-row"><label>Enrolled Courses:</label><span><?= $totalCourses ?></span></div>
            <div class="info-row"><label>Average Marks:</label><span><?= $avgGrade ? round($avgGrade, 1) . '%' : 'N/A' ?></span></div>
            <div class="info-row"><label>Account Email:</label><span><?= htmlspecialchars($_SESSION['email']) ?></span></div>
            <div class="info-row"><label>Account Role:</label><span>Student</span></div>
        </div>
    </div>
</div>
<?php else: ?>
    <div class="card"><p class="text-center" style="padding:40px;">No student profile found. Contact admin.</p></div>
<?php endif; ?>

<?php include '../includes/footer.php'; ?>
