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
SELECT 
    books.book_id,
    books.title,
    books.author,
    books.price,
    books.image_url,
    cart.quantity
FROM cart
JOIN books ON cart.book_id = books.book_id
WHERE cart.user_id = ?
");

$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$total = 0;
?>

<main class="cart-page">

    <h2>Your Cart</h2>

    <div class="cart-grid">

        <?php if ($result->num_rows > 0): ?>

            <?php while ($row = $result->fetch_assoc()): ?>

                <?php $subtotal = $row['price'] * $row['quantity']; ?>
                <?php $total += $subtotal; ?>

                <div class="cart-card">

                    <img src="images/<?= htmlspecialchars($row['image_url']) ?>">

                    <div class="cart-info">

                        <h3><?= htmlspecialchars($row['title']) ?></h3>
                        <p class="author"><?= htmlspecialchars($row['author']) ?></p>
                        <p>$<?= number_format($row['price'], 2) ?></p>

                        <div class="qty">
                            Quantity: <?= $row['quantity'] ?>
                        </div>

                        <p class="subtotal">
                            Subtotal: $<?= number_format($subtotal, 2) ?>
                        </p>

                        <!-- REMOVE BUTTON -->
                        <form method="POST" action="cart_remove.php">
                            <input type="hidden" name="book_id" value="<?= $row['book_id'] ?>">
                            <button type="submit" class="remove-btn">Remove</button>
                        </form>

                    </div>

                </div>

            <?php endwhile; ?>

        <?php else: ?>
            <p class="empty-cart">Your cart is empty.</p>
        <?php endif; ?>

    </div>

    <?php if ($total > 0): ?>
        <div class="cart-summary">
            <h3>Total: $<?= number_format($total, 2) ?></h3>
            <form method="POST" action="checkout.php">
                <button class="checkout-btn">Proceed to Checkout</button>
            </form>
        </div>
    <?php endif; ?>

    <?php if (isset($_GET['success'])): ?>
    <p class="success-msg">Order placed successfully!</p>
<?php endif; ?>

</main>