<?php
session_start();
include '../db.php';
if (!isset($_SESSION['user_id']) || !$_SESSION['is_admin']) {
  header("Location: ../login.php");
  exit();
}

$totalUsers = $conn->query("SELECT COUNT(*) AS total FROM users WHERE is_admin = 0")->fetch_assoc()['total'];
$totalBookings = $conn->query("SELECT COUNT(*) AS total FROM bookings")->fetch_assoc()['total'];
$totalRevenue = $conn->query("SELECT SUM(total_price) AS total FROM bookings")->fetch_assoc()['total'] ?? 0;
$totalMovies = $conn->query("SELECT COUNT(*) AS total FROM movies")->fetch_assoc()['total'];
$totalTheatres = $conn->query("SELECT COUNT(*) AS total FROM theatres")->fetch_assoc()['total'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Admin Dashboard | DeluxeCinemas</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      background-color: #111;
      color: #fff;
      font-family: 'Segoe UI', sans-serif;
      margin: 0;
      padding: 0;
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
      margin-left: 240px;
      padding: 40px;
    }

    .card-panel {
      display: flex;
      gap: 20px;
      overflow-x: auto;
      padding-bottom: 20px;
    }

    .dashboard-card {
      flex: 1 1 220px;
      background: #1f1f1f;
      border-radius: 12px;
      padding: 30px 20px;
      color: white;
      text-align: center;
      min-width: 220px;
      box-shadow: 0 5px 15px rgba(255, 0, 0, 0.15);
      transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    .dashboard-card:hover {
      transform: translateY(-5px);
      box-shadow: 0 8px 20px rgba(255, 0, 0, 0.25);
    }

    .dashboard-card .title {
      font-size: 15px;
      color: #aaa;
    }

    .dashboard-card .value {
      font-size: 30px;
      font-weight: bold;
      margin-top: 10px;
      color: #fff;
    }

    .quick-links h4 {
      color: #e20808;
      margin-top: 50px;
      margin-bottom: 20px;
    }

    .quick-links .link-card {
      background: #1f1f1f;
      padding: 20px;
      border-radius: 10px;
      text-align: center;
      transition: background 0.3s;
      color: #fff;
      font-weight: 500;
      text-decoration: none;
    }

    .quick-links .link-card:hover {
      background: #e20808;
      color: white;
    }

    ::-webkit-scrollbar {
      height: 8px;
    }

    ::-webkit-scrollbar-thumb {
      background-color: #444;
      border-radius: 4px;
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
  <h1 class="mb-4">Dashboard Overview</h1>

  <div class="card-panel">
    <div class="dashboard-card">
      <div class="title">Total Users</div>
      <div class="value"><?= $totalUsers ?></div>
    </div>
    <div class="dashboard-card">
      <div class="title">Total Bookings</div>
      <div class="value"><?= $totalBookings ?></div>
    </div>
    <div class="dashboard-card">
      <div class="title">Total Revenue</div>
      <div class="value"><?= number_format($totalRevenue) ?> PKR</div>
    </div>
    <div class="dashboard-card">
      <div class="title">Total Movies</div>
      <div class="value"><?= $totalMovies ?></div>
    </div>
    <div class="dashboard-card">
      <div class="title">Total Theatres</div>
      <div class="value"><?= $totalTheatres ?></div>
    </div>
  </div>

  <div class="quick-links">
    <h4>Quick Access</h4>
    <div class="row g-3">
      <div class="col-md-3">
        <a href="manage_theatres.php" class="link-card d-block">🎭 Manage Theatres</a>
      </div>
      <div class="col-md-3">
        <a href="manage_movies.php" class="link-card d-block">🎞 Manage Movies</a>
      </div>
      <div class="col-md-3">
        <a href="manage_showtimes.php" class="link-card d-block">⏰ Manage Showtimes</a>
      </div>
      <div class="col-md-3">
        <a href="view_users.php" class="link-card d-block">👥 View Users</a>
      </div>
    </div>
  </div>
</div>

</body>
</html>
