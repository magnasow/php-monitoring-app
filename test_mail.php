<?php
require 'send_mail.php';

if (send_mail('sowmarieta013@gmail.com', 'Test', 'Ceci est un test.')) {
    echo "Email envoyé avec succès !";
} else {
    echo "Échec d'envoi de l'email.";
}
?>
