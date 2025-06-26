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
        $mailer = new PHPMailer(true);
        $mailer->SMTPDebug = 2;
        $mailer->isSMTP();
        $mailer->Host = 'smtp.laposte.net';
        $mailer->SMTPAuth = true;
        $mailer->Username = 'everspierre@laposte.net';
        $mailer->Password = 'zkg.afv6wpc6mqu2KCD';
        $mailer->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        $mailer->Port = 465;
        $mailer->setFrom('everspierre@laposte.net', 'pierreevers.fr');
        $mailer->addAddress('everspierre@gmail.com');
        $mailer->isHTML(true);
        $mailer->Subject = 'everspierre.fr - email de contact';
        $mailer->Body = sprintf('Mail envoyé par %s (%s - %s)<br><br>Message : %s', $fullname, $email, $phone, $message);
        $mailer->AltBody = sprintf('Mail envoyé par %s (%s - %s)<br><br>Message : %s', $fullname, $email, $phone, $message);
        $mailer->send();
        //$errors['errors'] = ['envoi' => 'test'];
        //$data['success'] = false;
        $data['success'] = true;
    } catch (Exception $e) {
        $errors['errors'] = ['envoi' => $e->getMessage()];
        $data['success'] = false;
    }
}

echo json_encode($data);