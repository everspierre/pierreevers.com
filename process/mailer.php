<?php

use PHPMailer\PHPMailer\PHPMailer;
use PhpMailer\PHPMailer\Exception;
use Dotenv\Dotenv;

require 'vendor/autoload.php';

/**
 * Classe permettant de vérifier le google recaptcha.
 */
class reCaptcha {

    /**
     * Constructeur
     * 
     * @param string $secret
     */
    function __construct(private string $secret) {}

    /**
     * Verifie le code.
     * 
     * @param string|null $code
     * 
     * @return boolean
     */
    function checkCode(?string $code): bool {

        if (empty($code)) {
            return false;
        }

        $url = "https://www.google.com/recaptcha/api/siteverify?secret={$this->secret}&response={$code}";

        if (function_exists("curl_version")) {
            $curl = curl_init($url);
            curl_setopt($curl, CURLOPT_HEADER, false);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl, CURLOPT_TIMEOUT, 1);
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
            $response = curl_exec($curl);
        } else {
            $response = file_get_contents($url);
        }

        if (empty($response) || is_null($response)) {
            return false;
        }

        $json = json_decode($response);

        return $json->success;
    }

}

// Chargement des données sécurisée
$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

/**
 * Vérification des champs
 */
$errors = [];
$data = [];
$fullname = htmlspecialchars($_POST['inputFullname']);
$email = htmlspecialchars($_POST['inputEmail']);
$phone = htmlspecialchars($_POST['inputPhone']);
$message = htmlspecialchars($_POST['inputMessage']);
$code = $_POST['responseCode'];

if (empty($fullname)) {
    $errors['inputFullname'] = 'Le champs doit être renseigné';
}

if (empty($email)) {
    $errors['inputEmail'] = 'Le champs doit être renseigné';
}

if (empty($message)) {
    $errors['inputMessage'] = 'Le champs doit être renseigné';
}

/**
 * Vérification du reCaptcha
 */
$recaptcha = new reCaptcha($_ENV['GOOGLE_RECAPTCHA_SECRET']);

if (!$recaptcha->checkCode($code)) {
    $errors['envoi'] = "Veuillez confirmer la case à cocher «Je ne suis pas un robot»";
}

/**
 * Vérification des erreurs
 */
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