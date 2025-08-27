<?php
include 'db.php';
session_start();
$isLoggedIn = isset($_SESSION['user_id']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Deluxe Cinemas</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
  <style>
    body {
      background-color: #000;
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
        display: flex;
        overflow-x: scroll;
        scrollbar-width: none; 
        -ms-overflow-style: none; 
        gap: 20px;
        padding: 30px 20px 60px;
        scroll-snap-type: x mandatory;
        -webkit-overflow-scrolling: touch;
        position: relative;
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
      transform: scale(1.08);
      opacity: 5;
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

    #logout-menu {
      position: absolute;
      right: 0;
      top: 100%;
      background-color: #e20808;
      padding: 8px;
      border-radius: 8px;
      min-width: 160px;
      box-shadow: 0 4px 8px rgba(209, 7, 7, 0.789);
      z-index: 9999;
    }

    .footer {
  background-color: #111;
  color: #ccc;
  padding: 40px 20px 20px;
  font-family: 'Segoe UI', sans-serif;
}

.footer-container {
  display: flex;
  flex-wrap: wrap;
  justify-content: space-between;
  max-width: 1200px;
  margin: auto;
}

.footer-section {
  flex: 1 1 200px;
  margin: 20px;
}

.footer-section h4, .footer-section h5 {
  color: #fff;
  margin-bottom: 15px;
}

.footer-section p,
.footer-section ul li a {
  font-size: 14px;
  color: #aaa;
  text-decoration: none;
  transition: color 0.2s ease-in-out;
}

.footer-section ul {
  list-style: none;
  padding: 0;
}

.footer-section ul li {
  margin-bottom: 10px;
}

.footer-section ul li a:hover {
  color: #e20808;
}

.social-icons a {
  color: #fff;
  margin-right: 12px;
  font-size: 1.2rem;
  transition: color 0.3s;
}

.social-icons a:hover {
  color: #e20808;
}

.footer-bottom {
  font-size: 14px;
  color: #666;
}

@media (max-width: 768px) {
  .row.align-items-center {
    text-align: center;
  }
  .row.align-items-center h4 {
    margin-top: 20px;
  }
}

.theatre-gallery .main-image img {
  width: 1000px;
  max-height: 600px;
  object-fit: cover;
  transform: scale(1.08);
  opacity: 2;
  box-shadow: 0 0 70px rgba(226, 8, 8, 0.3);   transition: all 0.3s ease;
}

.theatre-gallery .thumbnail-img {
  width: 120px;
  height: 80px;
  object-fit: cover;
  border: 2px solid #444;
  cursor: pointer;
  opacity: 0.7;
  transition: all 0.3s ease;
  border-radius: 4px;
}

.theatre-gallery .thumbnail-img:hover,
.theatre-gallery .thumbnail-img.active-thumbnail {
  transform: scale(1.08);
  opacity: 5;
}
</style>
</head>
<body>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark ">
  <div class="container">
    <a class="navbar-brand fw-bold fs-4 text-danger" href="index.php">DeluxeCinema</a>
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
          <div id="user-profile" class="ms-3 position-relative">
            <span class="nav-link user-icon" onclick="toggleLogoutMenu()" style="cursor: pointer;">👤</span>
            <div id="logout-menu" class="d-none">
              <a class="nav-link text-white" href="logout.php">Logout</a>
              <a class="nav-link text-white" href="my_bookings.php">My Bookings</a>
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

<!-- Hero Carousel -->
<div id="carouselExampleIndicators" class="carousel slide hero-carousel mb-3" data-bs-ride="carousel">
  <div class="carousel-inner">
    <div class="carousel-item active">
      <img src="images/dune-part-two.jpg" class="d-block w-100" alt="Dune" />
    </div>
    <div class="carousel-item">
      <img src="images/ballerina.webp" class="d-block w-100" alt="Ballerina" />
    </div>
    <div class="carousel-item">
      <img src="images/f22.webp" class="d-block w-100" alt="Elio" />
    </div>
  </div>
  <button class="carousel-control-prev" type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide="prev">
    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
  </button>
  <button class="carousel-control-next" type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide="next">
    <span class="carousel-control-next-icon" aria-hidden="true"></span>
  </button>
</div>

<!-- Movies Section -->
<div class="container-fluid carousel-section">
  <div class="carousel-header">
    <h3 class="text-danger">Movies</h3>
    <div>
      <button id="btn-now" class="active" onclick="toggleSection('now')">Now Showing</button>
      <button id="btn-upcoming" onclick="toggleSection('upcoming')">Upcoming</button>
    </div>
  </div>

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
            ' . ($isLoggedIn ? 
            '<a href="book.php?movie_id=' . $row['id'] . '" class="btn btn-ticket">Get Tickets</a>' :
            '<a href="login.php" class="btn btn-ticket">Get Tickets</a>') . '
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
</div>

<section class="py-5" style="background: linear-gradient(to bottom right,rgb(0, 0, 0),rgb(0, 0, 0)); color: white;">
  <div class="container">
    <h2 class="text-center text-danger mb-5">Why Choose Deluxe Cinemas?</h2>
      <p>At Deluxe Cinemas, we believe moviegoing should be more than just watching a film—it should be an unforgettable experience. From the moment you walk through our doors, you’ll discover why we’re the preferred destination for cinema lovers who value comfort, technology, and exceptional service.</p>
      <br><br><br><br>


    <div class="row align-items-center mb-5">
      <div class="col-md-6">
        <h4 class="mb-3">1. Unmatched Comfort & Luxury</h4>
        <p>Escape into a world of plush recliner seating, spacious aisles, and ambient lighting designed to make you feel at home. Every auditorium is crafted to deliver the perfect blend of elegance and relaxation.</p>
      </div>
      <div class="col-md-6 text-center">
        <img src="logo/seats.png" alt="Summer Nights" class="img-fluid rounded shadow" style="max-height: 250px; object-fit: cover;">
      </div>
    </div>
    <br><br><br><br>

    <div class="row align-items-center mb-5 flex-md-row-reverse">
      <div class="col-md-6">
        <h4 class="mb-3"> 2. Cutting-Edge Technology</h4>
        <p>Enjoy your favorite movies with crystal-clear 4K projection and immersive Dolby Atmos sound that puts you right in the center of the action. Our state-of-the-art screens ensure you experience films exactly as the director intended.</p>
      </div>
      <div class="col-md-6 text-center">
        <img src="logo/future.png" alt="Snacks" class="img-fluid rounded shadow" style="max-height: 250px; object-fit: cover;">
      </div>
    </div>
    <br><br><br><br>

    <div class="row align-items-center mb-5">
      <div class="col-md-6">
        <h4 class="mb-3">3. Gourmet Food & Drinks</h4>
        <p>From handcrafted popcorn to freshly prepared gourmet snacks and premium beverages, our concession stands redefine movie snacking. Treat yourself to flavors as memorable as the movies themselves.</p>
      </div>
      <div class="col-md-6 text-center">
        <img src="logo/food.png" alt="Opening Night" class="img-fluid rounded shadow" style="max-height: 250px; object-fit: cover;">
      </div>
    </div>
    <br><br><br><br>

    <div class="row align-items-center flex-md-row-reverse">
      <div class="col-md-6">
        <h4 class="mb-3">4. Exceptional Guest Experience</h4>
        <p>Our friendly staff is dedicated to making every visit special. Whether it’s your first time or your hundredth, you’ll always be welcomed with warm hospitality and personalized service.</p>
      </div>
      <div class="col-md-6 text-center">
        <img src="logo/lobby.png" alt="Luxury Cinema" class="img-fluid rounded shadow" style="max-height: 250px; object-fit: cover;">
      </div>
    </div>
  </div>
</section>

<section class="py-5" style="background-color: #000; color: white;">
  <div class="container">
    <h2 class="text-center text-danger mb-4">Explore Our Best Cinema Of 2024</h2>
    <div class="theatre-gallery text-center">
      <div class="main-image mb-3">
        <img id="mainTheatreImage" src="logo/deluxe.png" alt="Theatre Interior" class="img-fluid rounded" style="max-height: 500px; object-fit: cover;">
      </div>
      <div class="thumbnail-row d-flex justify-content-center gap-3">
        <img src="logo/download.jpg" alt="Thumb 1" class="thumbnail-img active-thumbnail" onclick="changeTheatreImage(this)">
        <img src="logo/exlobby.jpg" alt="Thumb 2" class="thumbnail-img" onclick="changeTheatreImage(this)">
        <img src="logo/deluxe.png" alt="Thumb 3" class="thumbnail-img" onclick="changeTheatreImage(this)">
      </div>
    </div>
  </div>
</section>

<footer class="footer mt-5">
  <div class="footer-container">
    <div class="footer-section brand">
      <h4 class="text-danger">DeluxeCinemas</h4>
      <p>Your ultimate destination for movie magic and luxury experience.</p>
    </div>
    <div class="footer-section">
      <h5>Quick Links</h5>
      <ul>
        <li><a href="index.php">Home</a></li>
        <li><a href="now_showing.php">Now Showing</a></li>
        <li><a href="upcoming.php">Upcoming</a></li>
        <li><a href="contactus.php">Contact</a></li>
      </ul>
    </div>
    <div class="footer-section">
      <h5>Account</h5>
      <ul>
        <li><a href="login.php">Login</a></li>
        <li><a href="register.php">Register</a></li>
        <li><a href="my_bookings.php">My Bookings</a></li>
      </ul>
    </div>
    <div class="footer-section">
      <h5>Visit Us</h5>
      <p>DeluxeCinema Main Branch<br> Clifton, Karachi<br> Pakistan</p>
      <p>Email: support@deluxecinemas.com</p>
    </div>
  </div>
  <div class="footer-bottom text-center mt-3">
    <hr class="bg-secondary" />
    <p>&copy; 2025 DeluxeCinemas. All Rights Reserved.</p>
  </div>
</footer>


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
        currentIndex = index; // 

        const cardWidth = card.offsetWidth + 32;
        container.scrollTo({
          left: currentIndex * cardWidth - (container.offsetWidth / 2 - cardWidth / 2),
          behavior: 'smooth'
        });
      });
    });
  });
  function changeTheatreImage(el) {
    document.getElementById('mainTheatreImage').src = el.src;
    document.querySelectorAll('.thumbnail-img').forEach(img => {
      img.classList.remove('active-thumbnail');
    });
    el.classList.add('active-thumbnail');
  }
</script>
</body>
</html>
<?php $conn->close(); ?>
