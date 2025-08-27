<?php
include 'session_check.php';
include 'db.php';

if (!isset($_GET['movie_id'])) {
  echo "No movie selected. <a href='index.php'>Go back</a>";
  exit();
}

$movie_id = intval($_GET['movie_id']);

$stmt = $conn->prepare("
  SELECT title, poster_url, trailer_url, duration, rating, critics_score, audience_score, release_date, status, synopsis
  FROM movies WHERE id = ?
");
$stmt->bind_param("i", $movie_id);
$stmt->execute();
$stmt->bind_result($title, $poster, $trailer, $duration, $rating, $critics, $audience, $release_date, $status, $synopsis);
if (!$stmt->fetch()) {
  echo "Movie not found.";
  exit();
}
$stmt->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF‑8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title><?= htmlspecialchars($title) ?> | DeluxeCinemas</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"/>
  <style>
    body {
      background: #0d0d0d;
      color: #fff;
      font-family: 'Segoe UI', sans-serif;
    }
    .poster-header {
      background: url('<?= htmlspecialchars($poster) ?>') center/cover no-repeat;
      height: 70vh;
      position: relative;
      display: flex;
      align-items: center;
      justify-content: center;
      text-align: center;
      overflow: hidden;
    }
    .poster-header::before {
      content: "";
      position: absolute;
      inset: 0;
      backdrop-filter: blur(12px);
      background-color: rgba(0, 0, 0, 0.6);
    }
    .poster-content {
      position: relative;
      z-index: 2;
    }
    .poster-content h1 {
      font-size: 3rem;
      font-weight: bold;
    }
    .poster-content p {
      margin-top: 10px;
      font-size: 1rem;
      color: #ccc;
    }
    .score-badge {
      font-size: 0.9rem;
      padding: 0.5em 0.8em;
      margin: 5px;
    }
    .btn-red {
      background: #e20808;
      border: none;
      padding: 12px 28px;
      font-size: 1rem;
      border-radius: 40px;
      font-weight: 500;
      box-shadow: 0 0 12px rgba(255,0,0,0.6);
      transition: transform 0.2s, box-shadow 0.3s;
    }
    .btn-red:hover {
      transform: scale(1.05);
      box-shadow: 0 0 18px rgba(255,0,0,0.9);
    }
    iframe {
      width: 100%;
      height: 450px;
      border: none;
      border-radius: 8px;
      box-shadow: 0 4px 12px rgba(0,0,0,0.5);
    }
    .overview {
      margin: 2rem auto;
      max-width: 800px;
      line-height: 1.6;
    }
    @media (max-width: 768px) {
      .poster-header {
        height: 60vh;
        padding: 20px;
      }
      .poster-content h1 {
        font-size: 2rem;
      }
      iframe {
        height: 250px;
      }
    }

     body {
      background-color: #111;
      color: #fff;
      font-family: 'Segoe UI', sans-serif;
      margin: 0;
      padding: 0;
    }

    .navbar {
      background-color: #1a1a1a;
    }

    .hero-carousel img {
      width: 100%;
      height: auto;
      max-height: 100%;
      object-fit: contain;
      display: block;
      margin: 0 auto;
      background-color: #000;
    }


    .hero-carousel img {
      width: 100%;
      height: 100%;
      object-fit: cover;
    }

    @media (max-width: 768px) {
      .hero-carousel {
        height: 280px;
      }
    }

    .carousel-section {
      padding: 1rem 0 3rem;
      position: relative;
    }

    .carousel-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding: 0 8%;
      margin-bottom: 20px;
    }

    .carousel-header button {
      background-color: transparent;
      border: none;
      color: #e20808;
      font-size: 1rem;
      font-weight: bold;
      margin-left: 12px;
      cursor: pointer;
      border-bottom: 2px solid transparent;
    }

    .carousel-header button.active {
      border-bottom: 2px solid #e20808;
    }

    .scroll-container {
      display: none;
      overflow: hidden;
      gap: 2rem;
      padding-bottom: 80px;
      position: relative;
      scroll-snap-type: x mandatory;
     -ms-overflow-style: none;  
      scrollbar-width: none;   
    }

    .movie-card {
      position: relative;
      width: 200px;
      height: 450px;
      flex: 0 0 auto;
      background: transparent;
      text-align: center;
      transform: scale(1);
      opacity: 0.6;
      pointer-events: auto;
      scroll-snap-align: center;
      transition: transform 0.3s ease, opacity 0.3s ease;
      overflow: visible;
      cursor: pointer;
    }

    .movie-card.active {
      transform: scale(1.1);
      opacity: 1;
      pointer-events: auto;
    }


    .movie-card .btn-ticket {
      position: absolute;
      bottom: 10px;
      left: 50%;
      transform: translateX(-50%);
      display: none;
      background-color: #e20808;
      color: #fff;
      padding: 4px 12px;
      font-size: 0.90rem;
      border: none;
      border-radius: 0px;
      z-index: 5;
    }

    .movie-card.active .btn-ticket {
      display: inline-block;
    }

    .carousel-section .scroll-container {
      display: flex;
      overflow-x: auto;
      padding: 30px 20px 60px; 
      gap: 20px;
      scroll-snap-type: x mandatory;
      -webkit-overflow-scrolling: touch;
      position: relative;
    }

    .movie-card img {
      width: 100%;
      height: 300px;
      object-fit: cover;
      border-radius: 6px;
    }

    .movie-info {
      padding: 5px 0 45px; 
      position: relative;
      z-index: 1;
    }

    .movie-title {
      font-weight: bold;
      font-size: 1rem;
      color: #fff;
      white-space: nowrap;
      overflow: hidden;
      text-overflow: ellipsis;
    }

    .movie-meta {
      font-size: 0.8rem;
      color: #ccc;
    }

    .btn-ticket {
      background-color: #e20808;
      color: #fff;
      padding: 6px 14px;
      border: none;
      font-size: 0.85rem;
      margin-top: 5px;
    }

    .btn-ticket.disabled {
      position: absolute;
      bottom: -5px;
      left: 50%;
      transform: translateX(-50%);
      display: none;
      color: #fff;
      padding: 4px 12px;
      font-size: 0.85rem;
      border: none;
      border-radius: 5px;
      z-index: 5;
      background-color: gray;
      cursor: not-allowed;
    }

    .scroll-btn {
      position: absolute;
      top: 50%;
      transform: translateY(-50%);
      background-color: rgba(0, 0, 0, 0.6);
      border: none;
      color: #fff;
      font-size: 2rem;
      width: 40px;
      height: 60px;
      cursor: pointer;
      z-index: 5;
    }

    .scroll-btn.left {
      left: 0;
    }

    .scroll-btn.right {
      right: 0;
    }

    .show-more {
      text-align: center;
      margin-top: 20px;
    }

    .show-more button {
      background-color: #e20808;
      border: none;
      color: #fff;
      padding: 10px 25px;
      font-size: 1rem;
      margin-bottom:20px;
    }

    .btn-ticket.disabled {
      position: absolute;
      bottom: -5px;
      left: 50%;
      transform: translateX(-50%);
      display: none;
      color: #fff;
      padding: 4px 12px;
      font-size: 0.85rem;
      border: none;
      border-radius: 5px;
      z-index: 5;
      background-color: gray;
      cursor: not-allowed;
    }

  </style>
</head>
<body>

<nav class="navbar navbar-dark bg-black">
  <div class="container">
    <a href="index.php" class="navbar-brand text-danger fw-bold">DeluxeCinemas</a>
  </div>
</nav>

<div class="poster-header">
  <div class="poster-content">
    <h1><?= htmlspecialchars($title) ?></h1>
    <p><?= htmlspecialchars($release_date) ?> • <?= htmlspecialchars($duration) ?> • Rated <?= htmlspecialchars($rating) ?></p>
    <div>
      <span class="badge bg-success score-badge"><?= $critics ?>% Critics</span>
      <span class="badge bg-warning text-dark score-badge"><?= $audience ?>% Audience</span>
    </div>
    <a href="#trailer" class="btn btn-red mt-3">🎬 Watch Trailer</a>
  </div>
</div>

<main class="container my-5">
  <section id="trailer">
    <h3 class="text-danger mb-3">Trailer</h3>
    <iframe src="<?= htmlspecialchars($trailer) ?>" allowfullscreen></iframe>
  </section>

  <section class="overview">
    <h3 class="text-danger mb-3">About the Movie</h3>
    <p><?= nl2br(htmlspecialchars($synopsis)) ?></p>
  </section>

  <div class="d-flex justify-content-center mt-4">
    <a href="book.php?movie_id=<?= $movie_id ?>" class="btn btn-red">🎟 Book Tickets</a>
  </div>
</main>

<!-- Movies Section -->
<div class="container-fluid carousel-section">
  <div class="carousel-header">
    <h3 class="text-danger">Movies</h3>
    <div>
      <button id="btn-now" class="active" onclick="toggleSection('now')">Now Showing</button>
      <button id="btn-upcoming" onclick="toggleSection('upcoming')">Upcoming</button>
    </div>
  </div>

  <!-- Scroll Buttons -->
  <button class="scroll-btn left" onclick="scrollMovies(-1)">&#8249;</button>
  <button class="scroll-btn right" onclick="scrollMovies(1)">&#8250;</button>

  <!-- Now Showing -->
  <div class="scroll-container" id="movieCarousel">
    <?php
      $result = $conn->query("SELECT * FROM movies WHERE status = 'now_showing' AND is_visible = 1 ORDER BY release_date DESC LIMIT 10");
      while ($row = $result->fetch_assoc()) {
        echo '
        <div class="movie-card">
          <img src="' . $row['poster_url'] . '" alt="' . htmlspecialchars($row['title']) . '" />
          <div class="movie-info">
            <div class="movie-title">' . htmlspecialchars($row['title']) . '</div>
            <div class="movie-meta">
              ' . htmlspecialchars($row['duration']) . '<br>
              ' . htmlspecialchars($row['rating']) . '<br>
              Released ' . date('M d, Y', strtotime($row['release_date'])) . '
            </div>
            <a href="book.php?movie_id=' . $row['id'] . '" class="btn btn-ticket">Get Tickets</a>
          </div>
        </div>';
      }
    ?>
  </div>
  <div class="show-more">
    <a href="now_showing.php?filter=now_showing"><button>Show More</button></a>
  </div>
  <!-- Upcoming -->
  <div class="scroll-container d-none" id="upcomingCarousel">
    <?php
      $result = $conn->query("SELECT * FROM movies WHERE status = 'upcoming' AND is_visible = 1 ORDER BY release_date ASC LIMIT 10");
      while ($row = $result->fetch_assoc()) {
        echo '
          <div class="movie-card">
            <img src="' . $row['poster_url'] . '" alt="' . htmlspecialchars($row['title']) . '" />
            <div class="movie-info">
              <div class="movie-title">' . htmlspecialchars($row['title']) . '</div>
              <div class="movie-meta">
                ' . htmlspecialchars($row['duration']) . '<br>
                ' . htmlspecialchars($row['rating']) . '<br>
                Releases ' . date('M d, Y', strtotime($row['release_date'])) . '
              </div>
              <span class="btn btn-ticket disabled">Coming Soon</span>
            </div>
          </div>';
      }
    ?>
  </div>
  <div class="show-more d-none" id="upcomingMore">
    <a href="all_movies.php?filter=upcoming"><button>Show More</button></a>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
  function toggleLogoutMenu() {
    document.getElementById('logout-menu').classList.toggle('d-none');
  }
  function toggleSection(section) {
    const now = document.getElementById('movieCarousel');
    const upcoming = document.getElementById('upcomingCarousel');
    const nowMore = document.querySelector('.show-more');
    const upcomingMore = document.getElementById('upcomingMore');
    now.classList.toggle('d-none', section !== 'now');
    nowMore.classList.toggle('d-none', section !== 'now');
    upcoming.classList.toggle('d-none', section !== 'upcoming');
    upcomingMore.classList.toggle('d-none', section !== 'upcoming');
    document.getElementById('btn-now').classList.toggle('active', section === 'now');
    document.getElementById('btn-upcoming').classList.toggle('active', section === 'upcoming');
    setTimeout(() => centerActiveCard(), 50);
  }
  let currentIndex = 0;
  function scrollMovies(direction) {
    const container = document.querySelector('.scroll-container:not(.d-none)');
    const cards = container.querySelectorAll('.movie-card');
    const total = cards.length;
    if (!total) return;
    currentIndex = (currentIndex + direction + total) % total;
    container.classList.add('scrolling');
    const cardWidth = cards[0].offsetWidth + 32;
    container.scrollTo({
      left: currentIndex * cardWidth - (container.offsetWidth / 2 - cardWidth / 2),
      behavior: 'smooth'
    });
    cards.forEach(card => card.classList.remove('active'));
    cards[currentIndex].classList.add('active');
    setTimeout(() => {
      container.classList.remove('scrolling');
    }, 600);
  }
  function centerActiveCard() {
    const container = document.querySelector('.scroll-container:not(.d-none)');
    const cards = container.querySelectorAll('.movie-card');
    const total = cards.length;
    if (!total) return;
    currentIndex = Math.floor(total / 2);
    cards.forEach(card => card.classList.remove('active'));
    const cardWidth = cards[0].offsetWidth + 32;
    container.scrollTo({
      left: currentIndex * cardWidth - (container.offsetWidth / 2 - cardWidth / 2),
      behavior: 'auto'
    });
    cards[currentIndex].classList.add('active');
  }
  window.addEventListener('load', () => {
    centerActiveCard();
    
    document.querySelectorAll('.movie-card').forEach(card => {
    card.addEventListener('click', () => {
        const container = card.closest('.scroll-container');
        const cards = container.querySelectorAll('.movie-card');
        cards.forEach(c => c.classList.remove('active'));
        card.classList.add('active');

        const index = Array.from(cards).indexOf(card);
        currentIndex = index; 

        const cardWidth = card.offsetWidth + 32;
        container.scrollTo({
          left: currentIndex * cardWidth - (container.offsetWidth / 2 - cardWidth / 2),
          behavior: 'smooth'
        });
      });
    });
  });

</script>


</body>
</html>
<?php $conn->close(); ?>
