<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/SMTP.php';

require "config.php";

if($_SERVER["REQUEST_METHOD"] == "POST") {
    if(isset($_POST['message'])) {
        $msg = $_POST['message'];
        $recepient_email = $_POST['recepemail'];
        $recepient_phone = $_POST['recepphone'];

        $sbj = "Encrypted Message";
        $message_body = $recepient_phone ."  ". $msg;
        send_email($recepient_email, $sbj, $message_body, $message_body);
        echo json_encode($message_body); //confirms success
    }
}


/********************************** */
function send_email($user, $subject, $body, $altbody) {

    $mail = new PHPMailer(true);
    $mail->isSMTP();
    $mail->Host = Config::SMTP_HOST;
    $mail->SMTPAuth   = true;
    $mail->Username   = Config::SMTP_USER;
    $mail->Password   = Config::SMTP_PASSWORD;
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;

    $mail->setFrom('projects@otanga.co.ke', 'Des Messages');
    $mail->addAddress($user);

    $mail->isHTML(true);
    $mail->Subject = $subject;
    $mail->Body = $body;
    $mail->Altbody = $altbody;

    $mail->send();

}