<?php
// Configuration
$to_email = "contact@scordesigner.com"; // REMPLACEZ par votre vrai email
$subject = "Nouveau message via ScorDesigner.com";

// Protection contre le spam
header('Content-Type: application/json');

// Vérifier que c'est une requête POST
if ($_SERVER["REQUEST_METHOD"] != "POST") {
    http_response_code(405);
    echo json_encode(["success" => false, "message" => "Méthode non autorisée"]);
    exit;
}

// Récupérer et nettoyer les données
$name = isset($_POST['name']) ? strip_tags(trim($_POST['name'])) : '';
$email = isset($_POST['email']) ? filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL) : '';
$phone = isset($_POST['phone']) ? strip_tags(trim($_POST['phone'])) : '';
$message = isset($_POST['message']) ? strip_tags(trim($_POST['message'])) : '';

// Validation
$errors = [];

if (empty($name)) {
    $errors[] = "Le nom est requis.";
}

if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = "Un email valide est requis.";
}

if (empty($message)) {
    $errors[] = "Le message est requis.";
}

// Si erreurs, renvoyer
if (!empty($errors)) {
    http_response_code(400);
    echo json_encode(["success" => false, "message" => implode(" ", $errors)]);
    exit;
}

// Protection anti-spam basique (honeypot et rate limiting)
session_start();
$current_time = time();

// Vérifier si l'utilisateur a déjà soumis récemment (rate limiting)
if (isset($_SESSION['last_submission']) && ($current_time - $_SESSION['last_submission']) < 60) {
    http_response_code(429);
    echo json_encode(["success" => false, "message" => "Veuillez attendre avant d'envoyer un autre message."]);
    exit;
}

// Date et heure
$date_time = date('d/m/Y à H:i');

// Construire l'email HTML
$email_content = '
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nouveau message</title>
</head>
<body style="background-color: #FFF; font-family: roboto, arial, sans-serif; font-size:16px;">
<div class="sdcontainer" style="width:500px; background-color: #fff;padding: 2em;margin: 2em auto;border-radius: 7px; border:1px solid #eee; box-shadow: -5px 5px 5px rgba(0, 0, 0, 0.05);">
    <div class="sdintro" style="margin: 0 auto; text-align: center;">
        <img src="http://scordesigner.com/logo-email.jpg" alt="scordesigner" width="100">
        <p style="margin: 0 0 20px 0; color: #64748B; font-size: 14px; margin-top:2em">📅 Reçu le ' . $date_time . '</p>
    </div>
    <div class="sdcontact" style="background-color: rgba(0,0,0,0.03); padding: 2em; margin: 2em auto;border-radius: 7px;color:#000; border: 1px solid #eee;">
        <p style="margin: 10px 0;"><b>Nom:</b> ' . htmlspecialchars($name) . '</p>
        <p style="margin: 10px 0;"><b>Email:</b> <a href="mailto:' . htmlspecialchars($email) . '" style="color: #3B82F6; text-decoration: none;">' . htmlspecialchars($email) . '</a></p>';

if (!empty($phone)) {
    $email_content .= '
        <p style="margin: 10px 0;"><b>Tél:</b> <a href="tel:' . htmlspecialchars($phone) . '" style="color: #3B82F6; text-decoration: none;">' . htmlspecialchars($phone) . '</a></p>';
}

$email_content .= '
    </div>
    <div class="sdmessage" style="padding: 1em 0;">
        <p style="font-weight: bold; margin-bottom: 10px;">Message:</p>
        <p style="color: #333; line-height: 1.6; white-space: pre-wrap;">' . nl2br(htmlspecialchars($message)) . '</p>
    </div>
</div>
<p align="center" style="text-align: center; color: #111; font-size: 0.75em; margin-top: 2em;">scordesigner.com</p>
</body>
</html>
';

// Headers de l'email pour HTML
$headers = "From: ScorDesigner <no-reply@scordesigner.com>\r\n";
$headers .= "Reply-To: $email\r\n";
$headers .= "MIME-Version: 1.0\r\n";
$headers .= "Content-Type: text/html; charset=UTF-8\r\n";

// Envoyer l'email
if (mail($to_email, $subject, $email_content, $headers)) {
    $_SESSION['last_submission'] = $current_time;
    http_response_code(200);
    echo json_encode(["success" => true, "message" => "Merci ! Votre message a été envoyé avec succès. Nous vous répondrons sous 24h."]);
} else {
    http_response_code(500);
    echo json_encode(["success" => false, "message" => "Erreur lors de l'envoi. Veuillez réessayer."]);
}
?>