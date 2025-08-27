<?php
session_start();
if (!isset($_SESSION['user_id']) || !$_SESSION['is_admin']) {
  header("Location: ../login.php");
  exit();
}

include '../db.php';

$result = $conn->query("SELECT * FROM users WHERE is_admin = 0 ORDER BY created_at DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>View Users | Admin Panel</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
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

    h1 {
      font-size: 28px;
      color: #e20808;
      margin-bottom: 30px;
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

    .table {
      border-radius: 10px;
      overflow: hidden;
    }
  </style>
</head>
<body>

<div class="sidebar">
  <h2>🎬 Admin Panel</h2>
  <a href="dashboard.php">📊 Dashboard</a>
  <a href="manage_theatres.php">🏛️ Manage Theatres</a>
  <a href="manage_movies.php">🎥 Manage Movies</a>
  <a href="manage_showtimes.php">🕒 Manage Showtimes</a>
  <a href="view_users.php" class="active">👤 View Users</a>
  <a href="../logout.php" class="logout-link">🚪 Logout</a>
</div>

<div class="main">
  <h1>👤 Registered Users</h1>

  <div class="table-responsive">
    <table class="table table-dark table-striped table-bordered">
      <thead>
        <tr>
          <th>ID</th>
          <th>Email</th>
          <th>Full Name</th>
          <th>Date of Birth</th>
          <th>Registered At</th>
        </tr>
      </thead>
      <tbody>
        <?php while ($row = $result->fetch_assoc()) { ?>
          <tr>
            <td><?= $row['id'] ?></td>
            <td><?= htmlspecialchars($row['email']) ?></td>
            <td><?= htmlspecialchars($row['first_name'] . ' ' . $row['last_name']) ?></td>
            <td><?= $row['birth_month'] . '/' . $row['birth_day'] . '/' . $row['birth_year'] ?></td>
            <td><?= date('M d, Y', strtotime($row['created_at'])) ?></td>
          </tr>
        <?php } ?>
      </tbody>
    </table>
  </div>
</div>

</body>
</html>
