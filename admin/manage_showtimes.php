<?php
include '../db.php';
session_start();
if (!isset($_SESSION['user_id']) || !$_SESSION['is_admin']) {
  header("Location: ../login.php");
  exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['movie_id'], $_POST['theatre_id'], $_POST['showtime'])) {
  $stmt = $conn->prepare("INSERT INTO showtimes (movie_id, theatre_id, showtime) VALUES (?, ?, ?)");
  $stmt->bind_param("iis", $_POST['movie_id'], $_POST['theatre_id'], $_POST['showtime']);
  $stmt->execute();
  $stmt->close();
}

if (isset($_GET['delete'])) {
  $conn->query("DELETE FROM showtimes WHERE id = " . intval($_GET['delete']));
}

$theatreFilter = $_GET['theatre_filter'] ?? 'all';
$categoryFilter = $_GET['category_filter'] ?? 'all';
$movieFilter = $_GET['movie_filter'] ?? 'all';

$where = [];
if ($theatreFilter !== 'all') $where[] = "t.id = '" . intval($theatreFilter) . "'";
if ($categoryFilter !== 'all') $where[] = "m.category_id = '" . intval($categoryFilter) . "'";
if ($movieFilter !== 'all') $where[] = "m.id = '" . intval($movieFilter) . "'";
$whereSQL = count($where) ? 'WHERE ' . implode(' AND ', $where) : '';

$categories = $conn->query("SELECT id, name FROM categories");

$allMovies = $conn->query("SELECT id, title FROM movies ORDER BY title ASC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Manage Showtimes | Admin</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
  <style>
    body {
      margin: 0;
      font-family: 'Segoe UI', sans-serif;
      background-color: #101010;
      color: #fff;
    }
    .sidebar {
      width: 240px;
      position: fixed;
      top: 0;
      bottom: 0;
      background: #1a1a1a;
      padding: 40px 20px;
      border-right: 1px solid #333;
    }
    .sidebar h2 {
      font-size: 22px;
      color: #e20808;
      margin-bottom: 40px;
    }
    .sidebar a {
      display: block;
      color: #ccc;
      padding: 12px 16px;
      border-radius: 6px;
      margin-bottom: 12px;
      text-decoration: none;
      font-weight: 500;
      transition: all 0.3s ease;
    }
    .sidebar a:hover, .sidebar a.active {
      background-color: #e20808;
      color: white;
    }
    .sidebar .logout-link {
      margin-top: 60px;
      color: #ff4d4d !important;
    }
    .main {
      margin-left: 260px;
      padding: 50px 40px;
    }
    h2 {
      color: #e20808;
    }
    .form-select, .form-control {
      background-color: #222;
      color: white;
      border: 1px solid #555;
    }
    .form-select:focus, .form-control:focus {
      border-color: #e20808;
      box-shadow: none;
    }
    .table-dark thead {
      background-color: #2a2a2a;
    }
    .table-dark th {
      color: #e20808;
      width: 20%;
    }
    .table-dark td {
      background-color: #1a1a1a;
      border-color: #333;
      vertical-align: top;
    }
    .table-dark tbody tr:hover {
      background-color: #292929;
    }
    .btn-danger, .btn-warning {
      font-size: 13px;
      padding: 4px 8px;
    }
    .showtime-container {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
      gap: 12px;
      justify-content: start;
      max-width: 500px;
    }

    .showtime-entry {
      background-color: #222;
      padding: 6px 10px;
      border-radius: 6px;
      display: flex;
      align-items: center;
      gap: 4px;
      font-size: 0.75rem;
    }
    .showtime-entry span {
      white-space: nowrap;
    }
  </style>
</head>
<body>

<div class="sidebar">
  <h2>🎬 Admin Panel</h2>
  <a href="dashboard.php">📊 Dashboard</a>
  <a href="manage_theatres.php">🏛️ Manage Theatres</a>
  <a href="manage_movies.php">🎥 Manage Movies</a>
  <a href="manage_showtimes.php" class="active">🕒 Manage Showtimes</a>
  <a href="view_users.php">👤 View Users</a>
  <a href="../logout.php" class="logout-link">🚪 Logout</a>
</div>

<div class="main">
  <h2 class="mb-4">🕒 Manage Showtimes</h2>

  <form method="POST" class="mb-5 row g-3">
    <div class="col-md-3">
      <label class="form-label">🎥 Movie</label>
      <select name="movie_id" class="form-select" required>
        <?php
        $movies = $conn->query("SELECT id, title FROM movies WHERE is_visible = 1");
        while ($m = $movies->fetch_assoc()) {
          echo "<option value='{$m['id']}'>" . htmlspecialchars($m['title']) . "</option>";
        }
        ?>
      </select>
    </div>
    <div class="col-md-3">
      <label class="form-label">🏛️ Theatre</label>
      <select name="theatre_id" class="form-select" required>
        <?php
        $theatres = $conn->query("SELECT id, name FROM theatres");
        while ($t = $theatres->fetch_assoc()) {
          echo "<option value='{$t['id']}'>" . htmlspecialchars($t['name']) . "</option>";
        }
        ?>
      </select>
    </div>
    <div class="col-md-3">
      <label class="form-label">🕔 Showtime</label>
      <input type="datetime-local" name="showtime" class="form-control" required />
    </div>
    <div class="col-md-3 d-flex align-items-end">
      <button type="submit" class="btn btn-danger w-100">➕ Add Showtime</button>
    </div>
  </form>

  <form method="GET" class="row g-3 justify-content-end mb-4">
    <div class="col-md-3">
      <select name="movie_filter" class="form-select" onchange="this.form.submit()">
        <option value="all" <?= $movieFilter === 'all' ? 'selected' : '' ?>>All Movies</option>
        <?php while ($movie = $allMovies->fetch_assoc()): ?>
          <option value="<?= $movie['id'] ?>" <?= $movieFilter == $movie['id'] ? 'selected' : '' ?>>
            <?= htmlspecialchars($movie['title']) ?>
          </option>
        <?php endwhile; ?>
      </select>
    </div>
    <div class="col-md-3">
      <select name="category_filter" class="form-select" onchange="this.form.submit()">
        <option value="all" <?= $categoryFilter === 'all' ? 'selected' : '' ?>>All Categories</option>
        <?php
        $categories->data_seek(0);
        while ($cat = $categories->fetch_assoc()):
        ?>
          <option value="<?= $cat['id'] ?>" <?= $categoryFilter == $cat['id'] ? 'selected' : '' ?>>
            <?= htmlspecialchars($cat['name']) ?>
          </option>
        <?php endwhile; ?>
      </select>
    </div>
    <div class="col-md-3">
      <select name="theatre_filter" class="form-select" onchange="this.form.submit()">
        <option value="all">All Theatres</option>
        <?php
        $theatres = $conn->query("SELECT id, name FROM theatres");
        while ($t = $theatres->fetch_assoc()) {
          $sel = $theatreFilter == $t['id'] ? 'selected' : '';
          echo "<option value='{$t['id']}' $sel>" . htmlspecialchars($t['name']) . "</option>";
        }
        ?>
      </select>
    </div>
  </form>

  <div class="table-responsive">
    <table class="table table-dark table-striped">
      <thead>
        <tr>
          <th>🎥 Movie</th>
          <th>🏛️ Theatre</th>
          <th>🕔 Times</th>
        </tr>
      </thead>
      <tbody>
        <?php
        $result = $conn->query("
          SELECT s.id, m.title AS movie, t.name AS theatre, s.showtime
          FROM showtimes s
          JOIN movies m ON s.movie_id = m.id
          JOIN theatres t ON s.theatre_id = t.id
          $whereSQL
          ORDER BY m.title, t.name, s.showtime ASC
        ");
        $grouped = [];
        while ($row = $result->fetch_assoc()) {
          $key = $row['movie'].'_'.$row['theatre'];
          $grouped[$key]['movie'] = $row['movie'];
          $grouped[$key]['theatre'] = $row['theatre'];
          $grouped[$key]['showtimes'][] = [
            'id' => $row['id'],
            'showtime' => $row['showtime']
          ];
        }

        foreach ($grouped as $group):
        ?>
        <tr>
          <td><?= htmlspecialchars($group['movie']) ?></td>
          <td><?= htmlspecialchars($group['theatre']) ?></td>
          <td>
            <div class="showtime-container">
              <?php foreach ($group['showtimes'] as $st): ?>
                <div class="showtime-entry">
                  <span><?= date('M d, Y - h:i A', strtotime($st['showtime'])) ?></span>
                  <a href='edit_showtimes.php?id=<?= $st['id'] ?>' class='btn btn-sm btn-warning'>Edit</a>
                  <a href='?delete=<?= $st['id'] ?>&movie_filter=<?= $movieFilter ?>&theatre_filter=<?= $theatreFilter ?>&category_filter=<?= $categoryFilter ?>' class='btn btn-sm btn-danger' onclick="return confirm('Are you sure?')">Delete</a>
                </div>
              <?php endforeach; ?>
            </div>
          </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div> 
</body>
</html>
