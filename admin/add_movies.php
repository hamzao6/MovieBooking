<?php
include '../db.php';
session_start();
if (!isset($_SESSION['user_id']) || !$_SESSION['is_admin']) {
  header("Location: ../login.php");
  exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  $title = $_POST['title'];
  $poster_url = $_POST['poster_url'];
  $trailer_url = $_POST['trailer_url'];
  $duration = $_POST['duration'];
  $rating = $_POST['rating'];
  $critics = $_POST['critics_score'];
  $audience = $_POST['audience_score'];
  $status = $_POST['status'];
  $release_date = $_POST['release_date'];
  $is_visible = isset($_POST['is_visible']) ? 1 : 0;
  $category_id = intval($_POST['category_id']);
  $category_id_2 = !empty($_POST['category_id_2']) ? intval($_POST['category_id_2']) : null;

  $stmt = $conn->prepare("
    INSERT INTO movies
    (title, poster_url, trailer_url, duration, rating, critics_score, audience_score, status, release_date, is_visible, category_id, category_id_2)
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
  ");
  $stmt->bind_param(
    "sssssiissiii",
    $title,
    $poster_url,
    $trailer_url,
    $duration,
    $rating,
    $critics,
    $audience,
    $status,
    $release_date,
    $is_visible,
    $category_id,
    $category_id_2
  );
  $stmt->execute();
  $stmt->close();

  header("Location: manage_movies.php");
  exit();
}

$categoryResult = mysqli_query($conn, "SELECT id, name FROM categories ORDER BY name ASC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Add Movie</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-dark text-white p-4">
  <div class="container">
    <h2 class="mb-4 text-danger">Add New Movie</h2>
    <a href="dashboard.php" class="btn btn-secondary mb-3 float-end">🔙 Back to Dashboard</a>

    <form method="POST">
      <div class="mb-3">
        <label class="form-label">Title</label>
        <input type="text" name="title" class="form-control" required>
      </div>
      <div class="mb-3">
        <label class="form-label">Poster URL</label>
        <input type="text" name="poster_url" class="form-control" required>
      </div>
      <div class="mb-3">
        <label class="form-label">Trailer URL</label>
        <input type="text" name="trailer_url" class="form-control">
      </div>
      <div class="mb-3">
        <label class="form-label">Duration</label>
        <input type="text" name="duration" class="form-control" required>
      </div>
      <div class="mb-3">
        <label class="form-label">Rating</label>
        <input type="text" name="rating" class="form-control" required>
      </div>
      <div class="mb-3">
        <label class="form-label">Critics Score</label>
        <input type="number" name="critics_score" class="form-control" required>
      </div>
      <div class="mb-3">
        <label class="form-label">Audience Score</label>
        <input type="number" name="audience_score" class="form-control" required>
      </div>
      <div class="mb-3">
        <label class="form-label">Release Date</label>
        <input type="date" name="release_date" class="form-control" required>
      </div>
      <div class="mb-3">
        <label class="form-label">Status</label>
        <select name="status" class="form-select">
          <option value="now_showing">Now Showing</option>
          <option value="upcoming">Upcoming</option>
        </select>
      </div>

      <!-- Primary -->
      <div class="mb-3">
        <label class="form-label">Primary Category</label>
        <select name="category_id" class="form-select" required>
          <option value="">Select Primary Category</option>
          <?php
          mysqli_data_seek($categoryResult, 0);
          while ($cat = mysqli_fetch_assoc($categoryResult)):
          ?>
            <option value="<?php echo $cat['id']; ?>">
              <?php echo htmlspecialchars($cat['name']); ?>
            </option>
          <?php endwhile; ?>
        </select>
      </div>

      <!-- Secondary  -->
      <div class="mb-3">
        <label class="form-label">Secondary Category (optional)</label>
        <select name="category_id_2" class="form-select">
          <option value="">None</option>
          <?php
          mysqli_data_seek($categoryResult, 0);
          while ($cat = mysqli_fetch_assoc($categoryResult)):
          ?>
            <option value="<?php echo $cat['id']; ?>">
              <?php echo htmlspecialchars($cat['name']); ?>
            </option>
          <?php endwhile; ?>
        </select>
      </div>

      <div class="form-check mb-3">
        <input class="form-check-input" type="checkbox" name="is_visible" id="is_visible" value="1" checked>
        <label class="form-check-label" for="is_visible">
          Show this movie on the website
        </label>
      </div>

      <button type="submit" class="btn btn-danger">Add Movie</button>
    </form>
  </div>
</body>
</html>
