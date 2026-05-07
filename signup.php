<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $firstname = $_POST['firstname'] ?? '';
    $lastname = $_POST['lastname'] ?? '';
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    $membership_id = $_POST['membership'] ?? '';

    if ($password !== $confirm_password) {
        $error = "Passwords do not match!";
    } elseif (!in_array($membership_id, [1, 2])) {
        $error = "Invalid membership selection!";
    } else {
        $hashed_password = password_hash($password, PASSWORD_BCRYPT);

        $conn = new mysqli("localhost", "root", "", "project");

        if ($conn->connect_error) {
            die("Connection Error: $conn->connect_error");
        }

        $stmt = $conn->prepare("SELECT * FROM customers WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $error = "Email is already registered.";
        } else {
            $stmt = $conn->prepare("INSERT INTO customers (firstname, lastname, email, password, membership_id) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("ssssi", $firstname, $lastname, $email, $hashed_password, $membership_id);
            $stmt->execute();

            $_SESSION['user_id'] = $conn->insert_id;
            $_SESSION['email'] = $email;
            $_SESSION['firstname'] = $firstname;
            $_SESSION['lastname'] = $lastname;

            header("Location: index.php?page=home");
            exit;
        }

        $stmt->close();
        $conn->close();
    }
}

$conn = new mysqli("localhost", "root", "", "project");
$membership_sql = "SELECT membership_id, name, benefits FROM memberships";
$membership_result = $conn->query($membership_sql);
$memberships = [];
if ($membership_result) {
    while ($row = $membership_result->fetch_assoc()) {
        $memberships[] = $row;
    }
    $membership_result->close();
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <?php include 'header.php'; ?>
    <main>
<main class="auth-container">

    <div class="auth-card">
        <h2>Create Account</h2>

        <?php if (isset($error)): ?>
            <p class="error"><?= htmlspecialchars($error); ?></p>
        <?php endif; ?>

        <form method="POST" class="auth-form">

            <div class="form-group">
                <label>First Name</label>
                <input type="text" name="firstname" required>
            </div>

            <div class="form-group">
                <label>Last Name</label>
                <input type="text" name="lastname" required>
            </div>

            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email" required>
            </div>

            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" required>
            </div>

            <div class="form-group">
                <label>Confirm Password</label>
                <input type="password" name="confirm_password" required>
            </div>

            <div class="form-group">
                <label>Membership</label>
                <select name="membership" required>
                    <option value="">Select Membership</option>
                    <?php foreach ($memberships as $membership): ?>
                        <option value="<?= $membership['membership_id']; ?>">
                            <?= htmlspecialchars($membership['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <button type="submit" class="btn-primary">Sign Up</button>

            <p class="auth-switch">
                Already have an account?
                <a href="signin.php">Sign In</a>
            </p>

        </form>
    </div>

</main>
    <footer>
        <?php include 'footer.php'; ?>
    </footer>
</body>
</html>