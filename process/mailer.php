<?php

use PHPMailer\PHPMailer\PHPMailer;
use PhpMailer\PHPMailer\Exception;

require 'vendor/autoload.php';

/**
 * Vérification des champs
 */
$errors = [];
$data = [];
$fullname = htmlspecialchars($_POST['inputFullname']);
$email = htmlspecialchars($_POST['inputEmail']);
$phone = htmlspecialchars($_POST['inputPhone']);
$message = htmlspecialchars($_POST['inputMessage']);

if (empty($fullname)) {
    $errors['inputFullname'] = 'Le champs doit être renseigné';
}

if (empty($email)) {
    $errors['inputEmail'] = 'Le champs doit être renseigné';
}

if (empty($message)) {
    $errors['inputMessage'] = 'Le champs doit être renseigné';
}

if (!empty($errors)) {
    $data['success'] = false;
    $data['errors'] = $errors;
} else {
    $data['success'] = true;
    $data['errors'] = [];
}

/**
 * Configuration et envoi du mail
 */
if ($data['success']) {
    try {
        $mailer = new PHPMailer();
        $mailer->SMTPDebug = 0;
        $mailer->isSMTP();
        $mailer->Host = 'smtp.laposte.net';
        $mailer->SMTPAuth = true;
        $mailer->Username = '****';
        $mailer->Password = '****';
        $mailer->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        $mailer->Port = 465;
        $mailer->setFrom('***', 'pierreevers.fr');
        $mailer->addAddress('everspierre@gmail.com');
        $data['success'] = true;
        $mailer->isHTML();
        $mailer->Subject = 'everspierre.fr - email de contact';
        $mailer->Body = sprintf('Mail envoyé par %s (%s - %s)<br><br>%s', $fullname, $email, $phone, $message);
        $mailer->AltBody = sprintf('Mail envoyé par %s (%s - %s)<br><br>%s', $fullname, $email, $phone, $message);
        $mailer->send();
        $data['success'] = true;

    } catch (Exception $e) {
        $data['success'] = false;
        $data['errors']['envoi'] = $e->getMessage();
    }
}

echo json_encode($data);