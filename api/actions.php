<?php
session_start();
include '../config.php';

if (!isset($_SESSION['email']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../index.php");
    exit();
}

$action = $_POST['action'] ?? '';

function getReferer() {
    $http = $_SERVER['HTTP_REFERER'] ?? '../admin/dashboard.php';
    return $http;
}

function sanitize($conn, $val) {
    return $conn->real_escape_string(trim($val));
}

switch ($action) {

    case 'create_account':
        $name = sanitize($conn, $_POST['name']);
        $email = sanitize($conn, $_POST['email']);
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $role = sanitize($conn, $_POST['role']);

        $check = $conn->query("SELECT id FROM users WHERE email = '$email'");
        if ($check->num_rows > 0) {
            $_SESSION['error'] = 'Email already exists';
            header("Location: " . getReferer());
            exit();
        }

        $conn->query("INSERT INTO users (name, email, password, role) VALUES ('$name', '$email', '$password', '$role')");
        header("Location: " . getReferer());
        exit();

    case 'delete_account':
        $id = intval($_POST['id']);
        if ($id != $_SESSION['user_id']) {
            $conn->query("DELETE FROM users WHERE id = $id");
        }
        header("Location: " . getReferer());
        exit();

    case 'add_student':
        $firstName = sanitize($conn, $_POST['first_name']);
        $lastName = sanitize($conn, $_POST['last_name']);
        $email = sanitize($conn, $_POST['email']);
        $gender = sanitize($conn, $_POST['gender'] ?? '');
        $phone = sanitize($conn, $_POST['phone'] ?? '');
        $dob = sanitize($conn, $_POST['date_of_birth'] ?? '');
        $address = sanitize($conn, $_POST['address'] ?? '');
        $userId = intval($_POST['user_id'] ?? 0);

        $userIdVal = $userId > 0 ? $userId : 'NULL';
        $conn->query("INSERT INTO students (user_id, first_name, last_name, email, gender, phone, date_of_birth, address) VALUES ($userIdVal, '$firstName', '$lastName', '$email', '$gender', '$phone', " . ($dob ? "'$dob'" : "NULL") . ", '$address')");
        header("Location: " . getReferer());
        exit();

    case 'edit_student':
        $id = intval($_POST['id']);
        $firstName = sanitize($conn, $_POST['first_name']);
        $lastName = sanitize($conn, $_POST['last_name']);
        $email = sanitize($conn, $_POST['email']);
        $gender = sanitize($conn, $_POST['gender'] ?? '');
        $phone = sanitize($conn, $_POST['phone'] ?? '');
        $dob = sanitize($conn, $_POST['date_of_birth'] ?? '');
        $address = sanitize($conn, $_POST['address'] ?? '');
        $status = sanitize($conn, $_POST['status'] ?? 'active');

        $dobVal = $dob ? "'$dob'" : "NULL";
        $conn->query("UPDATE students SET first_name='$firstName', last_name='$lastName', email='$email', gender='$gender', phone='$phone', date_of_birth=$dobVal, address='$address', status='$status' WHERE id=$id");
        header("Location: " . getReferer());
        exit();

    case 'delete_student':
        $id = intval($_POST['id']);
        $conn->query("DELETE FROM students WHERE id = $id");
        header("Location: " . getReferer());
        exit();

    case 'add_course':
        $code = sanitize($conn, $_POST['course_code']);
        $name = sanitize($conn, $_POST['course_name']);
        $desc = sanitize($conn, $_POST['description'] ?? '');
        $credits = intval($_POST['credits'] ?? 3);

        $conn->query("INSERT INTO courses (course_code, course_name, description, credits) VALUES ('$code', '$name', '$desc', $credits)");
        header("Location: " . getReferer());
        exit();

    case 'edit_course':
        $id = intval($_POST['id']);
        $code = sanitize($conn, $_POST['course_code']);
        $name = sanitize($conn, $_POST['course_name']);
        $desc = sanitize($conn, $_POST['description'] ?? '');
        $credits = intval($_POST['credits'] ?? 3);

        $conn->query("UPDATE courses SET course_code='$code', course_name='$name', description='$desc', credits=$credits WHERE id=$id");
        header("Location: " . getReferer());
        exit();

    case 'delete_course':
        $id = intval($_POST['id']);
        $conn->query("DELETE FROM courses WHERE id = $id");
        header("Location: " . getReferer());
        exit();

    case 'add_enrollment':
        $studentId = intval($_POST['student_id']);
        $courseId = intval($_POST['course_id']);

        $check = $conn->query("SELECT id FROM enrollments WHERE student_id = $studentId AND course_id = $courseId");
        if ($check->num_rows == 0) {
            $conn->query("INSERT INTO enrollments (student_id, course_id) VALUES ($studentId, $courseId)");
        }
        header("Location: " . getReferer());
        exit();

    case 'remove_enrollment':
        $id = intval($_POST['id']);
        $conn->query("DELETE FROM enrollments WHERE id = $id");
        header("Location: " . getReferer());
        exit();

    case 'add_module':
        $courseId = intval($_POST['course_id']);
        $title = sanitize($conn, $_POST['title']);
        $desc = sanitize($conn, $_POST['description'] ?? '');
        $orderNum = intval($_POST['order_num'] ?? 0);

        $conn->query("INSERT INTO modules (course_id, title, description, order_num) VALUES ($courseId, '$title', '$desc', $orderNum)");
        header("Location: " . getReferer());
        exit();

    case 'edit_module':
        $id = intval($_POST['id']);
        $title = sanitize($conn, $_POST['title']);
        $desc = sanitize($conn, $_POST['description'] ?? '');
        $orderNum = intval($_POST['order_num'] ?? 0);

        $conn->query("UPDATE modules SET title='$title', description='$desc', order_num=$orderNum WHERE id=$id");
        header("Location: " . getReferer());
        exit();

    case 'delete_module':
        $id = intval($_POST['id']);
        $conn->query("DELETE FROM modules WHERE id = $id");
        header("Location: " . getReferer());
        exit();

    case 'save_grades':
        $moduleId = intval($_POST['module_id']);
        $courseId = intval($_POST['course_id']);
        $enrollmentIds = $_POST['enrollment_ids'] ?? [];
        $marks = $_POST['marks'] ?? [];
        $gradesArr = $_POST['grades'] ?? [];
        $comments = $_POST['comments'] ?? [];
        $userId = $_SESSION['user_id'];

        for ($i = 0; $i < count($enrollmentIds); $i++) {
            $eid = intval($enrollmentIds[$i]);
            $mark = $marks[$i] !== '' ? floatval($marks[$i]) : 'NULL';
            $grade = sanitize($conn, $gradesArr[$i] ?? '');
            $comment = sanitize($conn, $comments[$i] ?? '');

            $existing = $conn->query("SELECT id FROM grades WHERE enrollment_id = $eid AND module_id = $moduleId");
            if ($existing->num_rows > 0) {
                $gid = $existing->fetch_assoc()['id'];
                $conn->query("UPDATE grades SET marks=$mark, grade='$grade', comments='$comment', graded_by=$userId, graded_at=NOW() WHERE id=$gid");
            } else {
                $conn->query("INSERT INTO grades (enrollment_id, module_id, marks, grade, comments, graded_by) VALUES ($eid, $moduleId, $mark, '$grade', '$comment', $userId)");
            }
        }
        header("Location: ../teacher/grades.php?course_id=$courseId&module_id=$moduleId");
        exit();

    case 'save_progress':
        $courseId = intval($_POST['course_id']);
        $completed = $_POST['completed'] ?? [];

        foreach ($completed as $studentId => $modules) {
            foreach ($modules as $moduleId => $val) {
                $sid = intval($studentId);
                $mid = intval($moduleId);
                $existing = $conn->query("SELECT id, completed FROM progress WHERE student_id = $sid AND module_id = $mid");
                if ($existing->num_rows > 0) {
                    $row = $existing->fetch_assoc();
                    if ($val == 1 && !$row['completed']) {
                        $conn->query("UPDATE progress SET completed = 1, completed_at = NOW() WHERE id = {$row['id']}");
                    } elseif ($val != 1 && $row['completed']) {
                        $conn->query("UPDATE progress SET completed = 0, completed_at = NULL WHERE id = {$row['id']}");
                    }
                } elseif ($val == 1) {
                    $conn->query("INSERT INTO progress (student_id, module_id, completed, completed_at) VALUES ($sid, $mid, 1, NOW())");
                }
            }
        }
        header("Location: ../teacher/modules.php?course_id=$courseId");
        exit();

    default:
        header("Location: ../admin/dashboard.php");
        exit();
}
?>
