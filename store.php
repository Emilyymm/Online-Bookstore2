<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$conn = new mysqli("localhost", "root", "", "project");

if ($conn->connect_error) {
    die("Connection Error: " . $conn->connect_error);
}

$selected_category = $_GET['category'] ?? '';

$category_result = $conn->query("SELECT DISTINCT genre FROM books ORDER BY genre");

$search = $_GET['search'] ?? '';

$sql = "SELECT book_id, title, author, price, stock, image_url, genre FROM books WHERE 1=1";

$params = [];
$types = "";

if (!empty($selected_category)) {
    $sql .= " AND genre = ?";
    $params[] = $selected_category;
    $types .= "s";
}

if (!empty($search)) {
    $sql .= " AND (title LIKE ? OR author LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
    $types .= "ss";
}

$sql .= " ORDER BY title";



$stmt = $conn->prepare($sql);

if (!$stmt) {
    die("Prepare failed: " . $conn->error);
}

if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}

$stmt->execute();
$result = $stmt->get_result();

?>

<h2>Browse Books</h2>

<section class="store-layout">

<aside class="category-box">
    <h3>Categories</h3>
    <ul>
        <li>
            <a href="index.php?page=store" <?= !$selected_category ? "class='active'" : "" ?>>
                All Books
            </a>
        </li>

        <?php while ($category = $category_result->fetch_assoc()): 
            $cat = htmlspecialchars($category['genre']);
            $active = ($selected_category === $cat) ? "class='active'" : "";
        ?>
            <li>
                <a href="index.php?page=store&category=<?= urlencode($cat) ?>" <?= $active ?>>
                    <?= $cat ?>
                </a>
            </li>
        <?php endwhile; ?>
    </ul>
</aside>

<div class="book-grid">
<?php while ($row = $result->fetch_assoc()): ?>

<a class="book-card"
   href="book.php?id=<?= $row['book_id'] ?>">

    <div class="book-image">
        <img src="images/<?= htmlspecialchars($row['image_url'] ?? 'placeholder.jpg') ?>"
             alt="<?= htmlspecialchars($row['title']) ?>">
    </div>

    <div class="book-info">
        <h4><?= htmlspecialchars($row['title']) ?></h4>

        <p class="price">$<?= number_format($row['price'], 2) ?></p>

        <p class="stock">
            <?= (!empty($row['stock']) && $row['stock'] > 0)
                ? "In Stock: " . intval($row['stock'])
                : "Out of Stock"; ?>
        </p>
    </div>

</a>

<?php endwhile; ?>
</div>

</section>

<?php
$stmt->close();
$conn->close();
?>