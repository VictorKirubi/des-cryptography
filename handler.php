<?php

ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/SMTP.php';

require "config.php";

//This is an API that receives encryption and decryption requests from front end

if($_SERVER["REQUEST_METHOD"] == "POST") {
    //Encrypt and send message if API request received
    if(isset($_POST['message'])) {
        $msg = $_POST['message'];
        $recepient_email = $_POST['recepemail'];
        $recepient_phone = $_POST['recepphone'];

        $sbj = "Encrypted Message";
        $cipher = "AES-128-CTR";
        $key = bin2hex(openssl_random_pseudo_bytes(28));
        $ivlen = openssl_cipher_iv_length($cipher);
        $iv = bin2hex(openssl_random_pseudo_bytes($ivlen/2));

        $m = threeDesEncrypt($msg, $key, $iv);

        $message_body = "Phone: ". $recepient_phone ." Encrypted Message: ". $m;
        send_email($recepient_email, $sbj, $message_body, $message_body);

        $smsmsg = "key: $key iv code: $iv";
        $smsmsg = urlencode($smsmsg);
        $curlres = sendMessage($smsmsg, $recepient_phone);

        echo json_encode($curlres);
    }

    //Decrypt message if API request received
    if(isset($_POST['encmessage'])) {
        $encmsg = $_POST['encmessage'];
        $dkey = $_POST['dkey'];
        $ivcode = $_POST['ivcode'];
        $dec = threeDesDecrypt($encmsg, $dkey, $ivcode);
        echo json_encode($dec);
    }
}


/********************************** */
//Send email function
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

//Encryption function
function encryptMessage($msg, $key, $iv) {
    $cipher = "AES-128-CTR";
    $enc = openssl_encrypt($msg, $cipher, $key, $options=0, $iv);
    return $enc;
}

//Decryption function
function decryptMessage($cipher, $key, $iv) {
    $cipherAlgo = "AES-128-CTR";
    $msg = openssl_decrypt($cipher, $cipherAlgo, $key, $options=0, $iv);
    return $msg;
}

//3DES encryption algo (encrypts thrice)
function threeDesEncrypt($msg, $key, $iv) {
    $cipher1 = encryptMessage($msg, $key, $iv);
    $cipher2 = encryptMessage($cipher1, $key, $iv);
    $cipher3 = encryptMessage($cipher2, $key, $iv);
    return $cipher3;
}

//3DES decryption algo
function threeDesDecrypt($msg, $key, $iv) {
    $dec1 = decryptMessage($msg, $key, $iv);
    $dec2 = decryptMessage($dec1, $key, $iv);
    $dec3 = decryptMessage($dec2, $key, $iv);
    return $dec3;
}

/***************************** */
//send sms function
function sendMessage($msg, $phone) {

    $url = "https://api.africastalking.com/version1/messaging";

    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_HEADER, false);
    curl_setopt($curl, CURLOPT_HTTPHEADER, array(
        "Accept: application/json",
        "Content-Type: application/x-www-form-urlencoded",
        "apiKey: 67215f1ecaae12825f69a2046a342ad6d30aed7a11c7e903a1754ceb60128858"
    ));
    curl_setopt($curl, CURLOPT_POSTFIELDS, "username=deskirubi&to=$phone&message=$msg");
    curl_setopt($curl, CURLOPT_POST, true);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    
    $curl_response = curl_exec($curl);

    $arr = json_decode($curl_response, true);
    return $arr;

}
