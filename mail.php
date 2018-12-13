<?php
include_once('corsHeaders.php');
include_once('dbAccess.php');
// Import PHPMailer classes into the global namespace
// These must be at the top of your script, not inside a function
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

//Load Composer's autoloader
require 'vendor/autoload.php';

$mail = new PHPMailer(true);                              // Passing `true` enables exceptions

//search for images in DB table and attache them
$takenLoadId = $_POST['takenLoadId'];

try {
    //Server settings
    $mail->SMTPDebug = 2;                                 // Enable verbose debug output
    $mail->isSMTP();                                      // Set mailer to use SMTP
    $mail->Host = 'srv3.artplus.pl';                      // Specify main and backup SMTP servers
    $mail->SMTPAuth = true;                               // Enable SMTP authentication
    $mail->Username = 'krzysztof.baca@artplus.pl';        // SMTP username
    $mail->Password = '%DLd4!(&?k$A';                     // SMTP password
    $mail->SMTPSecure = 'tls';                            // Enable TLS encryption, `ssl` also accepted
    $mail->Port = 587;                                    // TCP port to connect to
    $mail->CharSet = 'UTF-8';

    $pdo = new PDO('mysql:host='.$host.';dbname='.$database.';port='.$port, $usernameDB, $passwordDB);

    $data = $pdo->prepare('
        SELECT email_canonical as email FROM route
        JOIN (SELECT id, email_canonical FROM fos_user) AS fuser ON fuser.id = route.user_id
        WHERE route_id = :takenLoadId
        ');
    $data->bindParam(':takenLoadId', $takenLoadId, PDO::PARAM_STR, 12);
    $data->execute();
    $data = $data->fetchAll();
    $email_address = $data[0]['email'];
    //Recipients
    $mail->setFrom('super@admin.pl', 'Magda Trans');

    // change to real admin of this route after testes
    //$mail->addAddress($data['email']);
    $mail->addAddress('krzysztof.baca@artplus.pl');


    //Attachments
    $attachments = $pdo->prepare('SELECT document_name FROM related_documents WHERE load_id = :takenLoadId');
    $attachments->bindParam(':takenLoadId', $takenLoadId, PDO::PARAM_STR, 12);
    $attachments->execute();
    $attachments = $attachments->fetchAll();
    if($attachments){
        foreach($attachments as $attachment){
            $mail->addAttachment("uploadedFiles/$takenLoadId/{$attachment['document_name']}");
        }
    }

    //Content
    $mail->isHTML(true);                                  // Set email format to HTML
    $mail->Subject = 'Dokumenty trasy ID '.$takenLoadId;
    $mail->Body    = 'This is the HTML message body <b>in bold!</b>';
    $mail->AltBody = 'This is the body in plain text for non-HTML mail clients.';

    $mail->send();
    echo 'Message has been sent';
} catch (Exception $e) {
    echo 'Message could not be sent. Mailer Error: ', $mail->ErrorInfo;
}