<?php
include 'db.php';
include 'send_mail.php';

// Log pour vérifier que le script est exécuté
file_put_contents("cron_run.log", "[" . date('Y-m-d H:i:s') . "] cron.php lancé\n", FILE_APPEND);

// Fonction pour récupérer le code HTTP et le contenu en suivant les redirections, avec cURL
function get_http_response_code_and_content($url) {
    $ch = curl_init($url);

    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true); // suivre les redirections
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (compatible; CustomMonitor/1.0)');
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);

    $content = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    curl_close($ch);

    return [$httpCode, $content];
}

// Fonction pour notifier tous les emails enregistrés
function notify_all($subject, $message) {
    global $db;
    file_put_contents("cron_run.log", "notify_all() appelé\n", FILE_APPEND);

    $emails = $db->query("SELECT email FROM emails")->fetchAll(PDO::FETCH_COLUMN);
    foreach ($emails as $email) {
        $success = send_mail($email, $subject, $message);
        file_put_contents("cron_run.log", "Envoi à $email : " . ($success ? "SUCCÈS" : "ÉCHEC") . "\n", FILE_APPEND);
    }
}

$now = time();
$urls = $db->query("SELECT * FROM urls");

foreach ($urls as $url) {
    $last = $url['last_checked'];
    $interval = $url['interval_min'] * 60;

    if ($now - $last >= $interval) {
        $urlToCheck = $url['url'];
        list($httpCode, $content) = get_http_response_code_and_content($urlToCheck);

        $logEntry = date('Y-m-d H:i:s') . " | ";

        if ($content !== false && $httpCode >= 200 && $httpCode < 400) {
            $logEntry .= "[OK $httpCode] {$urlToCheck}";
        } else {
            $logEntry .= "[FAIL $httpCode] {$urlToCheck}";
            $subject = "🚨 Alerte Serveur";
            $message = "Le serveur {$urlToCheck} a échoué avec le code HTTP $httpCode.";
            notify_all($subject, $message);
            file_put_contents("cron_run.log", "Erreur détectée → Envoi d'alerte : $urlToCheck\n", FILE_APPEND);
        }

        // Écriture dans le journal principal
        file_put_contents('log.txt', $logEntry . PHP_EOL, FILE_APPEND);

        // Mise à jour de la date du dernier check
        $maxAttempts = 3;
        $attempt = 0;
        $success = false;
        do {
            try {
                $stmt = $db->prepare("UPDATE urls SET last_checked = ? WHERE id = ?");
                $stmt->execute([$now, $url['id']]);
                $success = true;
            } catch (PDOException $e) {
                if ($e->getCode() === 'HY000') {
                    $attempt++;
                    sleep(1);
                } else {
                    throw $e;
                }
            }
        } while (!$success && $attempt < $maxAttempts);
    }
}
