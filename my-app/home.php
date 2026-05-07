<?php
$conn = new mysqli("localhost", "root", "", "project");

if ($conn->connect_error) {
    die("Connection Error: " . $conn->connect_error);
}

// Recently added (random)
$recent = $conn->query("
    SELECT * FROM books 
    ORDER BY created_at DESC 
    LIMIT 4
");

// Fiction
$fiction = $conn->prepare("
    SELECT * FROM books 
    WHERE genre = 'Fiction' 
    ORDER BY RAND() 
    LIMIT 4
");
$fiction->execute();
$fiction_result = $fiction->get_result();

// Technology
$tech = $conn->prepare("
    SELECT * FROM books 
    WHERE genre = 'Technology' 
    ORDER BY RAND() 
    LIMIT 4
");
$tech->execute();
$tech_result = $tech->get_result();
?>

<h1>Welcome to the Online Bookstore</h1>
<p>Feel free to view and check out books here</p>

<div class="banner">
    <a href="index.php?page=store">
        <img src="images/BannerPromo.jpg" alt="Bookstore">
    </a>
</div>

<!-- RECENT -->
<h2 class="section-title">Recently Added</h2>

<div class="home-row">
<?php while ($row = $recent->fetch_assoc()): ?>
    <a class="home-book" href="book.php?id=<?= $row['book_id'] ?>">
        <img src="images/<?= htmlspecialchars($row['image_url']) ?>"
             onerror="this.src='images/placeholder.jpg'">
        <p><?= htmlspecialchars($row['title']) ?></p>
    </a>
<?php endwhile; ?>
</div>

<!-- FICTION -->
<h2 class="section-title">Fiction</h2>

<div class="home-row">
<?php while ($row = $fiction_result->fetch_assoc()): ?>
    <a class="home-book" href="book.php?id=<?= $row['book_id'] ?>">
        <img src="images/<?= htmlspecialchars($row['image_url']) ?>">
        <p><?= htmlspecialchars($row['title']) ?></p>
    </a>
<?php endwhile; ?>
</div>

<!-- TECHNOLOGY -->
<h2 class="section-title">Technology</h2>

<div class="home-row">
<?php while ($row = $tech_result->fetch_assoc()): ?>
    <a class="home-book" href="book.php?id=<?= $row['book_id'] ?>">
        <img src="images/<?= htmlspecialchars($row['image_url']) ?>">
        <p><?= htmlspecialchars($row['title']) ?></p>
    </a>
<?php endwhile; ?>
</div>

<h3 class="browse-more">Check out the Books tab for a wider selection</h3>

<?php $conn->close(); ?>