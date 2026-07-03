<?php
// app/Services/Mailer.php
namespace App\Services;

/**
 * Simple mailer — no Composer dependency.
 *
 * In development (APP_ENV = 'development'):
 *   Emails are written to /app/logs/mail.log instead of sent.
 *   This means registration works on localhost without any SMTP setup.
 *
 * In production:
 *   Configure the SMTP constants below and set USE_SMTP = true.
 *   Or swap this class for a proper library (PHPMailer, Symfony Mailer)
 *   once you have Composer set up.
 */
class Mailer
{
    // ── Config ────────────────────────────────────────────
    // Set these when you have real SMTP credentials.
    private const FROM_EMAIL = 'noreply@thegentlemensplace.eu';
    private const FROM_NAME  = "The Gentleman's Place";

    // ── Public send methods ───────────────────────────────

    public static function sendEmailVerification(string $to, string $name, string $token): void
    {
        $link    = BASE_URL . BASE_PATH . '/verify-email/' . $token;
        $subject = 'Verify your email — The Gentleman\'s Place';
        $body    = self::renderTemplate('verification', [
            'name' => $name,
            'link' => $link
        ]);

        self::send($to, $name, $subject, $body);
    }

    public static function sendPasswordReset(string $to, string $name, string $token): void
    {
        $link    = BASE_URL . BASE_PATH . '/reset-password/' . $token;
        $subject = 'Reset your password — The Gentleman\'s Place';
        $body    = self::renderTemplate('reset', [
            'name' => $name,
            'link' => $link
        ]);

        self::send($to, $name, $subject, $body);
    }

    public static function sendCallConnectionLink(
        string $to,
        string $name,
        string $performerName,
        string $token,
        int    $expMins
    ): void {
        $link    = BASE_URL . BASE_PATH . '/call/connect/' . $token;
        $subject = "Your connection link to {$performerName} — valid for {$expMins} minutes";
        $body    = self::renderTemplate('call-link', [
            'name'          => $name,
            'performerName' => $performerName,
            'link'          => $link,
            'expMins'       => $expMins
        ]);

        self::send($to, $name, $subject, $body);
    }

    // ── Core send ─────────────────────────────────────────

    private static function send(string $to, string $name, string $subject, string $htmlBody): void
    {
        // In development, write to log file instead of sending
        if (defined('APP_ENV') && APP_ENV === 'development') {
            self::logEmail($to, $subject, $htmlBody);
            return;
        }

        // Production: use PHP mail() — replace with SMTP library for reliability
        $headers  = "MIME-Version: 1.0\r\n";
        $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
        $headers .= "From: " . self::FROM_NAME . " <" . self::FROM_EMAIL . ">\r\n";
        $headers .= "Reply-To: " . self::FROM_EMAIL . "\r\n";
        $headers .= "X-Mailer: PHP/" . phpversion();

        $sent = mail($to, $subject, $htmlBody, $headers);

        if (!$sent) {
            error_log("[Mailer] mail() failed for {$to} — subject: {$subject}");
            throw new \RuntimeException("Failed to send email to {$to}");
        }
    }

    // ── Dev log ───────────────────────────────────────────

    private static function logEmail(string $to, string $subject, string $body): void
    {
        $logDir  = APP_ROOT . '/logs';
        $logFile = $logDir . '/mail.log';

        if (!is_dir($logDir)) {
            mkdir($logDir, 0755, true);
        }

        $entry = implode("\n", [
            str_repeat('─', 60),
            'TIME:    ' . date('Y-m-d H:i:s'),
            'TO:      ' . $to,
            'SUBJECT: ' . $subject,
            'BODY:',
            strip_tags($body),
            '',
        ]);

        file_put_contents($logFile, $entry, FILE_APPEND | LOCK_EX);
    }

    private static function renderTemplate(string $template, array $vars): string
    {
        extract($vars);
        ob_start();
        require APP_ROOT . '/Views/emails/' . $template . '.php';
        return ob_get_clean();
    }
}
