<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$conn = new mysqli("localhost", "root", "", "project");

if ($conn->connect_error) {
    die("Connection Error: " . $conn->connect_error);
}

if (!isset($_GET['id'])) {
    echo "<p class='error-msg'>Book not found.</p>";
    exit;
}

$id = intval($_GET['id']);

$stmt = $conn->prepare("SELECT * FROM books WHERE book_id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "<p class='error-msg'>Book not found.</p>";
    exit;
}

$book = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($book['title']) ?></title>
    <link rel="stylesheet" href="styles.css">
</head>

<body>

<?php include 'header.php'; ?>

<main class="book-detail-page">

    <div class="book-detail-card">

        <div class="book-detail-image">
            <img src="images/<?= htmlspecialchars($book['image_url']) ?>" 
                 alt="<?= htmlspecialchars($book['title']) ?>">
        </div>

        <div class="book-detail-info">

            <h1><?= htmlspecialchars($book['title']) ?></h1>

            <?php if (!empty($book['author'])): ?>
                <p class="author">By <?= htmlspecialchars($book['author']) ?></p>
            <?php endif; ?>

            <p class="price">$<?= number_format($book['price'], 2) ?></p>

            <p class="stock">
                <?= ($book['stock'] > 0) ? "In Stock" : "Out of Stock" ?>
            </p>

            <?php if ($book['stock'] > 0): ?>
                <form method="POST" action="cart_add.php">
                    <input type="hidden" name="book_id" value="<?= $book['book_id'] ?>">
                    <button type="submit" class="btn-primary">Add to Cart</button>
                </form>
            <?php else: ?>
                <p class="out-of-stock">Out of Stock</p>
            <?php endif; ?>

        </div>

    </div>

    <div class="book-description-box">
        <h3>Description</h3>
        <p><?= htmlspecialchars($book['description']) ?></p>
    </div>

</main>

<?php include 'footer.php'; ?>

</body>
</html>