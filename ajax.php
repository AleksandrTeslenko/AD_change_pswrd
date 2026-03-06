<?php

ini_set('display_errors', 'stderr');
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include "autoload.php";

header("Content-Type: application/json");

// set_error_handler("customErrorHandler");
// register_shutdown_function("shutdownHandler");

$data = json_decode(file_get_contents("php://input"), true);
$ldap_user = $data['login'] ?? '';
$ldap_pass = $data['pswrd'] ?? '';
$ldap_pass_new = $data['new-pswrd'] ?? '';

// echo json_encode(["success" => true, "message" => "OK"]); exit;
echo json_encode(["success" => false, "message" => "Could not connect to LDAP server"]); exit;

$ldap_server = AD_SERVER;
$ldap_dn = AD_BASE_DN;

putenv('LDAPTLS_REQCERT=never');

$ldapconn = ldap_connect($ldap_server);
if (!$ldapconn) {
    echo json_encode(["success" => false, "message" => "Could not connect to LDAP server: " . $ldap_server]);
    exit;
}

ldap_set_option($ldapconn, LDAP_OPT_PROTOCOL_VERSION, 3);
ldap_set_option($ldapconn, LDAP_OPT_REFERRALS, 0);
// ldap_start_tls($ldapconn);

if (!ldap_bind($ldapconn, $ldap_user, $ldap_pass)) {
    echo json_encode(["success" => false, "message" => "AD authorization error: " . $ldap_user]);
    exit;
}

$ldap_pass_new = "\"$ldap_pass_new\"";
$ldap_pass_new = mb_convert_encoding($ldap_pass_new, "UTF-16LE");
$entry = ["unicodePwd" => $ldap_pass_new];

$dn = 'OU=LDAP_Users,DC=srv,DC=vvtrack,DC=com';

// var_dump($ldap_user, $dn, $entry);

if (ldap_mod_replace($ldapconn, $dn, $entry)) {
    echo json_encode(["success" => true, "message" => "Password changed successfully."]);
} else {
    echo json_encode(["success" => false, "message" => "Password change error: " . ldap_error($ldapconn)]);
}

ldap_unbind($ldapconn);


function customErrorHandler($errno, $errstr, $errfile, $errline)
{
    if ($errno == E_WARNING) {
        echo "</br>Warning: $errstr in $errfile on line $errline\n";
        exit;
    }
    return false;
}

function shutdownHandler()
{
    $error = error_get_last();
    if ($error && ($error['type'] == E_ERROR || $error['type'] == E_PARSE)) {
        echo "</br>Fatal error: {$error['message']} in {$error['file']} on line {$error['line']}, type {$error['type']}\n";
        exit;
    }
}
