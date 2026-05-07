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

$user_id = $_SESSION['user_id'];
$book_id = $_POST['book_id'] ?? 0;

if (!$book_id) {
    die("Invalid book");
}

$stockStmt = $conn->prepare("SELECT stock FROM books WHERE book_id = ?");
$stockStmt->bind_param("i", $book_id);
$stockStmt->execute();
$stockResult = $stockStmt->get_result();

if ($stockResult->num_rows === 0) {
    die("Book not found.");
}

$book = $stockResult->fetch_assoc();

if ($book['stock'] <= 0) {
    die("This book is out of stock.");
}

$check = $conn->prepare("SELECT quantity FROM cart WHERE user_id = ? AND book_id = ?");
$check->bind_param("ii", $user_id, $book_id);
$check->execute();
$result = $check->get_result();

if ($row = $result->fetch_assoc()) {

    $newQty = $row['quantity'] + 1;

    if ($newQty > $book['stock']) {
        die("Cannot add more than available stock.");
    }

    $update = $conn->prepare("
        UPDATE cart 
        SET quantity = ? 
        WHERE user_id = ? AND book_id = ?
    ");
    $update->bind_param("iii", $newQty, $user_id, $book_id);
    $update->execute();

} else {

    // Adding first time (quantity = 1 is safe because stock > 0 already checked)
    $insert = $conn->prepare("
        INSERT INTO cart (user_id, book_id, quantity) 
        VALUES (?, ?, 1)
    ");
    $insert->bind_param("ii", $user_id, $book_id);
    $insert->execute();
}

header("Location: index.php?page=cart");
exit;
?>