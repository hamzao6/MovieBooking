<?php
include 'db.php';
session_start();
$isLoggedIn = isset($_SESSION['user_id']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Contact | DeluxeCinemas</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      background-color: #121212;
      color: #ffffff;
      font-family: 'Segoe UI', sans-serif;
    }

    h2 {
      font-weight: bold;
      margin-bottom: 2rem;
    }

    .card {
      background-color: #1e1e1e;
      border: none;
      border-radius: 1rem;
      transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    .card:hover {
      transform: translateY(-5px);
      box-shadow: 0 0 15px rgba(255, 0, 0, 0.3);
    }

    .form-label {
      color: #f5f5f5;
      font-weight: 600;
    }

    .form-control {
      background-color: #2b2b2b;
      border: none;
      color: white;
    }

    .form-control:focus {
      background-color: #2b2b2b;
      color: white;
      border-color: #e50914;
      box-shadow: 0 0 5px rgba(229, 9, 20, 0.5);
    }

    .btn-primary {
      background-color: #e50914;
      border: none;
      padding: 0.7rem 2rem;
      font-size: 1.1rem;
      border-radius: 30px;
    }

    .btn-primary:hover {
      background-color: #ff2c2c;
    }
  </style>
</head>
<body>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
  <div class="container">
    <a class="navbar-brand fw-bold fs-4 text-danger" href="index.php">DeluxeCinemas</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavAltMarkup">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse justify-content-end" id="navbarNavAltMarkup">
      <div class="navbar-nav">
        <a class="nav-link active" href="index.php">Home</a>
        <a class="nav-link" href="now_showing.php">Now Showing</a>
        <a class="nav-link" href="upcoming.php">Upcoming</a>
        <a class="nav-link" href="contactus.php">Contact</a>
        <?php if ($isLoggedIn): ?>
          <div class="ms-3 position-relative">
            <span class="nav-link user-icon" onclick="toggleLogoutMenu()" style="cursor: pointer;">👤</span>
            <div id="logout-menu" class="d-none">
              <a class="nav-link text-white" href="logout.php">Logout</a>
              <a class="nav-link text-white" href="bookings.php">My Bookings</a>
              <a class="nav-link text-white" href="history.php">Previous</a>
              <a class="nav-link text-white" href="orders.php">Payments</a>
            </div>
          </div>
        <?php else: ?>
          <div class="d-flex">
            <a class="nav-link btn btn-danger text-white px-3 ms-2" href="login.php">Login</a>
            <a class="nav-link btn btn-danger text-white px-3 ms-2" href="register.php">Register</a>
          </div>
        <?php endif; ?>
      </div>
    </div>
  </div>
</nav>

<!-- Contact Form -->
<div class="container my-5">
  <h2 class="text-center text-danger">Contact Us</h2>
  <form action="contactus.php" method="POST">
    <div class="row justify-content-center">
      <div class="col-md-4 m-2">
        <div class="card p-3">
          <label for="name" class="form-label">Name</label>
          <input type="text" class="form-control" id="name" name="name" required>
        </div>
      </div>

      <div class="col-md-4 m-2">
        <div class="card p-3">
          <label for="email" class="form-label">Email</label>
          <input type="email" class="form-control" id="email" name="email" required>
        </div>
      </div>

      <div class="col-md-4 m-2">
        <div class="card p-3">
          <label for="phone" class="form-label">Phone</label>
          <input type="tel" class="form-control" id="phone" name="phone" required>
        </div>
      </div>

      <div class="col-md-4 m-2">
        <div class="card p-3">
          <label for="message" class="form-label">Message</label>
          <textarea class="form-control" id="message" name="message" rows="1" required></textarea>
        </div>
      </div>
    </div>

    <div class="text-center mt-4">
      <button type="submit" class="btn btn-primary">Send Message</button>
    </div>
  </form>
</div>

<!-- Bootstrap Scripts -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
