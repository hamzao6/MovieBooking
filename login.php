<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Login | Deluxe Cinemas</title>

  <!-- Google Font -->
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">

  <!-- Bootstrap -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />

  <style>
    body {
      margin: 0;
      padding: 0;
      font-family: 'Inter', sans-serif;
      background: linear-gradient(to right,rgb(0, 0, 0),rgb(0, 0, 0));
      color: white;
      min-height: 100vh;
    }

    .form-wrapper {
      max-width: 400px;
      margin: auto;
      padding: 80px 20px 60px;
    }

    .form-wrapper img {
      max-height: 50px;
    }

    .form-wrapper h2 {
      margin-top: 15px;
      font-size: 26px;
      font-weight: 600;
    }

    .form-wrapper p {
      color: #bbb;
      font-size: 14px;
    }

    .form-label {
      color: #ccc;
      margin-bottom: 6px;
    }

    .form-control {
      background-color: #2b184d;
      color: white;
      border: none;
      border-radius: 8px;
      padding: 10px 12px;
    }

    .form-control:focus {
      background-color: #3a2261;
      border: 1px solid #8e44ad;
      box-shadow: none;
    }

    ::placeholder {
      color: #aaa;
    }

    .btn-amc {
      background-color: transparent;
      color: white;
      border: 2px solid white;
      padding: 12px;
      border-radius: 10px;
      width: 100%;
      font-weight: bold;
      transition: 0.3s ease;
    }

    .btn-amc:hover {
      background-color: #ff2e63;
      border-color: #ff2e63;
    }

    a {
      color: #bb86fc;
    }

    a:hover {
      color: #e0b3ff;
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


  <div class="form-wrapper text-center">
    <h2>Sign In</h2>
    <p>Don't have an account? <a href="register.php">Register here</a></p>

    <form action="login_process.php" method="POST" class="text-start mt-4">
      <div class="mb-3">
        <label for="email" class="form-label">Email address</label>
        <input type="email" name="email" id="email" class="form-control" required />
      </div>
      <div class="mb-3">
        <label for="password" class="form-label">Password</label>
        <input type="password" name="password" id="password" class="form-control" required />
      </div>
      <button type="submit" class="btn btn-amc">Login</button>
    </form>
  </div>

<script>
  function confirmExit() {
  if (confirm("Are you sure you want to leave this page? Your data will be lost.")) {
    window.location.href = "index.php";
  }
}
</script>
</body>
</html>
