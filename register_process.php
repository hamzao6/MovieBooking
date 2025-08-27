<?php
session_start();
include 'db.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
  $email = $_POST['email'];
  $password = $_POST['password'];
  $first_name = $_POST['first_name'];
  $last_name = $_POST['last_name'];
  $dob = $_POST['dob']; 

  $date_parts = explode('-', $dob);
  if (count($date_parts) === 3) {
    $birth_year = intval($date_parts[0]);
    $birth_month = intval($date_parts[1]);
    $birth_day = intval($date_parts[2]);
  } else {
    die("Invalid date of birth format.");
  }

  $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

  $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
  $stmt->bind_param("s", $email);
  $stmt->execute();
  $stmt->store_result();

  if ($stmt->num_rows > 0) {
    $stmt->close();
    echo "Email already registered. <a href='register.php'>Try again</a>";
    exit();
  }
  $stmt->close();

  $stmt = $conn->prepare("INSERT INTO users (email, password, first_name, last_name, birth_month, birth_day, birth_year) VALUES (?, ?, ?, ?, ?, ?, ?)");
  $stmt->bind_param("sssssii", $email, $hashedPassword, $first_name, $last_name, $birth_month, $birth_day, $birth_year);

  if ($stmt->execute()) {
    $_SESSION['user_id'] = $stmt->insert_id;
    $_SESSION['user_name'] = $first_name;
    header("Location: index.php");
    exit();
  } else {
    echo "Error: " . $stmt->error;
  }

  $stmt->close();
  $conn->close();
} else {
  echo "Invalid request.";
}
?>
