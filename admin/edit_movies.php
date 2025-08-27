<?php
include '../db.php';
session_start();
if (!isset($_SESSION['user_id']) || !$_SESSION['is_admin']) {
  header("Location: ../login.php");
  exit();
}

if (!isset($_GET['id'])) {
  echo "Movie ID is missing.";
  exit();
}

$movie_id = intval($_GET['id']);

$stmt = $conn->prepare("SELECT * FROM movies WHERE id = ?");
$stmt->bind_param("i", $movie_id);
$stmt->execute();
$result = $stmt->get_result();
$movie = $result->fetch_assoc();
$stmt->close();

if (!$movie) {
  echo "Movie not found.";
  exit();
}

$categories = [];
$catRes = $conn->query("SELECT id, name FROM categories ORDER BY name ASC");
while ($row = $catRes->fetch_assoc()) {
  $categories[] = $row;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  $title = $_POST['title'];
  $poster_url = $_POST['poster_url'];
  $trailer_url = $_POST['trailer_url'];
  $duration = $_POST['duration'];
  $rating = $_POST['rating'];
  $critics = intval($_POST['critics_score']);
  $audience = intval($_POST['audience_score']);
  $status = $_POST['status'];
  $is_visible = isset($_POST['is_visible']) ? 1 : 0;
  $release_date = $_POST['release_date'];
  $category_id = intval($_POST['category_id']);
  $category_id_2 = !empty($_POST['category_id_2']) ? intval($_POST['category_id_2']) : null;

$stmt = $conn->prepare("
  UPDATE movies 
  SET 
    title=?, 
    poster_url=?, 
    trailer_url=?, 
    duration=?, 
    rating=?, 
    critics_score=?, 
    audience_score=?, 
    status=?, 
    is_visible=?, 
    release_date=?,
    category_id=?, 
    category_id_2=?
  WHERE id=?
");

$cat2 = ($category_id_2 === null) ? 0 : $category_id_2;

$stmt->bind_param(
  "sssssiisiiiii",
  $title,
  $poster_url,
  $trailer_url,
  $duration,
  $rating,
  $critics,
  $audience,
  $status,
  $is_visible,
  $release_date,
  $category_id,
  $cat2,
  $movie_id
);


  $stmt->execute();
  $stmt->close();

  header("Location: manage_movies.php");
  exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Edit Movie</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-dark text-white p-4">
  <div class="container">
    <h2 class="mb-4 text-danger">Edit Movie</h2>
    <a href="dashboard.php" class="btn btn-secondary mb-3 float-end">🔙 Back to Dashboard</a>

    <form method="POST">
      <div class="mb-3">
        <label class="form-label">Title</label>
        <input type="text" name="title" value="<?= htmlspecialchars($movie['title']) ?>" class="form-control" required>
      </div>

      <div class="mb-3">
        <label class="form-label">Poster URL</label>
        <input type="text" name="poster_url" value="<?= htmlspecialchars($movie['poster_url']) ?>" class="form-control" required>
      </div>

      <div class="mb-3">
        <label class="form-label">Trailer URL</label>
        <input type="text" name="trailer_url" value="<?= htmlspecialchars($movie['trailer_url']) ?>" class="form-control">
      </div>

      <div class="mb-3">
        <label class="form-label">Duration</label>
        <input type="text" name="duration" value="<?= htmlspecialchars($movie['duration']) ?>" class="form-control" required>
      </div>

      <div class="mb-3">
        <label class="form-label">Rating</label>
        <input type="text" name="rating" value="<?= htmlspecialchars($movie['rating']) ?>" class="form-control" required>
      </div>

      <div class="mb-3">
        <label class="form-label">Critics Score</label>
        <input type="number" name="critics_score" value="<?= intval($movie['critics_score']) ?>" class="form-control" required>
      </div>

      <div class="mb-3">
        <label class="form-label">Audience Score</label>
        <input type="number" name="audience_score" value="<?= intval($movie['audience_score']) ?>" class="form-control" required>
      </div>

      <div class="mb-3">
        <label class="form-label">Release Date</label>
        <input type="date" name="release_date" value="<?= htmlspecialchars($movie['release_date']) ?>" class="form-control" required>
      </div>

      <div class="mb-3">
        <label class="form-label">Status</label>
        <select name="status" class="form-select" required>
          <option value="now_showing" <?= $movie['status'] == 'now_showing' ? 'selected' : '' ?>>Now Showing</option>
          <option value="upcoming" <?= $movie['status'] == 'upcoming' ? 'selected' : '' ?>>Upcoming</option>
        </select>
      </div>

      <div class="mb-3">
        <label class="form-label">Primary Category</label>
        <select name="category_id" class="form-select" required>
          <option value="">-- Select Primary Category --</option>
          <?php foreach ($categories as $cat): ?>
            <option value="<?= $cat['id'] ?>" <?= $movie['category_id'] == $cat['id'] ? 'selected' : '' ?>>
              <?= htmlspecialchars($cat['name']) ?>
            </option>
          <?php endforeach; ?>
        </select>
      </div>

      <div class="mb-3">
        <label class="form-label">Secondary Category (optional)</label>
        <select name="category_id_2" class="form-select">
          <option value="">None</option>
          <?php foreach ($categories as $cat): ?>
            <option value="<?= $cat['id'] ?>" <?= $movie['category_id_2'] == $cat['id'] ? 'selected' : '' ?>>
              <?= htmlspecialchars($cat['name']) ?>
            </option>
          <?php endforeach; ?>
        </select>
      </div>

      <div class="form-check mb-4">
        <input class="form-check-input" type="checkbox" name="is_visible" id="is_visible" value="1" <?= $movie['is_visible'] ? 'checked' : '' ?>>
        <label class="form-check-label" for="is_visible">
          Show this movie on the website
        </label>
      </div>

      <button type="submit" class="btn btn-danger">Update Movie</button>
    </form>
  </div>
</body>
</html>
