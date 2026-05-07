<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id'])) {
    header("Location: signin.php");
    exit;
}

$conn = new mysqli("localhost", "root", "", "project");

$user_id = $_SESSION['user_id'];

/* Get user info */
$stmt = $conn->prepare("
SELECT firstname, lastname, membership_id
FROM customers
WHERE user_id = ?
");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

/* Get membership */
$membership = "Unknown";

$ms = $conn->prepare("
SELECT name FROM memberships WHERE membership_id = ?
");
$ms->bind_param("i", $user['membership_id']);
$ms->execute();
$res = $ms->get_result();

if ($row = $res->fetch_assoc()) {
    $membership = $row['name'];
}
?>

<main class="profile-page">

    <div class="profile-card">

        <h2>Your Profile</h2>

        <div class="profile-info">
            <p><strong>Name:</strong> <?= htmlspecialchars($user['firstname'] . " " . $user['lastname']) ?></p>
            <p><strong>Membership:</strong> <?= htmlspecialchars($membership) ?></p>
        </div>

        <hr>

        <h3>Manage Membership</h3>

        <form action="update_membership.php" method="POST" class="membership-form">

            <select name="membership_id">
                <?php
                $result = $conn->query("SELECT * FROM memberships");
                while ($m = $result->fetch_assoc()):
                ?>
                    <option value="<?= $m['membership_id'] ?>">
                        <?= htmlspecialchars($m['name']) ?>
                    </option>
                <?php endwhile; ?>
            </select>

            <button type="submit" class="btn">Update Membership</button>
        </form>

        <hr>

        <a href="logout.php" class="logout-btn">Sign Out</a>

    </div>

</main>