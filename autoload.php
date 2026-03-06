<?php

require_once(dirname(__FILE__) . "/vendor/autoload.php");

$baseDir = dirname(__FILE__) . "/";
$dotenv = Dotenv\Dotenv::createImmutable($baseDir);
if (!file_exists($baseDir . '.env')) throw new \Exception("Unable to read any of the environment file(s) (.env)", 400);
$dotenv->load();
$dotenv->required(['AD_BASE_DN', 'AD_SERVER', 'AD_USER', 'AD_PSWD']);


define('AD_BASE_DN', (checkENV('AD_BASE_DN')) ?: '');  #Базовий dn для пошуку
define('AD_SERVER', (checkENV('AD_SERVER')) ?: false); #LDAP URI of the form ldap://hostname:port or ldaps://hostname:port for SSL encryption.
define('AD_USER', (checkENV('AD_USER')) ?: '');
define('AD_PSWD', (checkENV('AD_PSWD')) ?: '');

function checkENV($env_name){
	return (getenv($env_name) && getenv($env_name) != '') ? getenv($env_name) : false;
}