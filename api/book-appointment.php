<?php
declare(strict_types=1);

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    header('Allow: POST');
    exit('Method not allowed');
}

if ((int) ($_SERVER['CONTENT_LENGTH'] ?? 0) > 30000) {
    http_response_code(413);
    exit('Request too large');
}

require __DIR__ . '/config.php';

function redirectTo(string $page): void
{
    header('Location: ../' . $page, true, 303);
    exit;
}

function field(string $key, int $maxLength, bool $allowLineBreaks = false): string
{
    $value = trim((string) ($_POST[$key] ?? ''));
    $value = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/u', '', $value) ?? '';
    if (!$allowLineBreaks) {
        $value = preg_replace('/[\r\n]+/', ' ', $value) ?? '';
    }
    return function_exists('mb_substr') ? mb_substr($value, 0, $maxLength) : substr($value, 0, $maxLength);
}

function safe(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

if (field('website', 200) !== '') {
    redirectTo('thank-you.html');
}

$fullName = field('full_name', 80);
$email = field('email', 120);
$phone = field('phone', 30);
$company = field('company', 100);
$service = field('service', 80);
$meetingDate = field('meeting_date', 10);
$meetingTime = field('meeting_time', 20);
$meetingMode = field('meeting_mode', 30);
$message = field('message', 1500, true);
$consent = field('consent', 10);

$allowedServices = [
    'BDX & ecosystem onboarding',
    'Wallet & custody guidance',
    'Privacy dApp adoption',
    'Masternode readiness',
    'Business & technical strategy',
    'Team workshop',
    'Other',
];
$allowedTimes = ['09:00 GST', '10:30 GST', '12:00 GST', '14:00 GST', '15:30 GST', '17:00 GST'];
$allowedModes = ['Video call', 'Dubai office', 'Phone call'];

$date = DateTimeImmutable::createFromFormat('!Y-m-d', $meetingDate, new DateTimeZone('Asia/Dubai'));
$today = new DateTimeImmutable('today', new DateTimeZone('Asia/Dubai'));
$latestDate = $today->modify('+180 days');
$validDate = $date instanceof DateTimeImmutable
    && $date->format('Y-m-d') === $meetingDate
    && $date > $today
    && $date <= $latestDate
    && (int) $date->format('N') <= 5;

$isValid = $fullName !== ''
    && filter_var($email, FILTER_VALIDATE_EMAIL) !== false
    && in_array($service, $allowedServices, true)
    && in_array($meetingTime, $allowedTimes, true)
    && in_array($meetingMode, $allowedModes, true)
    && $message !== ''
    && $consent === 'yes'
    && $validDate;

if (!$isValid) {
    redirectTo('booking-error.html');
}

$requestId = strtoupper(bin2hex(random_bytes(4)));
$submittedAt = new DateTimeImmutable('now', new DateTimeZone('Asia/Dubai'));
$formattedDate = $date->format('l, d F Y');
$subject = 'New consultation request: ' . $service . ' [' . $requestId . ']';

$rows = [
    'Request ID' => $requestId,
    'Name' => $fullName,
    'Email' => $email,
    'Phone / WhatsApp' => $phone !== '' ? $phone : 'Not provided',
    'Company' => $company !== '' ? $company : 'Not provided',
    'Consultation topic' => $service,
    'Preferred date' => $formattedDate,
    'Preferred time' => $meetingTime,
    'Meeting preference' => $meetingMode,
    'Submitted' => $submittedAt->format('d M Y, H:i T'),
];

$htmlRows = '';
$plainRows = '';
foreach ($rows as $label => $value) {
    $htmlRows .= '<tr><td style="padding:10px 14px;border-bottom:1px solid #e6e6e6;color:#66727d;font-size:12px;width:180px">' . safe($label) . '</td><td style="padding:10px 14px;border-bottom:1px solid #e6e6e6;color:#07121f;font-size:12px;font-weight:600">' . safe($value) . '</td></tr>';
    $plainRows .= $label . ': ' . $value . PHP_EOL;
}

$htmlBody = '<!doctype html><html><body style="margin:0;background:#f4f1e9;font-family:Arial,sans-serif;color:#07121f"><div style="max-width:680px;margin:30px auto;background:#ffffff;border-radius:18px;overflow:hidden"><div style="padding:26px 30px;background:#07121f;color:#ffffff"><div style="font-size:12px;color:#f0bd65;letter-spacing:1.5px;text-transform:uppercase">BN Technologies</div><h1 style="margin:8px 0 0;font-size:24px">New consultation request</h1></div><div style="padding:28px 30px"><table style="width:100%;border-collapse:collapse">' . $htmlRows . '</table><div style="margin-top:24px;padding:18px;background:#f6f3ec;border-radius:12px"><div style="margin-bottom:8px;color:#66727d;font-size:11px;text-transform:uppercase;letter-spacing:1px">Client goals</div><div style="font-size:13px;line-height:1.7;white-space:pre-wrap">' . safe($message) . '</div></div><p style="margin:24px 0 0;color:#66727d;font-size:11px">Reply directly to this email to contact ' . safe($fullName) . '.</p></div></div></body></html>';
$plainBody = "NEW CONSULTATION REQUEST\n\n" . $plainRows . "\nClient goals:\n" . $message . "\n";

$boundary = 'bn_' . bin2hex(random_bytes(12));
$headers = [
    'MIME-Version: 1.0',
    'Content-Type: multipart/alternative; boundary="' . $boundary . '"',
    'From: ' . $siteName . ' Website <' . $bookingSender . '>',
    'Reply-To: ' . $fullName . ' <' . $email . '>',
    'X-Mailer: PHP/' . PHP_VERSION,
];

$body = '--' . $boundary . "\r\n"
    . "Content-Type: text/plain; charset=UTF-8\r\n"
    . "Content-Transfer-Encoding: 8bit\r\n\r\n"
    . $plainBody . "\r\n"
    . '--' . $boundary . "\r\n"
    . "Content-Type: text/html; charset=UTF-8\r\n"
    . "Content-Transfer-Encoding: 8bit\r\n\r\n"
    . $htmlBody . "\r\n"
    . '--' . $boundary . "--\r\n";

$encodedSubject = '=?UTF-8?B?' . base64_encode($subject) . '?=';
$sent = mail($bookingRecipient, $encodedSubject, $body, implode("\r\n", $headers));

if (!$sent) {
    error_log('BN Technologies booking email failed for request ' . $requestId);
    redirectTo('booking-error.html');
}

redirectTo('thank-you.html');
