<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>

<nav class="navbar">

    <a href="index.php?page=home">Home</a>
    <a href="index.php?page=store">Books</a>
    <a href="index.php?page=cart">Cart</a>

    <?php if (isset($_SESSION['user_id'])): ?>
        <a href="index.php?page=profile">Profile</a>
    <?php else: ?>
        <a href="signin.php">Sign In</a>
    <?php endif; ?>

</nav>