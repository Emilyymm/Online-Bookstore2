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

$stmt = $conn->prepare("
    SELECT cart.book_id, cart.quantity, books.stock, books.price
    FROM cart
    JOIN books ON cart.book_id = books.book_id
    WHERE cart.user_id = ?
");

if (!$stmt) {
    die("Prepare failed (cart fetch): " . $conn->error);
}

$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("Cart is empty.");
}

$conn->begin_transaction();

$total = 0;

try {
    while ($row = $result->fetch_assoc()) {

        if ($row['stock'] < $row['quantity']) {
            throw new Exception("Not enough stock for a book.");
        }

        $update = $conn->prepare("
            UPDATE books 
            SET stock = stock - ? 
            WHERE book_id = ?
        ");

        if (!$update) {
            throw new Exception("Prepare failed (update stock): " . $conn->error);
        }

        $update->bind_param("ii", $row['quantity'], $row['book_id']);
        $update->execute();

        $total += $row['price'] * $row['quantity'];
    }

    $order = $conn->prepare("
        INSERT INTO orders (user_id, total_price) 
        VALUES (?, ?)
    ");

    if (!$order) {
        throw new Exception("Prepare failed (orders): " . $conn->error);
    }

    $order->bind_param("id", $user_id, $total);
    $order->execute();

    $order_id = $conn->insert_id;

    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {

        $item = $conn->prepare("
            INSERT INTO order_items (order_id, book_id, quantity)
            VALUES (?, ?, ?)
        ");

        if (!$item) {
            throw new Exception("Prepare failed (order_items): " . $conn->error);
        }

        $item->bind_param("iii", $order_id, $row['book_id'], $row['quantity']);
        $item->execute();
    }

    $clear = $conn->prepare("DELETE FROM cart WHERE user_id = ?");

    if (!$clear) {
        throw new Exception("Prepare failed (clear cart): " . $conn->error);
    }

    $clear->bind_param("i", $user_id);
    $clear->execute();

    $conn->commit();

    header("Location: index.php?page=cart&success=1");
    exit;

} catch (Exception $e) {

    $conn->rollback();
    die("Checkout failed: " . $e->getMessage());
}
?>