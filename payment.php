<?php
include 'session_check.php';
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $movie_id = intval($_POST['movie_id']);
  $theatre_id = intval($_POST['theatre_id']);
  $showtime = $_POST['showtime'];
  $seats = $_POST['seats'];
  $total_price = floatval($_POST['total']);
  $payment_method = $_POST['payment_method'];
  $user_id = $_SESSION['user_id'] ?? 1;
  $snacks = $_POST['snacks'] ?? '';
  $booking_time = date('Y-m-d H:i:s');

  $stmt = $conn->prepare("INSERT INTO bookings (user_id, movie_id, theatre_id, showtime, selected_seats, snacks, total_price, payment_method, booking_time)
                          VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
  $stmt->bind_param("iiisssdss", $user_id, $movie_id, $theatre_id, $showtime, $seats, $snacks, $total_price, $payment_method, $booking_time);
  $stmt->execute();
  $stmt->close();

  echo "<script>window.location.href='confirmation.php';</script>";
  exit();
}

$movie_id = intval($_GET['movie_id']);
$theatre_id = intval($_GET['theatre_id']);
$showtime = $_GET['showtime'];
$seats = $_GET['selected_seats'] ?? 'N/A';

$ticket_counts_arr = [
  'adult' => intval($_GET['ticket_adult'] ?? 0),
  'child' => intval($_GET['ticket_child'] ?? 0),
  'senior' => intval($_GET['ticket_senior'] ?? 0),
];

$snacks_arr = [];
foreach (['popcorn', 'soda', 'nachos', 'hotdog'] as $snack) {
  $qty = intval($_GET["snack_$snack"] ?? 0);
  if ($qty > 0) $snacks_arr[$snack] = $qty;
}
$snacks_summary = implode(', ', array_map(fn($k, $v) => "$v " . ucfirst($k), array_keys($snacks_arr), $snacks_arr));

$stmt = $conn->prepare("SELECT title FROM movies WHERE id = ?");
$stmt->bind_param("i", $movie_id);
$stmt->execute();
$stmt->bind_result($movieTitle);
$stmt->fetch();
$stmt->close();

$ticket_prices = ["adult" => 1000, "child" => 600, "senior" => 700];
$snack_prices = ["popcorn" => 450, "soda" => 350, "nachos" => 250, "hotdog" => 700];

$total = 0;
foreach ($ticket_counts_arr as $type => $qty) {
  $total += $ticket_prices[$type] * $qty;
}
foreach ($snacks_arr as $snack => $qty) {
  $total += $snack_prices[$snack] * $qty;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Confirm Purchase | DeluxeCinemas</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"/>
  <style>
    body {
      background: #0d0d0d;
      color: #fff;
      font-family: 'Segoe UI', sans-serif;
    }

    .summary-card {
      max-width: 800px;
      margin: auto;
      background: #111;
      padding: 30px;
      border-radius: 12px;
      box-shadow: 0 0 15px #000;
    }

    .summary-card h4 {
      color: #e20808;
    }

    .line {
      border-top: 1px solid #444;
      margin: 20px 0;
    }

    .pay-method label {
      display: flex;
      align-items: center;
      gap: 10px;
      cursor: pointer;
      margin-bottom: 10px;
    }

    .pay-method input[type="radio"] {
      accent-color: #e20808;
    }

    .card-details {
      display: none;
      margin-top: 20px;
    }

    .btn-danger {
      width: 100%;
      font-weight: bold;
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

<!-- Close Button -->
<button class="close-button" onclick="confirmExit()">×</button>

<div class="container mt-5">
  <div class="summary-card">
    <h4 class="mb-3">Confirm Your Purchase</h4>

    <p><strong>Movie:</strong> <?= htmlspecialchars($movieTitle) ?></p>
    <p><strong>Showtime:</strong> <?= date("D, M j g:i A", strtotime($showtime)) ?></p>
    <p><strong>Seats:</strong> <?= htmlspecialchars($seats) ?></p>

    <p><strong>Tickets:</strong></p>
    <ul>
      <?php foreach ($ticket_counts_arr as $type => $qty): ?>
        <?php if ($qty > 0): ?>
          <li><?= ucfirst($type) ?> × <?= $qty ?> — PKR<?= number_format($ticket_prices[$type] * $qty, 2) ?></li>
        <?php endif; ?>
      <?php endforeach; ?>
    </ul>

    <?php if (!empty($snacks_arr)): ?>
    <p><strong>Snacks:</strong></p>
    <ul>
      <?php foreach ($snacks_arr as $item => $qty): ?>
        <li><?= ucfirst($item) ?> × <?= $qty ?> — PKR<?= number_format($snack_prices[$item] * $qty, 2) ?></li>
      <?php endforeach; ?>
    </ul>
    <?php endif; ?>

    <div class="line"></div>
    <p><strong>Total:</strong> PKR <?= number_format($total, 2) ?></p>

    <form method="POST">
      <input type="hidden" name="movie_id" value="<?= $movie_id ?>">
      <input type="hidden" name="theatre_id" value="<?= $theatre_id ?>">
      <input type="hidden" name="showtime" value="<?= htmlspecialchars($showtime) ?>">
      <input type="hidden" name="seats" value="<?= htmlspecialchars($seats) ?>">
      <input type="hidden" name="snacks" value="<?= htmlspecialchars($snacks_summary) ?>">
      <input type="hidden" name="total" value="<?= $total ?>">

      <div class="pay-method mt-4">
        <label><input type="radio" name="payment_method" value="Online" required onclick="toggleCard(true)"> Online Payment</label>
        <label><input type="radio" name="payment_method" value="Cash on Counter" onclick="toggleCard(false)"> Cash on Counter</label>
      </div>

      <div class="card-details" id="cardDetails">
        <div class="mb-2">
          <label class="form-label text-light">Card Number</label>
          <input type="text" class="form-control" placeholder="1234 5678 9012 3456" maxlength="19"
                oninput="formatCardNumber(this)" pattern="(?:\d{4} ){3}\d{4}" required>
        </div>
        <div class="row">
            <div class="col">
              <label class="form-label text-light">Expiry</label>
              <input type="month" class="form-control" name="expiry_date" min="<?= date('Y-m') ?>" required>
            </div>
            <div class="col">
              <label class="form-label text-light">CVV</label>
              <input type="text" class="form-control" name="cvv" pattern="\d{3,4}" maxlength="3" required>
            </div>
        </div>
      </div>

      <button type="submit" class="btn btn-danger mt-4">Confirm Booking</button>
    </form>
  </div>
</div>

<div class="mb-5"></div>

<script>
function toggleCard(show) {
  const cardSection = document.getElementById('cardDetails');
  const inputs = cardSection.querySelectorAll('input');
  cardSection.style.display = show ? 'block' : 'none';
  inputs.forEach(input => input.required = show);
}

function formatCardNumber(input) {
  let value = input.value.replace(/\D/g, ''); 
  if (value.length > 16) value = value.slice(0, 16);
  const formatted = value.replace(/(\d{4})(?=\d)/g, '$1 ').trim();
  input.value = formatted;
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
