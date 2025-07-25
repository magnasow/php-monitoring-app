<?php
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $url = $_POST['url'];
    $interval = $_POST['interval'] ?? 5;
    $stmt = $db->prepare("INSERT INTO urls (url, interval_min) VALUES (?, ?)");
    $stmt->execute([$url, $interval]);
    header("Location: index.php");
    exit;
}
?>

<form method="POST">
    URL: <input type="text" name="url" required>
    Interval (minutes): <input type="number" name="interval" value="5" min="1">
    <button type="submit">Ajouter</button>
</form>
