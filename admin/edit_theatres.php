<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['is_admin'] != 1) {
  header("Location: ../login.php");
  exit();
}
include '../db.php';

if (!isset($_GET['id'])) {
  header("Location: manage_theatres.php");
  exit();
}

$theatre_id = intval($_GET['id']);
$error = "";
$success = "";

$stmt = $conn->prepare("SELECT name, type, location FROM theatres WHERE id = ?");
$stmt->bind_param("i", $theatre_id);
$stmt->execute();
$stmt->bind_result($name, $type, $location);
if (!$stmt->fetch()) {
  $stmt->close();
  die("Theatre not found.");
}
$stmt->close();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $name = trim($_POST['name']);
  $type = trim($_POST['type']);
  $location = trim($_POST['location']);

  if ($name && $type && $location) {
    $stmt = $conn->prepare("UPDATE theatres SET name=?, type=?, location=? WHERE id=?");
    $stmt->bind_param("sssi", $name, $type, $location, $theatre_id);
    if ($stmt->execute()) {
      $success = "Theatre updated successfully!";
    } else {
      $error = "Update failed.";
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
  <title>Edit Theatre | Admin</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body { background-color: #111; color: white; padding: 40px; }
    .form-control { background: #222; color: white; }
    .form-label { margin-top: 15px; }
    .btn-dark { margin-top: 20px; }
  </style>
</head>
<body>

<div class="container">
  <h2 class="mb-4">✏️ Edit Theatre</h2>
  <a href="dashboard.php" class="btn btn-secondary mb-3 float-end">🔙 Back to Dashboard</a>


  <?php if ($success): ?>
    <div class="alert alert-success"><?= $success ?></div>
  <?php elseif ($error): ?>
    <div class="alert alert-danger"><?= $error ?></div>
  <?php endif; ?>

  <form method="POST">
    <label class="form-label">Theatre Name</label>
    <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($name) ?>" required>

    <label class="form-label">Type</label>
    <input type="text" name="type" class="form-control" value="<?= htmlspecialchars($type) ?>" required>

    <label class="form-label">Location</label>
    <input type="text" name="location" class="form-control" value="<?= htmlspecialchars($location) ?>" required>

    <button type="submit" class="btn btn-warning btn-dark">Update Theatre</button>
    <a href="manage_theatres.php" class="btn btn-secondary ms-2">Back</a>
  </form>
</div>

</body>
</html>
