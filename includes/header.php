<?php
if (!isset($_SESSION)) {
    session_start();
}

if (!isset($_SESSION['email']) || !isset($_SESSION['role'])) {
    header("Location: /index.php");
    exit();
}

$role = $_SESSION['role'];
$currentPage = basename($_SERVER['PHP_SELF']);
$scriptDir = basename(dirname($_SERVER['PHP_SELF']));
$baseUrl = '/' . $scriptDir . '/';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Management Suite</title>
    <link rel="stylesheet" href="<?= $baseUrl ?>../style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <?php if ($role === 'admin'): ?>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <?php endif; ?>
</head>
<body class="dashboard-body">
    <nav class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <h3>SMS</h3>
            <button class="sidebar-toggle" onclick="document.getElementById('sidebar').classList.toggle('collapsed')">
                <i class="fas fa-bars"></i>
            </button>
        </div>
        <div class="sidebar-user">
            <div class="user-avatar">
                <i class="fas fa-user-circle"></i>
            </div>
            <span class="user-name"><?= htmlspecialchars($_SESSION['name']) ?></span>
            <span class="user-role"><?= ucfirst($role) ?></span>
        </div>
        <ul class="sidebar-menu">
            <li><a href="<?= $baseUrl ?>dashboard.php" class="<?= $currentPage === 'dashboard.php' ? 'active' : '' ?>"><i class="fas fa-chart-line"></i> Dashboard</a></li>
            <?php if ($role === 'admin'): ?>
                <li><a href="<?= $baseUrl ?>accounts.php" class="<?= $currentPage === 'accounts.php' ? 'active' : '' ?>"><i class="fas fa-users-cog"></i> Accounts</a></li>
                <li><a href="<?= $baseUrl ?>students.php" class="<?= $currentPage === 'students.php' ? 'active' : '' ?>"><i class="fas fa-user-graduate"></i> Students</a></li>
                <li><a href="<?= $baseUrl ?>courses.php" class="<?= $currentPage === 'courses.php' ? 'active' : '' ?>"><i class="fas fa-book"></i> Courses</a></li>
                <li><a href="<?= $baseUrl ?>enrollments.php" class="<?= $currentPage === 'enrollments.php' ? 'active' : '' ?>"><i class="fas fa-link"></i> Enrollments</a></li>
                <li><a href="<?= $baseUrl ?>modules.php" class="<?= $currentPage === 'modules.php' ? 'active' : '' ?>"><i class="fas fa-list-ul"></i> Modules</a></li>
            <?php elseif ($role === 'teacher'): ?>
                <li><a href="<?= $baseUrl ?>students.php" class="<?= $currentPage === 'students.php' ? 'active' : '' ?>"><i class="fas fa-user-graduate"></i> Students</a></li>
                <li><a href="<?= $baseUrl ?>grades.php" class="<?= $currentPage === 'grades.php' ? 'active' : '' ?>"><i class="fas fa-star"></i> Grades</a></li>
                <li><a href="<?= $baseUrl ?>modules.php" class="<?= $currentPage === 'modules.php' ? 'active' : '' ?>"><i class="fas fa-list-ul"></i> Modules</a></li>
            <?php else: ?>
                <li><a href="<?= $baseUrl ?>courses.php" class="<?= $currentPage === 'courses.php' ? 'active' : '' ?>"><i class="fas fa-book"></i> My Courses</a></li>
                <li><a href="<?= $baseUrl ?>profile.php" class="<?= $currentPage === 'profile.php' ? 'active' : '' ?>"><i class="fas fa-user"></i> Profile</a></li>
            <?php endif; ?>
            <li class="menu-divider"></li>
            <li><a href="/logout.php" class="logout-link"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
        </ul>
    </nav>
    <main class="main-content">
        <div class="topbar">
            <button class="mobile-toggle" onclick="document.getElementById('sidebar').classList.toggle('mobile-open')">
                <i class="fas fa-bars"></i>
            </button>
            <h1 class="page-title"><?php
                if ($currentPage === 'dashboard.php') echo 'Dashboard';
                elseif ($currentPage === 'accounts.php') echo 'Manage Accounts';
                elseif ($currentPage === 'students.php') echo 'Students';
                elseif ($currentPage === 'courses.php') echo ($role === 'student' ? 'My Courses' : 'Courses');
                elseif ($currentPage === 'enrollments.php') echo 'Enrollments';
                elseif ($currentPage === 'modules.php') echo 'Modules';
                elseif ($currentPage === 'grades.php') echo 'Grades';
                elseif ($currentPage === 'course_detail.php') echo 'Course Detail';
                elseif ($currentPage === 'profile.php') echo 'My Profile';
                else echo 'Dashboard';
            ?></h1>
        </div>
        <div class="content-wrapper">
