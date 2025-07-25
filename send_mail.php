<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';

function send_mail($to, $subject, $message) {
    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'sowmarieta013@gmail.com';
        $mail->Password   = 'ixrb bmqa zikb heij'; // mot de passe d'application Gmail
        $mail->SMTPSecure = 'tls';
        $mail->Port       = 587;

        $mail->setFrom('sowmarieta013@gmail.com', 'Mariéta Sow');
        $mail->addAddress($to);
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body    = $message;
        $mail->AltBody = strip_tags($message);
        $mail->send();

        return true;
    } catch (Exception $e) {
        error_log("Erreur mail à $to : " . $mail->ErrorInfo);
        return false;
    }
}
?>
