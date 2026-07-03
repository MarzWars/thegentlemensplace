# Directory Structure

```text
.
├── .git/ (contents omitted)
├── Assets/
│   ├── css/
│   │   ├── admin.css
│   │   └── main.css
│   ├── img/
│   │   └── og-default.jpg
│   └── js/
│       └── main.js
├── LICENSE
├── README.md
├── app/
│   ├── Config/
│   │   ├── config.php
│   │   └── database.php
│   ├── Controllers/
│   │   ├── admincontroller.php
│   │   ├── authcontroller.php
│   │   ├── billingcontroller.php
│   │   ├── callcontroller.php
│   │   ├── creditcontroller.php
│   │   ├── homecontroller.php
│   │   ├── paymentcontroller.php
│   │   ├── performercontroller.php
│   │   ├── performerdashcontroller.php
│   │   ├── profilecontroller.php
│   │   ├── sitemapcontroller.php
│   │   └── streamcontroller.php
│   ├── Core/
│   │   ├── controller.php
│   │   ├── csrf.php
│   │   ├── lang.php
│   │   ├── middleware.php
│   │   ├── model.php
│   │   ├── ratelimit.php
│   │   ├── router.php
│   │   ├── validator.php
│   │   └── view.php
│   ├── Cron/
│   │   ├── billing_watchdog.php
│   │   └── update_rates.php
│   ├── Lang/
│   │   ├── de.php
│   │   ├── en.php
│   │   ├── es.php
│   │   ├── fr.php
│   │   ├── it.php
│   │   ├── nl.php
│   │   ├── pl.php
│   │   └── pt.php
│   ├── Models/
│   │   ├── call.php
│   │   ├── calllink.php
│   │   ├── performer.php
│   │   ├── stream.php
│   │   └── user.php
│   ├── Services/
│   │   ├── creditservice.php
│   │   ├── currencyservice.php
│   │   ├── fileupload.php
│   │   ├── livekitservice.php
│   │   ├── mailer.php
│   │   ├── payfastservice.php
│   │   ├── pusherservice.php
│   │   └── telephonyservice.php
│   ├── Views/
│   │   ├── account/
│   │   │   └── index.php
│   │   ├── admin/
│   │   │   ├── admin-call.php
│   │   │   ├── admins.php
│   │   │   ├── calls.php
│   │   │   ├── dashboard.php
│   │   │   ├── login.php
│   │   │   ├── payouts.php
│   │   │   ├── performers.php
│   │   │   ├── settings.php
│   │   │   ├── transactions.php
│   │   │   └── users.php
│   │   ├── auth/
│   │   │   ├── forgot.php
│   │   │   ├── login.php
│   │   │   ├── register.php
│   │   │   └── reset.php
│   │   ├── calls/
│   │   │   ├── calling.php
│   │   │   └── room.php
│   │   ├── credits/
│   │   │   ├── cancelled.php
│   │   │   ├── confirm.php
│   │   │   ├── history.php
│   │   │   ├── packages.php
│   │   │   └── success.php
│   │   ├── emails/
│   │   │   ├── call-link.php
│   │   │   ├── reset.php
│   │   │   └── verification.php
│   │   ├── errors/
│   │   │   ├── 404.php
│   │   │   └── 503.php
│   │   ├── home/
│   │   │   └── index.php
│   │   ├── layouts/
│   │   │   ├── admin.php
│   │   │   ├── admin_auth.php
│   │   │   ├── home.php
│   │   │   └── main.php
│   │   ├── legal/
│   │   │   ├── 2257.php
│   │   │   ├── privacy.php
│   │   │   └── terms.php
│   │   ├── partials/
│   │   │   ├── cookie-banner.php
│   │   │   └── footer.php
│   │   ├── performer-dash/
│   │   │   └── dashboard.php
│   │   └── performers/
│   │       ├── index.php
│   │       └── profile.php
│   ├── htaccess
│   └── logs/ (contents omitted)
├── htaccess
├── index.php
├── robots.txt
├── rta_logo.svg
├── service-worker.js
└── uploads/
    ├── htaccess
    └── performers/ (contents omitted)
```
