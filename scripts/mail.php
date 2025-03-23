<?php
/*  !!!OBS!!! Ludvig, för att testa Kontaktformuläret så har jag en live preview på min hemsida
https://marcuslehm.se/nightcitywarez/contact.html där jag har en fungerande webbserver som är konfiguerad för att hantera PHP */

// Anger att det är JSON som skickas tillbaka
header('Content-Type: application/json');

// Hämtar och validerar den inskickade datan, samt anger ett nullvärde om mot förmodan något skulle vara tomt
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $firstName = htmlspecialchars($_POST['firstName'] ?? '');
    $lastName = htmlspecialchars($_POST['lastName'] ?? '');
    $email = htmlspecialchars($_POST['email'] ?? '');
    $subject = htmlspecialchars($_POST['subject'] ?? '');
    $message = htmlspecialchars($_POST['message'] ?? '');

    /* Kontrollera att alla fält är ifyllda, hängslen och livrem här iom att html formuläret inte tillåter tomma fält
    men man kan aldrig vara säker*/
    if (empty($firstName) || empty($lastName) || empty($email) || empty($subject) || empty($message)) {
        echo json_encode(["status" => "error", "message" => "Alla fält måste fyllas i!"]);
        exit;
    }
    
    // Mottagarens e-postadress, måste ange min domän här för att det ska fungera med min webbserver
    $to = "kontakt@marcuslehm.se";
    // Avsändaradressen som visas för mottagaren, måste ange min domän även här för att det ska fungera med min webbserver
    $headers = "From: kontakt@marcuslehm.se\r\n";
    // Anger vilken e-postadress som ska användas om mottagaren (Vilket är jag i detta fall) klickar på "Svara" i sin e-postklient.
    $headers .= "Reply-To: $email\r\n";
    // Anger att innehållet i meddelandet är formaterat som HTML
    $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
     // E-postmeddelandets ämne
    $email_subject = "Kontaktformulärförfråga: $subject";
    // E-postmeddelandets innehåll
    $email_body = "
<html>
<head>
    <title>Ny Förfrågan Via Kontaktformulär</title>
</head>
<body>
    <h2>$firstName har precis fyllt i ett kontaktformulär på sidan med ämnet: '$subject'</h2>
    <p><strong>Förnamn:</strong> $firstName</p>
    <p><strong>Efternamn:</strong> $lastName</p>
    <p><strong>E-post:</strong> $email</p>
    <p><strong>Ämne:</strong> $subject</p>
    <p><strong>Meddelande:</strong></p>
    <p>$message</p>
</body>
</html>
";
    // Skicka e-post meddelandet och hantera resultatet
    if (mail($to, $email_subject, $email_body, $headers)) {
        echo json_encode(["status" => "success", "message" => "Tack för ditt meddelande! Vi återkommer så snart vi kan!"]);
    } else {
        error_log("Meddelandet kunde inte skickas! Vänligen försök igen");
        echo json_encode(["status" => "error", "message" => "Något gick fel! Vänligen försök igen senare"]);
    }
} else {
    echo json_encode(["status" => "error", "message" => "Något gick fel! Vänligen försök igen senare"]);
}
?>