<?php include 'db.php'; ?>

<h2>URLs surveillées</h2>
<ul>
<?php
foreach ($db->query("SELECT * FROM urls") as $row) {
    echo "<li>{$row['url']} – Chaque {$row['interval_min']} min</li>";
}
?>
</ul>
<a href="add_url.php">Ajouter une URL</a>

<h2>Emails enregistrés</h2>
<ul>
<?php
foreach ($db->query("SELECT * FROM emails") as $row) {
    echo "<li>{$row['email']}</li>";
}
?>
</ul>
<a href="add_email.php">Ajouter un email</a>

<h2>Journal des événements</h2>
<pre><?php echo file_exists("log.txt") ? file_get_contents("log.txt") : "Aucun événement enregistré."; ?></pre>
