<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'admin/config/phpmailer/PHPMailer.php';
require 'admin/config/phpmailer/SMTP.php';
require 'admin/config/phpmailer/Exception.php';

try {
    $mail = new PHPMailer(true);
    
    //Server settings
    $mail->isSMTP();
    $mail->Host       = 'smtp.gmail.com';
    $mail->SMTPAuth   = true;
    $mail->Username   = 'bluecartsexporters@gmail.com';
    $mail->Password   = 'jeux dubg roxr aogr';
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port       = 587;

    //Recipients
    $mail->setFrom('bluecartsexporters@gmail.com', 'Blue Cart');
    $mail->addAddress('khatikanuj914@gmail.com');

    //Content
    $mail->isHTML(true);
    $mail->Subject = 'Test Email - ' . date('Y-m-d H:i:s');
    $mail->Body    = 'This is a test email sent at: ' . date('Y-m-d H:i:s');

    $mail->send();
    echo "Message sent successfully!";
} catch (Exception $e) {
    echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
} 