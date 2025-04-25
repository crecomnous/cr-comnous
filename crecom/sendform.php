<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';

$response = [];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Récupération des champs
    $firstName = htmlspecialchars($_POST['first_name']);
    $lastName = htmlspecialchars($_POST['last_name']);
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $countryCode = htmlspecialchars($_POST['country_code']); // Nouveau champ
    $contactNumber = htmlspecialchars($_POST['contact']);
    $message = htmlspecialchars($_POST['message']);

    // Construction du numéro complet
    $fullContact = $countryCode . preg_replace('/\D/', '', $contactNumber); // Supprime tout sauf les chiffres dans le numéro

    if (empty($firstName) || empty($lastName) || empty($email) || empty($contactNumber) || empty($message)) {
        $response['error'] = "Tous les champs sont requis. Veuillez remplir le formulaire correctement.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $response['error'] = "Veuillez entrer un email valide.";
    } elseif (!preg_match("/^\+\d{6,15}$/", $fullContact)) {
        $response['error'] = "Veuillez entrer un numéro de téléphone valide avec indicatif.";
    } else {
        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'creyaaye88@gmail.com';
            $mail->Password = 'psdd fipw owew hbao'; // Assure-toi que c’est un mot de passe d'application
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;
            $mail->SMTPOptions = [
                'ssl' => [
                    'verify_peer' => false,
                    'verify_peer_name' => false,
                    'allow_self_signed' => true
                ]
            ];

            $mail->setFrom($email, "$firstName $lastName");
            $mail->addAddress('creyaaye88@gmail.com', 'CRECOM');

            $mail->isHTML(false);
            $mail->Subject = 'Nouveau message de contact';
            $mail->Body = "Nom : $firstName $lastName\nEmail : $email\nContact : $fullContact\n\nMessage :\n$message";

            $mail->send();

            $response['success'] = "Merci de nous avoir contactés ! Votre message a bien été envoyé.";
        } catch (Exception $e) {
            $response['error'] = "Une erreur s'est produite. Erreur: {$mail->ErrorInfo}";
        }
    }

    echo json_encode($response);
    exit;
}
?>