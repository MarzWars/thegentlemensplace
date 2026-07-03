<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
</head>
<body style="margin:0; padding:0; background:#0a0805; font-family:Georgia,serif;">
  <table width="100%" cellpadding="0" cellspacing="0" style="background:#0a0805; padding:40px 20px;">
    <tr><td align="center">
      <table width="560" cellpadding="0" cellspacing="0"
             style="background:#111008; border:1px solid rgba(201,168,76,.2); max-width:560px; width:100%;">
        <tr>
          <td style="padding:32px; text-align:center; border-bottom:1px solid rgba(201,168,76,.1);">
            <div style="font-family:Georgia,serif; font-size:28px; font-weight:700; color:#c9a84c;">GC</div>
            <div style="font-family:Georgia,serif; font-size:13px; color:#9a7a30; letter-spacing:3px; margin-top:4px;">
              THE GENTLEMAN'S PLACE
            </div>
          </td>
        </tr>
        <tr>
          <td style="padding:36px 40px;">
            <h1 style="font-family:Georgia,serif; font-size:22px; color:#f0e8d0; margin:0 0 16px;">
              Verify Your Email Address
            </h1>
            <p style="font-size:15px; color:#c4b896; line-height:1.7; margin:0 0 24px;">
              Hello <strong><?= htmlspecialchars($name) ?></strong>,<br><br>
              Thank you for registering. Click the button below to verify your email address.
              This link expires in 24 hours.
            </p>
            <div style="text-align:center; margin:2rem 0;">
              <a href="<?= htmlspecialchars($link) ?>"
                 style="background:#c9a84c; color:#0a0805; padding:14px 32px;
                        text-decoration:none; font-family:sans-serif; font-size:14px;
                        font-weight:600; letter-spacing:1px; display:inline-block;">
                Verify Email Address
              </a>
            </div>
            <p style="text-align:center; font-size:12px; color:#888;">
              Or copy this link: <a href="<?= htmlspecialchars($link) ?>" style="color:#c9a84c;"><?= htmlspecialchars($link) ?></a>
            </p>
          </td>
        </tr>
        <tr>
          <td style="padding:20px 40px; border-top:1px solid rgba(201,168,76,.1); text-align:center;">
            <p style="font-size:11px; color:#555; margin:0;">
              © <?= date('Y') ?> The Gentleman's Place &nbsp;·&nbsp;
              <a href="<?= BASE_URL . BASE_PATH ?>/privacy" style="color:#9a7a30;">Privacy Policy</a>
            </p>
          </td>
        </tr>
      </table>
    </td></tr>
  </table>
</body>
</html>
