<?php
/* Newsletter sign-up (legacy endpoint). Validates the address before emailing the team. */
if (!isset($_SERVER['REQUEST_METHOD']) || $_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.html', true, 303);
    exit;
}

$email = isset($_POST['email']) && is_string($_POST['email']) ? trim($_POST['email']) : '';
$hp    = isset($_POST['website']) ? trim((string) $_POST['website']) : '';

if ($hp === '' && filter_var($email, FILTER_VALIDATE_EMAIL) && strlen($email) <= 120) {
    $from    = 'info@agsconsultant.com.pk';
    $headers = 'From: AGS Website <' . $from . ">\r\nContent-Type: text/plain; charset=UTF-8";
    @mail('info@agsconsultant.com.pk', 'Newsletter sign-up', 'Email: ' . $email, $headers, '-f' . $from);
}

header('Location: index.html', true, 303);
exit;
