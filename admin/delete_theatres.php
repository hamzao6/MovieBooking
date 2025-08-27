<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['is_admin'] != 1) {
  header("Location: ../login.php");
  exit();
}

include '../db.php';

if (!isset($_GET['id'])) {
  header("Location: manage_theatres.php?error=deletefailed");
  exit();
}

$theatre_id = intval($_GET['id']);

$conn->begin_transaction();

try {

  $stmt1 = $conn->prepare("DELETE FROM showtimes WHERE theatre_id = ?");
  $stmt1->bind_param("i", $theatre_id);
  $stmt1->execute();
  $stmt1->close();

  $stmt2 = $conn->prepare("DELETE FROM theatres WHERE id = ?");
  $stmt2->bind_param("i", $theatre_id);
  $stmt2->execute();

  if ($stmt2->affected_rows === 0) {

    throw new Exception("Theatre not found or could not be deleted.");
  }

  $stmt2->close();

  $conn->commit();

  header("Location: manage_theatres.php?deleted=1");
  exit();

} catch (Exception $e) {
  $conn->rollback();
  header("Location: manage_theatres.php?error=deletefailed");
  exit();
}
?>
