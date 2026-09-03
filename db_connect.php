<?php
// Database connection. Credentials come from environment variables or gitignored config.local.php.

global $mysqli;
global $db_connection_success;
$mysqli = null;
$db_connection_success = false;

if (is_file(__DIR__ . '/config.local.php')) {
    require_once __DIR__ . '/config.local.php';
}

$db_host = getenv('DB_SERVER') ?: getenv('DB_SERVER_AZURE') ?: '';
$db_user = getenv('DB_USERNAME') ?: getenv('DB_USERNAME_AZURE') ?: '';
$db_pass = getenv('DB_PASSWORD') ?: getenv('DB_PASSWORD_AZURE') ?: '';
$db_name = getenv('DB_NAME') ?: getenv('DB_NAME_AZURE') ?: '';

if ($db_host === '' || $db_user === '' || $db_pass === '' || $db_name === '') {
    error_log('EmailPOS database configuration missing. Copy config.local.example.php to config.local.php or set DB_* environment variables.');
    return;
}

$port = 3306;
$socket = null;

$mysqli = mysqli_init();
if (!$mysqli) {
    error_log('FATAL ERROR: Could not initialize mysqli object.');
    return;
}

$use_ssl = strpos($db_host, '.mysql.database.azure.com') !== false;
if ($use_ssl) {
    $ssl_ca = __DIR__ . '/certs/DigiCertGlobalRootG2.crt.pem';
    if (is_file($ssl_ca)) {
        mysqli_ssl_set($mysqli, null, null, $ssl_ca, null, null);
        $mysqli->options(MYSQLI_OPT_SSL_VERIFY_SERVER_CERT, true);
    } else {
        $mysqli->options(MYSQLI_OPT_SSL_VERIFY_SERVER_CERT, false);
        error_log('WARNING: Azure MySQL CA certificate not found; SSL verification disabled.');
    }
}

$connect_flags = $use_ssl ? MYSQLI_CLIENT_SSL : 0;

if (!@mysqli_real_connect($mysqli, $db_host, $db_user, $db_pass, $db_name, $port, $socket, $connect_flags)) {
    error_log('Database connection failed to ' . $db_host . ' for user ' . $db_user . ': ' . mysqli_connect_error());
    $mysqli = null;
    return;
}

if (!$mysqli->set_charset('utf8mb4')) {
    error_log('Error loading character set utf8mb4: ' . $mysqli->error);
    $mysqli->close();
    $mysqli = null;
    return;
}

$db_connection_success = true;
