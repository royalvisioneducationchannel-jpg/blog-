<?php
/**
 * AGS Consultant — inquiry form handler.
 * Used by the homepage counselling form and the Apply Online page.
 * Compatible with PHP 7.0+ and PHP 8.x.
 */

$to       = 'aatika@agsconsultant.com.pk';
$from     = 'info@agsconsultant.com.pk';
$redirect = '/thank-you';

if (!function_exists('mb_substr')) {
    function mb_substr($str, $start, $length = null, $encoding = null)
    {
        return substr($str, $start, $length);
    }
}

$wantsJson = (isset($_SERVER['HTTP_ACCEPT']) && strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false)
    || (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'fetch');

function ags_respond($ok, $message, $wantsJson, $redirect)
{
    if ($wantsJson) {
        header('Content-Type: application/json; charset=utf-8');
        http_response_code($ok ? 200 : 422);
        echo json_encode(array('ok' => $ok, 'message' => $message));
    } elseif ($ok) {
        header('Location: ' . $redirect, true, 303);
    } else {
        http_response_code(422);
        header('Content-Type: text/html; charset=utf-8');
        echo '<!DOCTYPE html><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">'
            . '<title>Form error | AGS Consultant</title>'
            . '<p style="font:16px/1.6 system-ui,sans-serif;max-width:560px;margin:15vh auto;padding:0 20px">'
            . htmlspecialchars($message, ENT_QUOTES, 'UTF-8')
            . '<br><br><a href="javascript:history.back()">&larr; Go back</a></p>';
    }
    exit;
}

/* Clean a single-line value: trim, strip tags/control chars, cap length. */
function ags_clean($key, $max = 200)
{
    if (!isset($_POST[$key]) || !is_string($_POST[$key])) {
        return '';
    }
    $v = trim(strip_tags($_POST[$key]));
    $v = preg_replace('/[\x00-\x1F\x7F]+/u', ' ', $v);
    if ($v === null) {
        return ''; // invalid UTF-8
    }
    return mb_substr($v, 0, $max, 'UTF-8');
}

if (!isset($_SERVER['REQUEST_METHOD']) || $_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: /inquiry', true, 303);
    exit;
}

/* Spam protection: honeypot + minimum fill time. Bots get a silent "success". */
$honeypot = isset($_POST['website']) ? trim((string) $_POST['website']) : '';
$started  = isset($_POST['_ts']) ? (int) $_POST['_ts'] : 0;
if ($honeypot !== '' || ($started > 0 && (time() - $started) < 3)) {
    ags_respond(true, 'Thank you! Our team will contact you shortly.', $wantsJson, $redirect);
}

$name  = ags_clean('name', 100);
$email = ags_clean('email', 120);
$phone = ags_clean('phone', 20);

if ($name === '' || $email === '' || $phone === '') {
    ags_respond(false, 'Please fill in your name, email and phone number.', $wantsJson, $redirect);
}
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    ags_respond(false, 'Please enter a valid email address.', $wantsJson, $redirect);
}
if (!preg_match('/^[0-9+\-\s()]{7,20}$/', $phone)) {
    ags_respond(false, 'Please enter a valid phone number.', $wantsJson, $redirect);
}

/* Optional fields (only those present are included in the email). */
$optional = array(
    'destination'        => 'Preferred destination',
    'country'            => 'Preferred destination',
    'study_level'        => 'Study level',
    'education'          => 'Current education',
    'last_qualification' => 'Last qualification / marks',
    'budget'             => 'Estimated budget',
    'intake'             => 'Preferred intake',
    'service'            => 'Service interested in',
    'city'               => 'City',
    'english_test'       => 'English test / score',
    'subject'            => 'Subject',
);

$lines   = array();
$lines[] = 'Name: ' . $name;
$lines[] = 'Email: ' . $email;
$lines[] = 'Phone / WhatsApp: ' . $phone;
foreach ($optional as $key => $label) {
    $val = ags_clean($key, 150);
    if ($val !== '') {
        $lines[] = $label . ': ' . $val;
    }
}

$message = isset($_POST['message']) && is_string($_POST['message'])
    ? mb_substr(trim(strip_tags($_POST['message'])), 0, 2000, 'UTF-8')
    : '';
if ($message !== '') {
    $lines[] = '';
    $lines[] = 'Message:';
    $lines[] = $message;
}

$source  = ags_clean('form', 80);
$lines[] = '';
$lines[] = '---';
$lines[] = 'Form: ' . ($source !== '' ? $source : 'Website inquiry');
$lines[] = 'Sent: ' . date('Y-m-d H:i');
$lines[] = 'IP: ' . (isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : 'unknown');

$subjectText = 'New website inquiry: ' . $name;
$subject     = '=?UTF-8?B?' . base64_encode($subjectText) . '?=';

/* Name and email are already stripped of CR/LF, so headers cannot be injected. */
$headers   = array();
$headers[] = 'From: AGS Website <' . $from . '>';
$headers[] = 'Reply-To: =?UTF-8?B?' . base64_encode($name) . '?= <' . $email . '>';
$headers[] = 'MIME-Version: 1.0';
$headers[] = 'Content-Type: text/plain; charset=UTF-8';
$headers[] = 'X-Mailer: PHP/' . phpversion();

$sent = @mail($to, $subject, implode("\r\n", $lines), implode("\r\n", $headers), '-f' . $from);

if ($sent) {
    ags_respond(true, 'Thank you! Your request has been received. Our team will contact you shortly.', $wantsJson, $redirect);
}
ags_respond(false, 'Sorry, we could not send your request right now. Please call or WhatsApp us on +92 300 1959995.', $wantsJson, $redirect);
