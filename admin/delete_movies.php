<?php
include '../db.php';
session_start();

if (!isset($_SESSION['user_id']) || !$_SESSION['is_admin']) {
  header("Location: ../login.php");
  exit();
}

if (!isset($_GET['id'])) {
  echo "Movie ID missing.";
  exit();
}

$movie_id = intval($_GET['id']);


$stmt = $conn->prepare("DELETE FROM movies WHERE id = ?");
$stmt->bind_param("i", $movie_id);

if ($stmt->execute()) {
  $stmt->close();
  header("Location: manage_movies.php?msg=deleted");
  exit();
} else {
  echo "Error deleting movie: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>
