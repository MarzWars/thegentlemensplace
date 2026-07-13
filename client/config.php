<?php
// client/config.php
session_start();

$localConfig = __DIR__ . '/../app/Config/config.php';
$localDb     = __DIR__ . '/../app/Config/database.php';
$localUser   = __DIR__ . '/../app/Models/user.php';
$localCurrency = __DIR__ . '/../app/Services/currencyservice.php';
$localPayfast  = __DIR__ . '/../app/Services/payfastservice.php';

$remoteConfig = '/var/www/www-root/data/www/thegentlemensplace.eu/app/Config/config.php';
$remoteDb     = '/var/www/www-root/data/www/thegentlemensplace.eu/app/Config/database.php';
$remoteUser   = '/var/www/www-root/data/www/thegentlemensplace.eu/app/Models/user.php';
$remoteCurrency = '/var/www/www-root/data/www/thegentlemensplace.eu/app/Services/currencyservice.php';
$remotePayfast  = '/var/www/www-root/data/www/thegentlemensplace.eu/app/Services/payfastservice.php';

if (file_exists($localConfig)) {
    require_once $localConfig;
    require_once $localDb;
    require_once $localUser;
    if (file_exists($localCurrency)) require_once $localCurrency;
    if (file_exists($localPayfast)) require_once $localPayfast;
} elseif (file_exists($remoteConfig)) {
    require_once $remoteConfig;
    require_once $remoteDb;
    require_once $remoteUser;
    if (file_exists($remoteCurrency)) require_once $remoteCurrency;
    if (file_exists($remotePayfast)) require_once $remotePayfast;
} else {
    die("Configuration not found. Please ensure this is mapped correctly to the main domain's app folder.");
}

function getDb() {
    return \App\Config\Database::getInstance();
}

function isLoggedIn() {
    return !empty($_SESSION['user_id']) && !empty($_SESSION['performer_id']);
}
