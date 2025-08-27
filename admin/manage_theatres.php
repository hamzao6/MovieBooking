<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['is_admin'] != 1) {
  header("Location: ../login.php");
  exit();
}
include '../db.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Manage Theatres | Admin Panel</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      margin: 0;
      font-family: 'Segoe UI', sans-serif;
      background-color: #101010;
      color: white;
    }

    .sidebar {
      width: 240px;
      position: fixed;
      top: 0;
      bottom: 0;
      background-color: #1a1a1a;
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
      font-weight: bold;
    }

    .table-dark th {
      background-color: #2a2a2a;
      color: #e20808;
      border-color: #444;
    }

    .table-dark td {
      background-color: #1a1a1a;
      border-color: #333;
      vertical-align: middle;
    }

    .table-dark tbody tr:hover {
      background-color: #292929;
    }

    .btn-add {
      margin-bottom: 20px;
    }

    .alert {
      margin-bottom: 20px;
      border-radius: 8px;
    }

    .btn-sm {
      padding: 4px 12px;
      font-size: 14px;
    }
  </style>
</head>
<body>

<div class="sidebar">
  <h2>🎬 Admin Panel</h2>
  <a href="dashboard.php">📊 Dashboard</a>
  <a href="manage_theatres.php" class="active">🏛️ Manage Theatres</a>
  <a href="manage_movies.php">🎥 Manage Movies</a>
  <a href="manage_showtimes.php">🕒 Manage Showtimes</a>
  <a href="view_users.php">👤 View Users</a>
  <a href="../logout.php" class="logout-link">🚪 Logout</a>
</div>

<div class="main">
  <?php if (isset($_GET['deleted'])): ?>
    <div class="alert alert-success">✅ Theatre deleted successfully.</div>
  <?php elseif (isset($_GET['error']) && $_GET['error'] === 'deletefailed'): ?>
    <div class="alert alert-danger">❌ Failed to delete theatre.</div>
  <?php endif; ?>

  <h2 class="mb-4">🎭 Manage Theatres</h2>
  <a href="add_theatres.php" class="btn btn-danger btn-add">➕ Add New Theatre</a>

  <div class="table-responsive">
    <table class="table table-dark table-striped table-bordered">
      <thead>
        <tr>
          <th>ID</th>
          <th>Name</th>
          <th>Type</th>
          <th>Location</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php
        $result = $conn->query("SELECT * FROM theatres ORDER BY id ASC");
        while ($row = $result->fetch_assoc()):
        ?>
        <tr>
          <td><?= $row['id'] ?></td>
          <td><?= htmlspecialchars($row['name']) ?></td>
          <td><?= htmlspecialchars($row['type']) ?></td>
          <td><?= htmlspecialchars($row['location']) ?></td>
          <td>
            <a href="edit_theatres.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-warning">Edit</a>
            <a href="delete_theatres.php?id=<?= $row['id'] ?>" onclick="return confirm('Are you sure?');" class="btn btn-sm btn-danger">Delete</a>
          </td>
        </tr>
        <?php endwhile; ?>
      </tbody>
    </table>
  </div>
</div>

</body>
</html>
