<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Booking Confirmed | Deluxe Cinemas</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"/>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"/>
  <style>
    body {
      margin: 0;
      padding: 0;
      background: radial-gradient(circle at center, #000000 40%, #1a1a1a 100%);
      color: #fff;
      font-family: 'Segoe UI', sans-serif;
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100vh;
      overflow: hidden;
    }

    .confirmation-container {
      background: rgba(0, 0, 0, 0.85);
      padding: 40px 30px;
      border-radius: 16px;
      text-align: center;
      box-shadow: 0 0 30px rgba(255, 0, 0, 0.3);
      max-width: 480px;
      animation: slideFade 1s ease-in-out;
    }

    .confirmation-container .icon {
      font-size: 70px;
      color: #e20808;
      margin-bottom: 20px;
      animation: pop 0.6s ease-in-out;
    }

    .confirmation-container h2 {
      font-weight: bold;
      color: #fff;
      margin-bottom: 15px;
      text-shadow: 0 0 5px #e20808;
    }

    .confirmation-container p {
      font-size: 16px;
      color: #ccc;
    }

    .btn-amc {
      margin-top: 30px;
      padding: 12px 28px;
      font-size: 16px;
      border: none;
      background: linear-gradient(145deg, #e20808, #b40000);
      color: white;
      border-radius: 50px;
      box-shadow: 0 0 10px #e20808;
      cursor: pointer;
      transition: transform 0.2s ease, box-shadow 0.2s ease;
    }

    .btn-amc:hover {
      transform: scale(1.05);
      box-shadow: 0 0 15px #ff0000;
    }

    @keyframes slideFade {
      from {
        opacity: 0;
        transform: translateY(-30px);
      }
      to {
        opacity: 1;
        transform: translateY(0);
      }
    }

    @keyframes pop {
      0% { transform: scale(0); opacity: 0; }
      60% { transform: scale(1.2); opacity: 1; }
      100% { transform: scale(1); }
    }

    @media (max-width: 576px) {
      .confirmation-container {
        padding: 30px 20px;
      }

      .btn-amc {
        width: 100%;
      }
    }
  </style>
</head>
<body>

<div class="confirmation-container">
  <div class="icon"><i class="fas fa-ticket-alt"></i></div>
  <h2>Booking Confirmed</h2>
  <p>Your ticket has been successfully booked! 🎉</p>
  <p>Thank you for choosing <strong>Deluxe Cinemas</strong> 🍿</p>
  <button class="btn-amc" onclick="goHome()">Return to Home</button>
</div>

<script>
  function goHome() {
    localStorage.clear();
    window.location.href = "index.php";
  }
</script>

</body>
</html>
