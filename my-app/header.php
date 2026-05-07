<header class="main-header">

    <div class="logo">
        <h1>Online Bookstore</h1>
    </div>

    <div class="search-bar">
        <form method="GET" action="index.php">
            <input type="hidden" name="page" value="store">
            <input 
                type="search" 
                name="search" 
                placeholder="Search books, authors, genres..."
                value="<?= htmlspecialchars($_GET['search'] ?? '') ?>"
            >
            <button type="submit">Search</button>
        </form>
    </div>

    <nav class="nav-links">
        <?php include 'nav.php'; ?>
    </nav>

</header>