// app/Cron/billing_watchdog.php
<?php
// Finds calls that have been "in_progress" for more than expected duration
// and haven't received a billing webhook — failsafe deduction
require_once __DIR__ . '/../Config/config.php';
// ... billing logic