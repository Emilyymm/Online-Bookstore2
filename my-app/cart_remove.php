<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id'])) {
    header("Location: signin.php");
    exit;
}

$conn = new mysqli("localhost", "root", "", "project");

if ($conn->connect_error) {
    die("Connection Error: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $user_id = $_SESSION['user_id'];
    $book_id = intval($_POST['book_id']);

    $stmt = $conn->prepare("
        DELETE FROM cart 
        WHERE user_id = ? AND book_id = ?
    ");

    $stmt->bind_param("ii", $user_id, $book_id);
    $stmt->execute();
    $stmt->close();
}

$conn->close();

header("Location: index.php?page=cart");
exit;