<?php
include 'db.php';
session_start();
$isLoggedIn = isset($_SESSION['user_id']);

if (!isset($_GET['movie_id'])) {
  echo "No movie selected. <a href='index.php'>Go back</a>";
  exit();
}

$movie_id = intval($_GET['movie_id']);

$stmt = $conn->prepare("SELECT title, poster_url, trailer_url, duration, rating, critics_score, audience_score FROM movies WHERE id = ?");
$stmt->bind_param("i", $movie_id);
$stmt->execute();
$stmt->bind_result($title, $poster, $trailer, $duration, $rating, $critics, $audience);
if (!$stmt->fetch()) {
  echo "Movie not found.";
  exit();
}
$stmt->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title><?= htmlspecialchars($title) ?> | DeluxeCinemas</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"/>
  <style>
    body { background: #0d0d0d; color: #fff; font-family: 'Segoe UI', sans-serif; }
    .navbar { background-color: #1a1a1a; }
    .movie-poster { border-radius: 12px; box-shadow: 0 0 10px rgba(0,0,0,0.8); }
    .trailer-box iframe {
      width: 100%; height: 300px;
      border-radius: 12px;
      box-shadow: 0 4px 12px rgba(255, 255, 255, 0.1);
    }
    .showtime-btn {
      background: #1a1a1a; border: 1px solid #444; color: #fff;
      margin: 6px 8px 0; padding: 12px 18px;
      border-radius: 30px; font-size: 1rem;
      display: inline-block; transition: 0.3s;
      text-decoration: none;
    }
    .showtime-btn:hover { background: #e20808; color: #fff; transform: scale(1.05); }
    .theatre-section {
      border-top: 1px solid #333; padding-top: 20px; margin-top: 20px;
    }
    .highlighted-theatre {
      border: 2px solid #e20808 !important;
      padding: 25px;
      background-color: #1a1a1a;
      border-radius: 10px;
      box-shadow: 0 0 10px #e20808;
    }
  </style>
</head>
<body>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark">
  <div class="container">
    <a class="navbar-brand text-danger fw-bold" href="index.php">DeluxeCinemas</a>
    <button class="navbar-toggler" data-bs-toggle="collapse" data-bs-target="#navbarNavAltMarkup"><span class="navbar-toggler-icon"></span></button>
    <div class="collapse navbar-collapse justify-content-end" id="navbarNavAltMarkup">
      <div class="navbar-nav">
        <a class="nav-link" href="index.php">Home</a>
        <a class="nav-link" href="now_showing.php">Now Showing</a>
        <a class="nav-link" href="upcoming.php">Upcoming</a>
        <a class="nav-link" href="contactus.html">Contact</a>
        <?php if ($isLoggedIn): ?>
        <div class="ms-3 position-relative">
          <span class="nav-link user-icon" style="cursor:pointer;" onclick="toggleLogoutMenu()">👤</span>
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

<div class="container mt-4">
  <div class="row g-4">
    <!-- Poster -->
    <div class="col-md-3">
      <img src="<?= htmlspecialchars($poster) ?>" alt="Poster" class="img-fluid movie-poster"/>
    </div>

    <!-- Info & Dropdowns -->
    <div class="col-md-5">
      <h2 class="text-danger"><?= htmlspecialchars($title) ?></h2>
      <p><strong><?= htmlspecialchars($duration) ?></strong> | Rated <strong><?= htmlspecialchars($rating) ?></strong></p>
      <p><span class="badge bg-success"><?= $critics ?>% Critics</span>
         <span class="badge bg-warning text-dark"><?= $audience ?>% Audience</span>
      </p>

      <!-- Theatre Dropdown -->
      <form id="filterForm">
        <div class="mb-3">
          <label class="form-label">Select Theatre:</label>
          <select id="theatreSelect" class="form-select">
            <option value="">-- Choose a theatre --</option>
            <?php
            $stmt = $conn->prepare("SELECT DISTINCT t.id, t.name, t.type, t.location FROM showtimes s JOIN theatres t ON s.theatre_id = t.id WHERE s.movie_id = ? ORDER BY t.name");
            $stmt->bind_param("i", $movie_id);
            $stmt->execute();
            $res = $stmt->get_result();
            while ($row = $res->fetch_assoc()) {
              $value = htmlspecialchars($row['name'].'-'.$row['type'].'-'.$row['location']);
              echo '<option value="'.md5($value).'">'.htmlspecialchars($row['name'].' ('.$row['type'].')').'</option>';
            }
            $stmt->close();
            ?>
          </select>
        </div>

        <div class="mb-3">
          <label class="form-label">Select Date:</label>
          <select id="daySelect" class="form-select">
            <?php
              $today = new DateTime();
              for ($i = 0; $i < 7; $i++) {
                $date = clone $today;
                $date->modify("+$i days");
                $label = $i === 0 ? 'Today' : $date->format('D, M j');
                echo "<option value='$i'>$label</option>";
              }
            ?>
          </select>
        </div>
      </form>

      <a href="movie_info.php?movie_id=<?= $movie_id ?>" class="btn btn-danger mt-2">🎬 Movie Info</a>
    </div>

    <!-- Trailer -->
    <div class="col-md-4">
      <h6>🎬 Trailer</h6>
      <div class="trailer-box">
        <iframe src="<?= htmlspecialchars($trailer) ?>" allowfullscreen></iframe>
      </div>
    </div>
  </div>

  <!-- Theatres & Showtimes -->
  <div id="showtimesSection" class="mt-5">
    <?php
    $query = "
      SELECT t.id AS theatre_id, t.name, t.type, t.location, s.showtime
      FROM showtimes s
      JOIN theatres t ON s.theatre_id = t.id
      WHERE s.movie_id = ?
      ORDER BY t.name, s.showtime
    ";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $movie_id);
    $stmt->execute();
    $result = $stmt->get_result();

    $theatres = [];
    $theatreIds = [];

    while ($row = $result->fetch_assoc()) {
      $key = htmlspecialchars($row['name'].'-'.$row['type'].'-'.$row['location']);
      $theatres[$key][] = $row['showtime'];
      $theatreIds[$key] = $row['theatre_id'];
    }
    $stmt->close();

    foreach ($theatres as $key => $times) {
      $theatreId = $theatreIds[$key];
      $htmlId = md5($key);
      echo "<div class='theatre-section' id='theatre-$htmlId'>";
      echo "<h5 class='text-light'>".str_replace('-', ' - ', htmlspecialchars($key))."</h5>";
      echo "<div class='showtime-container' data-theatre-id='$theatreId' data-times='".htmlspecialchars(json_encode($times))."'></div>";
      echo "</div>";
    }
    ?>
  </div>
</div>

<script>
document.getElementById('theatreSelect').addEventListener('change', function () {
  const theatreId = this.value;

  document.querySelectorAll('.theatre-section').forEach(el => {
    el.classList.remove('highlighted-theatre');
  });

  if (theatreId) {
    const target = document.getElementById('theatre-' + theatreId);
    if (target) {
      target.classList.add('highlighted-theatre');
      window.scrollTo({ top: target.offsetTop - 70, behavior: 'smooth' });
    }
  }
});

function renderShowtimes() {
  const dayOffset = parseInt(document.getElementById('daySelect').value);
  const containers = document.querySelectorAll('.showtime-container');

  containers.forEach(container => {
    const rawTimes = JSON.parse(container.dataset.times);
    let showtimes = [...rawTimes];
    const theatreId = container.dataset.theatreId;

    if (dayOffset !== 0) {
      for (let i = showtimes.length - 1; i > 0; i--) {
        const j = Math.floor(Math.random() * (i + 1));
        [showtimes[i], showtimes[j]] = [showtimes[j], showtimes[i]];
      }
    }

    container.innerHTML = '';
    showtimes.forEach(time => {
      const timeObj = new Date(time);
      timeObj.setDate(timeObj.getDate() + dayOffset);
      const formatted = timeObj.toLocaleTimeString([], { hour: 'numeric', minute: '2-digit' });

      const link = document.createElement('a');
      link.href = `seats.php?movie_id=<?= $movie_id ?>&showtime=${encodeURIComponent(timeObj.toISOString())}&theatre_id=${theatreId}`;
      link.className = 'showtime-btn';
      link.textContent = formatted;
      container.appendChild(link);
    });
  });
}

document.getElementById('daySelect').addEventListener('change', renderShowtimes);
renderShowtimes();
</script>
</body>
</html>
<?php $conn->close(); ?>
