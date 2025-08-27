<?php
include 'session_check.php';
include 'db.php';

if (!isset($_GET['movie_id']) || !isset($_GET['showtime']) || !isset($_GET['theatre_id']) || !isset($_GET['seat_count']) || !isset($_GET['seats'])) {
  echo "Missing parameters. <a href='index.php'>Go back</a>";
  exit();
}

$movie_id = intval($_GET['movie_id']);
$theatre_id = intval($_GET['theatre_id']);
$showtime = $_GET['showtime'];
$seatCount = intval($_GET['seat_count']);
$seats = $_GET['seats'];

if ($seatCount <= 0 || empty($seats)) {
  echo "No seats selected. <a href='seats.php'>Go back</a>";
  exit();
}

$stmt = $conn->prepare("SELECT title FROM movies WHERE id = ?");
$stmt->bind_param("i", $movie_id);
$stmt->execute();
$stmt->bind_result($movieTitle);
$stmt->fetch();
$stmt->close();

$ticket_prices = ['adult' => 1000, 'child' => 600, 'senior' => 700];
$snack_prices = ['popcorn' => 450, 'soda' => 350, 'nachos' => 250, 'hotdog' => 700];
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Select Tickets | DeluxeCinemas</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"/>
  <style>
    body {
      background: #0d0d0d;
      color: #fff;
      font-family: 'Segoe UI', sans-serif;
      margin: 0;
      padding: 0;
    }

    .ticket-section {
      max-width: 600px;
      margin: 60px auto;
      background: #111;
      padding: 30px;
      border-radius: 10px;
      box-shadow: 0 0 10px #000;
    }

    .ticket-type, .snack-type {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin: 15px 0;
    }

    h4, h5 {
      margin-bottom: 20px;
    }

    .ticket-controls, .snack-controls {
      display: flex;
      align-items: center;
      gap: 10px;
    }

    .ticket-controls button, .snack-controls button {
      background: #333;
      border: none;
      color: #fff;
      padding: 4px 10px;
      border-radius: 6px;
      font-size: 1.2rem;
    }

    .ticket-controls input, .snack-controls input {
      width: 40px;
      text-align: center;
      background: transparent;
      border: none;
      color: #fff;
      font-size: 1rem;
    }

    .footer {
      margin-top: 30px;
      text-align: center;
    }

    .btn-danger[disabled] {
      background: #555;
      border-color: #555;
    }

    hr {
      border-color: #444;
      margin-top: 30px;
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

<form action="payment.php" method="GET">
  <div class="ticket-section">
    <h4 class="text-danger text-center"><?= htmlspecialchars($movieTitle) ?></h4>
    <p class="text-center mb-4"><?= htmlspecialchars(date("D, M j g:i A", strtotime($showtime))) ?></p>
    <h5 class="text-center">Select <span id="remaining"><?= $seatCount ?></span> Remaining Tickets</h5>

    <?php foreach ($ticket_prices as $id => $price): ?>
      <div class="ticket-type">
        <h5><?= ucfirst($id) ?> <small class="text-muted">(PKR<?= number_format($price, 2) ?>)</small></h5>
        <div class="ticket-controls">
          <button type="button" onclick="updateQty('<?= $id ?>', -1)">−</button>
          <input type="text" name="ticket_<?= $id ?>" id="<?= $id ?>" value="0" readonly>
          <button type="button" onclick="updateQty('<?= $id ?>', 1)">+</button>
        </div>
      </div>
    <?php endforeach; ?>

    <p class="text-center mt-3"><strong>Total Tickets:</strong> <span id="totalCount">0</span> / <?= $seatCount ?></p>

    <hr>
    <h5 class="text-center">🍿 Select Snacks (Optional)</h5>

    <?php foreach ($snack_prices as $id => $price): ?>
      <div class="snack-type">
        <h5><?= ucfirst($id) ?> <small class="text-muted">(PKR<?= number_format($price, 2) ?>)</small></h5>
        <div class="snack-controls">
          <button type="button" onclick="updateSnack('<?= $id ?>', -1)">−</button>
          <input type="text" name="snack_<?= $id ?>" id="snack_<?= $id ?>" value="0" readonly>
          <button type="button" onclick="updateSnack('<?= $id ?>', 1)">+</button>
        </div>
      </div>
    <?php endforeach; ?>

    <div class="footer">
      <input type="hidden" name="movie_id" value="<?= $movie_id ?>">
      <input type="hidden" name="showtime" value="<?= htmlspecialchars($showtime) ?>">
      <input type="hidden" name="theatre_id" value="<?= $theatre_id ?>">
      <input type="hidden" name="seat_count" value="<?= $seatCount ?>">
      <input type="hidden" name="selected_seats" value="<?= htmlspecialchars($seats) ?>">
      <button type="submit" class="btn btn-danger px-4" id="continueBtn" disabled>Continue to Payment</button>
    </div>
  </div>
</form>

<div class="mb-5"></div>

<script>
let maxTickets = <?= $seatCount ?>;

function updateQty(type, change) {
  const input = document.getElementById(type);
  let current = parseInt(input.value);
  let total = getTotalTickets();

  if (change === -1 && current > 0) {
    input.value = current - 1;
  } else if (change === 1 && total < maxTickets) {
    input.value = current + 1;
  }

  document.getElementById("totalCount").textContent = getTotalTickets();
  document.getElementById("continueBtn").disabled = getTotalTickets() !== maxTickets;
}

function updateSnack(type, change) {
  const input = document.getElementById('snack_' + type);
  let current = parseInt(input.value);

  if (change === -1 && current > 0) {
    input.value = current - 1;
  } else if (change === 1) {
    input.value = current + 1;
  }
}

function getTotalTickets() {
  return ['adult', 'child', 'senior'].reduce((sum, id) => {
    return sum + parseInt(document.getElementById(id).value);
  }, 0);
}

function confirmExit() {
  if (confirm("Are you sure you want to leave this page? Your selections will be lost.")) {
    window.location.href = "index.php";
  }
}
</script>
</body>
</html>
<?php $conn->close(); ?>
