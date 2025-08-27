<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['is_admin'] != 1) {
  header("Location: ../login.php");
  exit();
}
include '../db.php';

$success = "";
$error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $name = trim($_POST['name']);
  $type = trim($_POST['type']);
  $location = trim($_POST['location']);

  if ($name && $type && $location) {
    $stmt = $conn->prepare("INSERT INTO theatres (name, type, location) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $name, $type, $location);
    if ($stmt->execute()) {
      $success = "Theatre added successfully!";
    } else {
      $error = "Failed to add theatre.";
    }
    $stmt->close();
  } else {
    $error = "All fields are required.";
  }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Add Theatre | Admin Panel</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body { background-color: #111; color: white; padding: 40px; }
    .form-control { background: #222; color: white; }
    .form-label { margin-top: 15px; }
    .btn-dark { margin-top: 50px; }
  </style>
</head>
<body>

<div class="container">
  <h2 class="mb-4">➕ Add New Theatre</h2>
  <a href="dashboard.php" class="btn btn-secondary mb-3 float-end">🔙 Back to Dashboard</a>


  <?php if ($success): ?>
    <div class="alert alert-success"><?= $success ?></div>
  <?php elseif ($error): ?>
    <div class="alert alert-danger"><?= $error ?></div>
  <?php endif; ?>

  <form method="POST" action="">
    <label class="form-label">Theatre Name</label>
    <input type="text" name="name" class="form-control" required>

    <label class="form-label">Type (e.g. IMAX, Standard)</label>
    <input type="text" name="type" class="form-control" required>

    <label class="form-label">Location</label>
    <input type="text" name="location" class="form-control" required>

    <button type="submit" class="btn btn-danger btn-dark">Add Theatre</button>
    <a href="manage_theatres.php" class="btn btn-secondary ms-2">Back</a>
  </form>
</div>

</body>
</html>
