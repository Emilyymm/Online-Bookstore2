<?php
session_start();

$allowed_pages = [
    'home' => 'home.php',
    'store' => 'store.php',
    'cart' => 'cart.php',
    'profile' => 'profile.php'
];

$page = $_GET['page'] ?? 'home';

$page = preg_replace('/[^a-zA-Z0-9_-]/', '', $page);

if (!isset($allowed_pages[$page]) || !file_exists($allowed_pages[$page])) {
    $page = 'home';
}

$page_file = $allowed_pages[$page];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Online Bookstore</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>

<?php include 'header.php'; ?>

<main>
    <?php include $page_file; ?>
</main>

<?php include 'footer.php'; ?>

</body>
</html>