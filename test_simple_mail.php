<?php
$to = 'khatikanuj914@gmail.com';
$subject = 'Test Email from PHP mail()';
$message = 'This is a test email sent at ' . date('Y-m-d H:i:s');
$headers = 'From: bluecartsexporters@gmail.com' . "\r\n" .
    'Reply-To: bluecartsexporters@gmail.com' . "\r\n" .
    'X-Mailer: PHP/' . phpversion();

if(mail($to, $subject, $message, $headers)) {
    echo "Test email sent successfully using mail()\n";
} else {
    echo "Failed to send test email using mail()\n";
    error_log("PHP mail() failed to send email");
} 