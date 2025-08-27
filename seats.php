<?php
include 'session_check.php';
include 'db.php';

if (!isset($_GET['movie_id']) || !isset($_GET['showtime']) || !isset($_GET['theatre_id'])) {
  echo "Missing parameters. <a href='index.php'>Go back</a>";
  exit();
}

$movie_id = intval($_GET['movie_id']);
$theatre_id = intval($_GET['theatre_id']);
$showtime = $_GET['showtime'];

$stmt = $conn->prepare("SELECT title FROM movies WHERE id = ?");
$stmt->bind_param("i", $movie_id);
$stmt->execute();
$stmt->bind_result($movieTitle);
$stmt->fetch();
$stmt->close();

$occupiedSeats = [];
$stmt = $conn->prepare("SELECT selected_seats FROM bookings WHERE movie_id = ? AND theatre_id = ? AND showtime = ?");
$stmt->bind_param("iis", $movie_id, $theatre_id, $showtime);
$stmt->execute();
$stmt->bind_result($seatsStr);
while ($stmt->fetch()) {
  $seatsArray = explode(',', $seatsStr);
  $occupiedSeats = array_merge($occupiedSeats, array_map('trim', $seatsArray));
}
$stmt->close();

$rows = ['A','B','C','D','E','F'];
$allSeats = [];
foreach ($rows as $r) {
  for ($i = 1; $i <= 10; $i++) {
    $allSeats[] = $r.$i;
  }
}
shuffle($allSeats);
$randomCount = rand(5,12);
$randomOccupied = array_slice($allSeats,0,$randomCount);
$occupiedSeats = array_unique(array_merge($occupiedSeats, $randomOccupied));
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Select Seats | DeluxeCinemas</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"/>
<style>
  body {
    background: #0d0d0d;
    color: #fff;
    font-family: 'Segoe UI', sans-serif;
  }
  .screen {
    width: 100%;
    max-width: 500px;
    margin: 0 auto 40px auto;
    height: 60px;
    border-top-left-radius: 50% 30px;
    border-top-right-radius: 50% 30px;
    background: linear-gradient(to bottom, #ccc, #444);
    text-align: center;
    line-height: 60px;
    font-weight: bold;
    font-size: 1.1rem;
    box-shadow: 0 -4px 10px rgba(255,255,255,0.2);
  }
  .seat-map {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 10px;
    width: 100%;
  }
  .seat-row {
    display: flex;
    justify-content: center;
    gap: 10px;
    flex-wrap: wrap;
  }
  .seat {
    width: 36px;
    height: 36px;
    background-color: #222;
    border: 2px solid #aaa;
    border-radius: 6px;
    cursor: pointer;
    text-align: center;
    line-height: 32px;
    font-size: 0.8rem;
    transition: transform 0.2s, background 0.3s;
    user-select: none;
  }
  .seat.selected {
    background-color: #e20808;
    border-color: #e20808;
  }
  .seat.occupied {
    background-color: #888;
    cursor: not-allowed;
  }
  .seat.gold { border-color: gold; }
  .seat.platinum { border-color: #00bfff; }
  .seat.box { border-color: #32cd32; }
  .seat:hover:not(.occupied) {
    transform: scale(1.1);
  }
  .legend {
    display: flex;
    flex-wrap: wrap;
    justify-content: center;
    gap: 4px;
    margin-top: 30px;
  }
  .legend-item {
    display: flex;
    align-items: center;
    gap: 8px;
    flex: 1 1 45%;
    max-width: 150px;
  }
  .legend-item .box {
    width: 24px;
    height: 24px;
    border-radius: 4px;
    border: 2px solid #aaa;
  }
  .legend-item .available { background-color: #222; }
  .legend-item .selected { background-color: #e20808; border-color: #e20808; }
  .legend-item .occupied { background-color: #888; }
  .legend-item .gold { border-color: gold; }
  .legend-item .platinum { border-color: #00bfff; }
  .legend-item .box-class { border-color: #32cd32; }
  @media (max-width: 768px) {
    .seat {
      width: 32px;
      height: 32px;
      font-size: 0.75rem;
      line-height: 30px;
    }
    h2 {
      font-size: 1.4rem;
      text-align: center;
    }
  }
  @media (max-width: 480px) {
    .seat {
      width: 28px;
      height: 28px;
      font-size: 0.65rem;
      line-height: 26px;
    }
    .seat-row {
      gap: 6px;
    }
    h2 {
      font-size: 1.2rem;
    }
  }
  .close-button {
    position: fixed;
    top: 12px;
    right: 20px;
    font-size: 1.8rem;
    font-weight: bold;
    color: #fff;
    background-color: transparent;
    border: none;
    z-index: 1000;
    cursor: pointer;
  }
  .close-button:hover {
    color: #e20808;
  }
</style>
</head>
<body>
<button class="close-button" onclick="confirmExit()">×</button>

<div class="container mt-4">
  <h2 class="mb-3 text-danger">🎟 Select Your Seats</h2>
  <p>
    <strong>Movie:</strong> <?= htmlspecialchars($movieTitle) ?><br>
    <strong>Showtime:</strong> <?= htmlspecialchars(date("D, M j g:i A", strtotime($showtime))) ?>
  </p>

  <div class="screen">SCREEN</div>

  <form action="seatsnum.php" method="GET" id="seatForm">
    <input type="hidden" name="movie_id" value="<?= $movie_id ?>">
    <input type="hidden" name="showtime" value="<?= htmlspecialchars($showtime) ?>">
    <input type="hidden" name="theatre_id" value="<?= $theatre_id ?>">
    <input type="hidden" name="seat_count" id="seatCountInput">
    <input type="hidden" name="seats" id="selectedSeatsInput">

    <div class="seat-map">
      <?php
      for ($r = 0; $r < count($rows); $r++) {
        $seatClass = $r < 2 ? 'gold' : ($r < 4 ? 'platinum' : 'box');
        echo '<div class="seat-row">';
        for ($i = 1; $i <= 10; $i++) {
          $seat = $rows[$r].$i;
          $isOccupied = in_array($seat, $occupiedSeats) ? 'occupied' : '';
          echo "<div class='seat $seatClass $isOccupied' data-seat='$seat'>$seat</div>";
        }
        echo '</div>';
      }
      ?>
    </div>

    <div class="legend mt-4">
      <div class="legend-item"><div class="box available"></div> Available</div>
      <div class="legend-item"><div class="box selected"></div> Selected</div>
      <div class="legend-item"><div class="box occupied"></div> Occupied</div>
      <div class="legend-item"><div class="box gold"></div> Golden Class</div>
      <div class="legend-item"><div class="box platinum"></div> Platinum Class</div>
      <div class="legend-item"><div class="box box-class"></div> Box Class</div>
    </div>

    <div class="text-center mt-4">
      <button type="submit" id="continueBtn" class="btn btn-danger px-4" disabled>Continue</button>
    </div>
  </form>
</div>

<div class="mb-5"></div>

<script>
const seats = document.querySelectorAll('.seat');
const selectedSeatsInput = document.getElementById('selectedSeatsInput');
const seatCountInput = document.getElementById('seatCountInput');
const continueBtn = document.getElementById('continueBtn');

function updateSelected() {
  const selected = [...document.querySelectorAll('.seat.selected')].map(s => s.dataset.seat);
  selectedSeatsInput.value = selected.join(',');
  seatCountInput.value = selected.length;
  continueBtn.disabled = selected.length === 0;
}

seats.forEach(seat => {
  seat.addEventListener('click', () => {
    if (!seat.classList.contains('occupied')) {
      seat.classList.toggle('selected');
      updateSelected();
    }
  });
});

function confirmExit() {
  if (confirm("Are you sure you want to leave this page? Your selections will be lost.")) {
    window.location.href = "index.php";
  }
}

updateSelected(); 
</script>
</body>
</html>
<?php $conn->close(); ?>
