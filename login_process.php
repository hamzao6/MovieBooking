<?php
session_start();
include 'db.php';

$email = $_POST['email'];
$password = $_POST['password'];

$stmt = $conn->prepare("SELECT id, password, is_admin FROM users WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows === 1) {
    $stmt->bind_result($id, $hashedPassword, $is_admin);
    $stmt->fetch();

    if (password_verify($password, $hashedPassword)) {
        $_SESSION['user_id'] = $id;
        $_SESSION['is_admin'] = $is_admin;

        if ($is_admin) {
            header("Location: admin/dashboard.php");
        } else {
            header("Location: index.php");
        }
        exit();
    }
}

$_SESSION['error'] = "Invalid email or password.";
header("Location: login.php");
exit();
?>
