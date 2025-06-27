<?php

use PHPMailer\PHPMailer\PHPMailer;
use PhpMailer\PHPMailer\Exception;
use Dotenv\Dotenv;

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
    // Chargement des données sécurisée
    $dotenv = Dotenv::createImmutable(__DIR__);
    $dotenv->load();

    // Paramétrage et envoi du mail
    try {
        $mailer = new PHPMailer();
        $mailer->SMTPDebug = 0;
        $mailer->isSMTP();
        $mailer->Host = $_ENV['SMTP_HOST'];
        $mailer->SMTPAuth = true;
        $mailer->Username = $_ENV['SMTP_USER'];
        $mailer->Password = $_ENV['SMTP_PASSWORD'];
        $mailer->SMTPSecure = $_ENV['SMTP_ENCRYPTION'];
        $mailer->Port = $_ENV['SMTP_PORT'];
        $mailer->setFrom($_ENV['SMTP_USER'], 'pierreevers.fr');
        $mailer->addAddress($_ENV['SMTP_TO']);
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