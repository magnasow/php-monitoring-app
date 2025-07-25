<?php
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $stmt = $db->prepare("INSERT INTO emails (email) VALUES (?)");
    $stmt->execute([$email]);
    header("Location: index.php");
    exit;
}
?>

<form method="POST">
    Email: <input type="email" name="email" required>
    <button type="submit">Ajouter</button>
</form>
