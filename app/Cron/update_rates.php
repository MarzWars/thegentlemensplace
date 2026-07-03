<?php
// app/Cron/update_rates.php
// Run daily via cron: 0 6 * * * php /path/to/public_html/app/Cron/update_rates.php

define('APP_ROOT',   dirname(__DIR__));
define('PUBLIC_PATH', dirname(APP_ROOT));
define('BASE_URL',   'https://thegentlemensplace.eu');
define('BASE_PATH',  '');

require_once APP_ROOT . '/Config/config.php';
require_once APP_ROOT . '/Config/database.php';
require_once APP_ROOT . '/Services/currencyservice.php';

$success = \App\Services\CurrencyService::fetchAndUpdateLiveRates();
echo $success ? "Rates updated successfully.\n" : "Rate update failed — check error log.\n";
