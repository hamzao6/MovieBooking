<?php
include 'session_check.php';
include 'db.php';

$user_id = $_SESSION['user_id'];

if (isset($_POST['cancel_booking'])) {
  $booking_id = intval($_POST['cancel_booking']);
  $stmt = $conn->prepare("DELETE FROM bookings WHERE id = ? AND user_id = ? AND showtime > NOW()");
  $stmt->bind_param("ii", $booking_id, $user_id);
  $stmt->execute();
  $stmt->close();
}

$filter = $_GET['filter'] ?? 'all';
$whereFilter = "";
if ($filter === 'upcoming') {
  $whereFilter = "AND b.showtime > NOW()";
} elseif ($filter === 'past') {
  $whereFilter = "AND b.showtime <= NOW()";
}

$stmt = $conn->prepare("SELECT b.*, m.title AS movie_title, t.name AS theatre_name, t.location FROM bookings b JOIN movies m ON b.movie_id = m.id JOIN theatres t ON b.theatre_id = t.id WHERE b.user_id = ? $whereFilter ORDER BY b.booking_time DESC");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>My Bookings | DeluxeCinemas</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
  <style>
    body {
      background: #0d0d0d; color: #fff; font-family: 'Segoe UI', sans-serif; padding-top: 60px;
    }
    .booking-card {
      background: #1a1a1a; border-radius: 10px; padding: 20px;
      margin-bottom: 20px; box-shadow: 0 0 10px rgba(255, 0, 0, 0.2);
    }
    .booking-card h5 { color: #e20808; }
    .badge-upcoming { background: #198754; }
    .badge-past { background: #6c757d; }
    .filter-btns .btn { margin-right: 8px; }
  </style>
</head>
<body>
<nav class="navbar navbar-dark bg-black fixed-top">
  <div class="container">
    <a class="navbar-brand text-danger fw-bold" href="index.php">DeluxeCinemas</a>
    <span class="navbar-text text-light">My Bookings</span>
  </div>
</nav>
<div class="container">
  <h2 class="text-danger mb-4">🎟 Your Bookings</h2>
  <div class="filter-btns mb-4">
    <a href="?filter=all" class="btn btn-outline-light <?= $filter === 'all' ? 'active' : '' ?>">All</a>
    <a href="?filter=upcoming" class="btn btn-outline-success <?= $filter === 'upcoming' ? 'active' : '' ?>">Upcoming</a>
    <a href="?filter=past" class="btn btn-outline-secondary <?= $filter === 'past' ? 'active' : '' ?>">Past</a>
  </div>

  <?php
  if ($result->num_rows === 0) {
    echo '<p class="text-light">No bookings found.</p>';
  } else {
    while ($row = $result->fetch_assoc()) {
      $isUpcoming = strtotime($row['showtime']) > time();
      $badge = $isUpcoming ? '<span class="badge badge-upcoming">Upcoming</span>' : '<span class="badge badge-past">Past</span>';
      echo '<div class="booking-card">';
      echo "<h5>{$row['movie_title']} $badge</h5>";
      echo "<p><strong>Theatre:</strong> {$row['theatre_name']} ({$row['location']})</p>";
      echo "<p><strong>Showtime:</strong> " . date("D, M j, g:i A", strtotime($row['showtime'])) . "</p>";
      echo "<p><strong>Seats:</strong> " . htmlspecialchars($row['selected_seats']) . "</p>";
      echo "<p><strong>Snacks:</strong> " . ($row['snacks'] ? htmlspecialchars($row['snacks']) : 'None') . "</p>";
      echo "<p><strong>Total:</strong> " . number_format($row['total_price'], 2) . " PKR</p>";
      echo "<p><strong>Payment:</strong> " . ucfirst($row['payment_method']) . "</p>";
      echo "<small class='text-muted'>Booked on " . date("M j, Y g:i A", strtotime($row['booking_time'])) . "</small>";
      if ($isUpcoming) {
        echo '<form method="POST" onsubmit="return confirm(\'Cancel this booking?\')">';
        echo '<input type="hidden" name="cancel_booking" value="' . $row['id'] . '">';
        echo '<button type="submit" class="btn btn-outline-danger mt-3">Cancel Booking</button>';
        echo '</form>';
      }
      echo '</div>';
    }
  }
  ?>
</div>
</body>
</html>
<?php $conn->close(); ?>
