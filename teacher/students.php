<?php
session_start();
include '../config.php';
include '../includes/header.php';

$search = $_GET['search'] ?? '';
if (!empty($search)) {
    $stmt = $conn->prepare("SELECT s.*, c.course_name, c.course_code FROM students s JOIN enrollments e ON s.id = e.student_id JOIN courses c ON e.course_id = c.id WHERE (s.first_name LIKE ? OR s.last_name LIKE ? OR s.email LIKE ?) AND s.status = 'active' ORDER BY s.first_name");
    $like = "%$search%";
    $stmt->bind_param("sss", $like, $like, $like);
    $stmt->execute();
    $students = $stmt->get_result();
} else {
    $students = $conn->query("SELECT s.*, c.course_name, c.course_code FROM students s JOIN enrollments e ON s.id = e.student_id JOIN courses c ON e.course_id = c.id WHERE s.status = 'active' ORDER BY s.first_name");
}
?>

<div class="card">
    <div class="card-header">
        <h3>Students</h3>
        <form method="get" class="search-form">
            <input type="text" name="search" placeholder="Search students..." value="<?= htmlspecialchars($search) ?>">
            <button type="submit" class="btn btn-sm btn-primary"><i class="fas fa-search"></i></button>
        </form>
    </div>
    <table class="data-table">
        <thead>
            <tr><th>Name</th><th>Email</th><th>Gender</th><th>Phone</th><th>Course</th><th>Status</th></tr>
        </thead>
        <tbody>
            <?php while ($s = $students->fetch_assoc()): ?>
                <tr>
                    <td><?= htmlspecialchars($s['first_name'] . ' ' . $s['last_name']) ?></td>
                    <td><?= htmlspecialchars($s['email']) ?></td>
                    <td><?= $s['gender'] ?: '-' ?></td>
                    <td><?= $s['phone'] ?: '-' ?></td>
                    <td><?= htmlspecialchars($s['course_code'] . ' - ' . $s['course_name']) ?></td>
                    <td><span class="badge badge-<?= $s['status'] ?>"><?= ucfirst($s['status']) ?></span></td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

<?php include '../includes/footer.php'; ?>
