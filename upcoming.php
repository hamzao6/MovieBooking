<?php
session_start();
include 'db.php';
$isLoggedIn = isset($_SESSION['user_id']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Upcoming | Deluxe Cinemas</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
  <style>
    body {
      background-color: #111;
      color: #fff;
      font-family: 'Segoe UI', sans-serif;
    }
    .navbar {
      background-color: #1a1a1a;
    }
    .movie-section {
      padding: 3rem 1rem;
    }
    .movie-title {
      font-size: 2rem;
      font-weight: 700;
      text-align: center;
      margin-bottom: 2rem;
      color: #e20808;
    }
    .movie-grid {
      display: grid;
      grid-template-columns: repeat(2, 1fr);
      gap: 1.5rem;
      justify-items: center;
    }
    .movie-card {
      width: 100%;
      max-width: 220px;
      background-color: transparent;
      border-radius: 10px;
      overflow: hidden;
      transition: transform 0.3s ease;
      display: flex;
      flex-direction: column;
      align-items: center;
      text-decoration: none;
      color: inherit;
    }
    .movie-card:hover {
      transform: scale(1.05);
    }
    .movie-card img {
      width: 100%;
      height: 330px;
      object-fit: cover;
    }
    .movie-info {
      padding: 1rem;
      text-align: center;
    }
    .movie-info h5 {
      font-size: 1.1rem;
      font-weight: bold;
      margin-bottom: 0.5rem;
    }
    .movie-meta {
      font-size: 0.9rem;
      color: #ccc;
    }
    .btn-ticket {
      background-color: #e20808;
      color: white;
      margin-top: 0.7rem;
      padding: 6px 16px;
      border: none;
    }
    .btn-ticket.disabled {
      background-color: gray;
      cursor: not-allowed;
    }
    #logout-menu {
      z-index: 9999;
      position: absolute;
      right: 0;
      top: 100%;
      background-color: #e20808;
      padding: 8px;
      border-radius: 8px;
      min-width: 120px;
      box-shadow: 0 4px 8px rgba(209, 7, 7, 0.789);
    }

    @media (min-width: 768px) {
      .movie-grid {
        grid-template-columns: repeat(3, 1fr);
      }
    }

    @media (min-width: 992px) {
      .movie-grid {
        grid-template-columns: repeat(4, 1fr);
      }
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
        <a class="nav-link" href="index.php">Home</a>
        <a class="nav-link" href="now_showing.php">Now Showing</a>
        <a class="nav-link active" href="upcoming.php">Upcoming</a>
        <a class="nav-link" href="contactus.php">Contact</a>
        <?php if ($isLoggedIn): ?>
          <div id="user-profile" class="ms-3 position-relative">
            <span class="nav-link user-icon" onclick="toggleLogoutMenu()" style="cursor: pointer;">👤</span>
            <div id="logout-menu" class="d-none">
              <a class="nav-link text-white" href="logout.php">Logout</a>
              <a class="nav-link text-white" href="bookings.php">My Bookings</a>
            </div>
          </div>
        <?php else: ?>
          <div id="auth-buttons" class="d-flex">
            <a class="nav-link btn btn-danger text-white px-3 ms-2" href="login.php">Login</a>
            <a class="nav-link btn btn-danger text-white px-3 ms-2" href="register.php">Register</a>
          </div>
        <?php endif; ?>
      </div>
    </div>
  </div>
</nav>

<!-- Upcoming Section -->
<div class="container movie-section">
  <h2 class="movie-title">Upcoming</h2>
  <div class="movie-grid">
    <?php
      $query = "SELECT * FROM movies WHERE status = 'upcoming' AND is_visible = 1 ORDER BY release_date ASC";
      $result = mysqli_query($conn, $query);
      if (mysqli_num_rows($result) > 0):
        while ($movie = mysqli_fetch_assoc($result)):
    ?>
      <div class="movie-card">
        <img src="<?php echo htmlspecialchars($movie['poster_url']); ?>" alt="<?php echo htmlspecialchars($movie['title']); ?>">
        <div class="movie-info">
          <h5><?php echo htmlspecialchars($movie['title']); ?></h5>
          <div class="movie-meta">
            <?php echo htmlspecialchars($movie['duration']); ?> | <?php echo htmlspecialchars($movie['rating']); ?><br>
            Releases: <?php echo date('M d, Y', strtotime($movie['release_date'])); ?>
          </div>
          <span class="btn btn-ticket disabled">Coming Soon</span>
        </div>
      </div>
    <?php endwhile; else: ?>
      <p class="text-white text-center">No upcoming movies at the moment.</p>
    <?php endif; ?>
  </div>
</div>

<div class="mb-5"></div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
  function toggleLogoutMenu() {
    document.getElementById('logout-menu').classList.toggle('d-none');
  }
</script>
</body>
</html>

<?php $conn->close(); ?>
