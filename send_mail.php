<?php

$filename = 'current_allocation.csv';
$export_allocation = unserialize($_POST['export_allocation']);

// File creation
$file = fopen($filename, "w");

foreach ($export_allocation as $line) {
    fputcsv($file, $line);
}

fclose($file);

// Import PHPMailer classes into the global namespace 
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Include PHPMailer library files 
require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';

// Create an instance of PHPMailer class 
$mail = new PHPMailer;
$mail->isSMTP();
$mail->Host     = 'smtp.gmail.com';
$mail->SMTPAuth = true;
$mail->Username = 'YOUR_GMAIL_ADDRESS_HERE';
$mail->Password = ' YOUR_GMAIL_PASSWORD_HERE';
$mail->SMTPSecure = 'tls';
$mail->Port     = 587;

// Sender info 
$mail->setFrom('YOUR_GMAIL_ADDRESS_HERE', 'YOUR_NAME_HERE');

// Add attachment
$mail->addAttachment('current_allocation.csv');

// Email subject 
$mail->Subject = 'TA Allocation';

// Set email format to HTML 
$mail->isHTML(true);

// Add a recipient
$allemails = $_POST['export_emails'];
$allnames = $_POST['export_names'];
$emails = explode (", ", $allemails); 
array_pop($emails);
$names = explode (", ", $allnames); 
array_pop($names);

$i = 0;
foreach($emails as $val)
{
    // Email body content 
    $mailContent = "<h2>Hi ". $names[$i] . ', you have been allocated a subject.</h2> 
    <p>Check attached CSV file for more information.</p>';
    $mail->Body = $mailContent;
    
    $mail->addAddress($val);
    // Send email 
    if (!$mail->send()) {
        die('Messages could not be sent. Error: ' . $mail->ErrorInfo);
    }
    $i += 1;
}

unlink($filename);

echo ("<script>
    window.alert('Messages have been successfully sent.');
    window.location.href='index.php';
    </script>");

?>