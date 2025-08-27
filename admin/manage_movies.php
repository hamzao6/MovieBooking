<?php
session_start();
if (!isset($_SESSION['user_id']) || !$_SESSION['is_admin']) {
  header("Location: ../login.php");
  exit();
}
include '../db.php';

$categoriesRes = $conn->query("SELECT id, name FROM categories ORDER BY name ASC");
$categories = [];
while ($row = $categoriesRes->fetch_assoc()) {
    $categories[] = $row;
}

$statusFilter = isset($_GET['status']) ? $_GET['status'] : 'all';
$categoryFilter = isset($_GET['category']) ? $_GET['category'] : 'all';


$whereClauses = [];
if ($statusFilter !== 'all') {
    $whereClauses[] = "m.status = '" . $conn->real_escape_string($statusFilter) . "'";
}
if ($categoryFilter !== 'all') {
    $whereClauses[] = "m.category_id = '" . intval($categoryFilter) . "'";
}
$whereSQL = count($whereClauses) ? 'WHERE ' . implode(' AND ', $whereClauses) : '';

$query = "
    SELECT
        m.*,
        c.name AS category_name,
        c2.name AS category2_name
    FROM
        movies m
    LEFT JOIN
        categories c ON m.category_id = c.id
    LEFT JOIN
        categories c2 ON m.category_id_2 = c2.id
    $whereSQL
    ORDER BY m.id ASC
";
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Manage Movies | Admin</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
  <style>
    body { background: #111; color: white; margin: 0; font-family: 'Segoe UI', sans-serif; }
    .sidebar {
      width: 240px; background-color: #1a1a1a; padding: 40px 20px; position: fixed; top: 0; bottom: 0; border-right: 1px solid #333;
    }
    .sidebar h2 { font-size: 22px; color: #e20808; margin-bottom: 40px; }
    .sidebar a {
      color: #ccc; text-decoration: none; display: block; margin-bottom: 16px; font-size: 16px; padding: 12px 16px;
      border-radius: 6px; transition: background 0.3s ease, color 0.3s ease;
    }
    .sidebar a:hover, .sidebar a.active { background-color: #e20808; color: white; }
    .sidebar .logout-link { margin-top: 60px; color: #ff4d4d !important; }
    .main { margin-left: 260px; padding: 50px 40px; }
    .main h1 { color: #e20808; font-weight: bold; }
    .page-title { display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; flex-wrap: wrap; }
    .filters { display: flex; gap: 1rem; flex-wrap: wrap; }
    .btn-success { background-color: #28a745; border: none; font-size: 15px; }
    .btn-warning, .btn-danger { font-size: 14px; padding: 6px 14px; }
    .table-dark thead { background-color: #2a2a2a; }
    .table-dark th { color: #e20808; }
    .table-dark td { background-color: #1a1a1a; border-color: #333; vertical-align: middle; }
    .table-dark tbody tr:hover { background-color: #292929; }
    @media (max-width: 768px) {
      .main { margin-left: 0; padding: 20px; }
      .sidebar { display: none; }
    }
  </style>
</head>
<body>

<div class="sidebar">
  <h2>🎬 Admin Panel</h2>
  <a href="dashboard.php">📊 Dashboard</a>
  <a href="manage_theatres.php">🏛️ Manage Theatres</a>
  <a href="manage_movies.php" class="active">🎥 Manage Movies</a>
  <a href="manage_showtimes.php">🕒 Manage Showtimes</a>
  <a href="view_users.php">👤 View Users</a>
  <a href="../logout.php" class="logout-link">🚪 Logout</a>
</div>

<div class="main">
  <div class="page-title">
    <h1>🎥 Manage Movies</h1>
    <a href="add_movies.php" class="btn btn-success">➕ Add New Movie</a>
  </div>

  <form method="get" class="filters mb-4">
    <div class="form-group">
      <label for="status" class="form-label">Status</label>
      <select name="status" id="status" class="form-select">
        <option value="all" <?= $statusFilter=='all'?'selected':''; ?>>All</option>
        <option value="now_showing" <?= $statusFilter=='now_showing'?'selected':''; ?>>Now Showing</option>
        <option value="upcoming" <?= $statusFilter=='upcoming'?'selected':''; ?>>Upcoming</option>
      </select>
    </div>
    <div class="form-group">
      <label for="category" class="form-label">Primary Category</label>
      <select name="category" id="category" class="form-select">
        <option value="all" <?= $categoryFilter=='all'?'selected':''; ?>>All</option>
        <?php foreach($categories as $cat): ?>
          <option value="<?= $cat['id'] ?>" <?= $categoryFilter==$cat['id']?'selected':''; ?>>
            <?= htmlspecialchars($cat['name']) ?>
          </option>
        <?php endforeach; ?>
      </select>
    </div>
    <div class="align-self-end">
      <button type="submit" class="btn btn-danger">Filter</button>
    </div>
  </form>

  <div class="table-responsive">
    <table class="table table-dark table-striped align-middle">
      <thead>
        <tr>
          <th>ID</th>
          <th>🎬 Title</th>
          <th>Categories</th>
          <th>Status</th>
          <th>Rating</th>
          <th>Duration</th>
          <th>Release Date</th>
          <th>Critics</th>
          <th>Audience</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php if($result->num_rows): while ($row = $result->fetch_assoc()): ?>
        <tr>
          <td><?= $row['id'] ?></td>
          <td><?= htmlspecialchars($row['title']) ?></td>
          <td>
            <?= htmlspecialchars($row['category_name'] ?: 'Uncategorized') ?>
            <?php if ($row['category2_name']): ?>
              | <?= htmlspecialchars($row['category2_name']) ?>
            <?php endif; ?>
          </td>
          <td><?= htmlspecialchars($row['status']) ?></td>
          <td><?= htmlspecialchars($row['rating']) ?></td>
          <td><?= htmlspecialchars($row['duration']) ?></td>
          <td><?= htmlspecialchars($row['release_date']) ?></td>
          <td><?= htmlspecialchars($row['critics_score']) ?>%</td>
          <td><?= htmlspecialchars($row['audience_score']) ?>%</td>
          <td>
            <a href="edit_movies.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-warning">Edit</a>
            <a href="delete_movies.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this movie?')">Delete</a>
          </td>
        </tr>
        <?php endwhile; else: ?>
        <tr>
          <td colspan="10" class="text-center">No movies found.</td>
        </tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>
</body>
</html>
