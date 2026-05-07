<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$conn = new mysqli("localhost", "root", "", "project");

if ($conn->connect_error) {
    die("Connection Error: " . $conn->connect_error);
}

$error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($email) || empty($password)) {
        $error = "Please enter both email and password.";
    } else {

        $stmt = $conn->prepare("
            SELECT user_id, firstname, lastname, password, membership_id
            FROM customers
            WHERE email = ?
        ");

        if (!$stmt) {
            die("Prepare failed: " . $conn->error);
        }

        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($user = $result->fetch_assoc()) {

            if (password_verify($password, $user['password'])) {

                $_SESSION['user_id'] = $user['user_id'];
                $_SESSION['firstname'] = $user['firstname'];
                $_SESSION['lastname'] = $user['lastname'];
                $_SESSION['membership_id'] = $user['membership_id'];

                header("Location: index.php?page=home");
                exit;

            } else {
                $error = "Incorrect password.";
            }

        } else {
            $error = "No account found with that email.";
        }

        $stmt->close();
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign In - Bookstore</title>
    <link rel="stylesheet" href="styles.css">
</head>

<body>

<?php include 'header.php'; ?>

<main class="auth-container">

    <div class="auth-card">

        <h2>Sign In</h2>

        <?php if (!empty($error)): ?>
            <p class="error"><?= htmlspecialchars($error) ?></p>
        <?php endif; ?>

        <form method="POST" class="auth-form">

            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email" required>
            </div>

            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" required>
            </div>

            <button type="submit" class="btn-primary">Sign In</button>

            <p class="auth-switch">
                Don’t have an account?
                <a href="signup.php">Create one</a>
            </p>

        </form>

    </div>

</main>

<?php include 'footer.php'; ?>

</body>
</html>