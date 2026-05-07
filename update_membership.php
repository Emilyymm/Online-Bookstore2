<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: signin.php");
    exit;
}

$conn = new mysqli("localhost", "root", "", "project");

$user_id = $_SESSION['user_id'];
$membership_id = $_POST['membership_id'];

$stmt = $conn->prepare("
UPDATE customers
SET membership_id = ?
WHERE user_id = ?
");

$stmt->bind_param("ii", $membership_id, $user_id);
$stmt->execute();

header("Location: index.php?page=profile");
exit;
?>