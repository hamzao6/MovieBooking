<?php
include '../db.php';
session_start();

if (!isset($_SESSION['user_id']) || !$_SESSION['is_admin']) {
  header("Location: ../login.php");
  exit();
}

$showtime_id = $_GET['id'] ?? 0;

$stmt = $conn->prepare("
  SELECT s.*, m.title, t.name
  FROM showtimes s
  JOIN movies m ON s.movie_id = m.id
  JOIN theatres t ON s.theatre_id = t.id
  WHERE s.id = ?
");
$stmt->bind_param("i", $showtime_id);
$stmt->execute();
$result = $stmt->get_result();
$showtime = $result->fetch_assoc();
$stmt->close();

if (!$showtime) {
  echo "Showtime not found.";
  exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $movie_id = $_POST['movie_id'];
  $theatre_id = $_POST['theatre_id'];
  $showtime_date = $_POST['showtime_date'];  
  $showtime_time = $_POST['showtime_time'];  

  $showtime_input = $showtime_date . ' ' . $showtime_time;

  $stmt = $conn->prepare("UPDATE showtimes SET movie_id=?, theatre_id=?, showtime=? WHERE id=?");
  $stmt->bind_param("iisi", $movie_id, $theatre_id, $showtime_input, $showtime_id);
  $stmt->execute();
  $stmt->close();

  header("Location: manage_showtimes.php");
  exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Edit Showtime</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-dark text-white p-4">
  <div class="container">
    <h2 class="mb-4 text-warning">Edit Showtime</h2>
    <form method="POST">
      <div class="mb-3">
        <label class="form-label">Movie</label>
        <select name="movie_id" class="form-select">
          <?php
          $movies = $conn->query("SELECT id, title FROM movies WHERE is_visible=1");
          while ($m = $movies->fetch_assoc()) {
            $selected = $m['id'] == $showtime['movie_id'] ? 'selected' : '';
            echo "<option value='{$m['id']}' $selected>{$m['title']}</option>";
          }
          ?>
        </select>
      </div>

      <div class="mb-3">
        <label class="form-label">Theatre</label>
        <select name="theatre_id" class="form-select">
          <?php
          $theatres = $conn->query("SELECT id, name, type FROM theatres");
          while ($t = $theatres->fetch_assoc()) {
            $selected = $t['id'] == $showtime['theatre_id'] ? 'selected' : '';
            echo "<option value='{$t['id']}' $selected>{$t['name']} ({$t['type']})</option>";
          }
          ?>
        </select>
      </div>

      <div class="mb-3">
        <label class="form-label">Showtime Date</label>
        <input type="date" name="showtime_date" value="<?= date('Y-m-d', strtotime($showtime['showtime'])) ?>" class="form-control" required>
      </div>

      <div class="mb-3">
        <label class="form-label">Showtime Time</label>
        <input type="time" name="showtime_time" value="<?= date('H:i', strtotime($showtime['showtime'])) ?>" class="form-control" required>
      </div>

      <button type="submit" class="btn btn-warning">Update Showtime</button>
      <a href="manage_showtimes.php" class="btn btn-secondary">Cancel</a>
    </form>
  </div>
</body>
</html>
